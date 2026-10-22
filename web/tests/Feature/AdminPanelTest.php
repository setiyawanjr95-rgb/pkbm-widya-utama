<?php

namespace Tests\Feature;

use App\Filament\Resources\Assignments\Pages\EditAssignment;
use App\Filament\Resources\Assignments\RelationManagers\SubmissionsRelationManager;
use App\Filament\Resources\LearningMaterials\Pages\CreateLearningMaterial;
use App\Filament\Resources\Schedules\Pages\ManageSchedules;
use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\LearningMaterial;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Submission;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('username', 'admin')->first());
    }

    public function test_admin_creates_student_who_can_login_with_nisn(): void
    {
        Livewire::test(CreateStudent::class)
            ->fillForm([
                'name' => 'Budi Santoso', 'nisn' => '9998887776', 'paket' => 'Paket B', 'kelas' => 'Kelas 8',
                'username' => 'budi', 'password' => 'rahasia123', 'active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $student = Student::where('nisn', '9998887776')->first();
        $this->assertSame('Budi Santoso', $student->user->name);
        $this->assertSame('student', $student->user->role);

        $this->postJson('/api/login', ['username' => '9998887776', 'password' => 'rahasia123'])
            ->assertOk()->assertJsonPath('user.kelas', 'Kelas 8');
    }

    public function test_admin_edits_student_without_changing_password(): void
    {
        $student = Student::first();
        $hash = $student->user->password;

        Livewire::test(EditStudent::class, ['record' => $student->getRouteKey()])
            ->assertSchemaStateSet(['name' => 'Siti Aisyah'])
            ->fillForm(['name' => 'Siti A.', 'password' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Siti A.', $student->user->fresh()->name);
        $this->assertSame($hash, $student->user->fresh()->password);
    }

    public function test_admin_creates_quiz_with_questions(): void
    {
        $course = Course::first();

        Livewire::test(CreateLearningMaterial::class)
            ->fillForm([
                'course_id' => $course->id, 'type' => 'quiz', 'title' => 'Kuis Bab 2',
                'questions' => [
                    ['question' => '1 + 1 = ?', 'options' => [['text' => '1'], ['text' => '2'], ['text' => '3']], 'answer' => 1],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $quiz = LearningMaterial::where('title', 'Kuis Bab 2')->first();
        $this->assertSame(['1', '2', '3'], $quiz->questions->first()->options);
        $this->assertSame(1, $quiz->questions->first()->answer);
    }

    public function test_grading_a_submission_creates_grade_and_notification(): void
    {
        $assignment = Assignment::first();
        $student = Student::first();
        $submission = Submission::create([
            'assignment_id' => $assignment->id, 'student_id' => $student->id,
            'file_path' => 'submissions/x.pdf', 'file_name' => 'jawaban.pdf',
        ]);

        Livewire::test(SubmissionsRelationManager::class, ['ownerRecord' => $assignment, 'pageClass' => EditAssignment::class])
            ->callAction(TestAction::make('edit')->table($submission), ['score' => 88, 'feedback' => 'Bagus'])
            ->assertHasNoFormErrors();

        $this->assertSame(88, $submission->fresh()->score);
        $this->assertDatabaseHas('grades', ['student_id' => $student->id, 'category' => 'Tugas', 'score' => 88]);
        $this->assertDatabaseHas('student_notifications', ['student_id' => $student->id, 'type' => 'nilai']);
    }

    public function test_weekly_schedule_repeats(): void
    {
        $before = Schedule::count();

        Livewire::test(ManageSchedules::class)
            ->callAction(CreateAction::class, [
                'course_id' => Course::first()->id, 'date' => '2026-11-02', 'repeat_weeks' => 3,
                'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'Ruang 1',
            ])
            ->assertHasNoFormErrors();

        $this->assertSame($before + 3, Schedule::count());
        $this->assertTrue(Schedule::whereDate('date', '2026-11-16')->exists());
    }

    public function test_student_cannot_open_admin_panel(): void
    {
        $this->actingAs(Student::first()->user)->get('/admin')->assertForbidden();
    }
}
