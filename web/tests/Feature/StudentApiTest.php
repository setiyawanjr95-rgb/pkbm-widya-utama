<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->token = $this->postJson('/api/login', ['username' => '1234567890', 'password' => 'siswa123'])
            ->assertOk()->json('token');
    }

    private function api(string $method, string $uri, array $data = [])
    {
        return $this->withToken($this->token)->json($method, '/api'.$uri, $data);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->postJson('/api/login', ['username' => '1234567890', 'password' => 'salah'])->assertUnprocessable();
    }

    public function test_admin_account_cannot_login_to_app(): void
    {
        $this->postJson('/api/login', ['username' => 'admin', 'password' => 'admin12345'])->assertUnprocessable();
    }

    public function test_data_endpoints_are_wrapped_in_data(): void
    {
        foreach (['/me', '/courses', '/courses/1/materials', '/assignments', '/grades', '/schedules', '/announcements', '/notifications'] as $uri) {
            $this->api('GET', $uri)->assertOk()->assertJsonStructure(['data']);
        }
        $this->api('GET', '/courses')->assertJsonCount(4, 'data')->assertJsonPath('data.0.paket', 'Paket C · Kelas 12');
    }

    public function test_other_group_course_is_hidden(): void
    {
        $other = Course::create(['name' => 'Tematik', 'paket' => 'Paket A', 'kelas' => 'Kelas 3']);
        $this->api('GET', "/courses/{$other->id}/materials")->assertNotFound();
    }

    public function test_quiz_score_goes_to_grades_and_progress(): void
    {
        $this->api('POST', '/materials/3/quiz', ['answers' => [1 => 2, 2 => 2, 3 => 0]])
            ->assertOk()->assertJsonPath('data.score', 67);
        $this->api('GET', '/grades')->assertJsonPath('data.0.category', 'Kuis')->assertJsonPath('data.0.score', 67);
        $math = collect($this->api('GET', '/courses')->json('data'))->firstWhere('name', 'Matematika');
        $this->assertEquals(0.33, $math['progress']);
    }

    public function test_submit_assignment_then_status_changes(): void
    {
        Storage::fake('local');
        $this->withToken($this->token)->post('/api/assignments/1/submit', [
            'file' => UploadedFile::fake()->create('jawaban.pdf', 100, 'application/pdf'),
        ], ['Accept' => 'application/json'])->assertCreated();

        $a = collect($this->api('GET', '/assignments')->json('data'))->firstWhere('id', 1);
        $this->assertSame('dikumpulkan', $a['status']);
        $this->assertSame('jawaban.pdf', $a['submitted_file']);
        Storage::disk('local')->assertExists(Student::first()->submissions()->first()->file_path);
    }

    public function test_notifications_can_be_marked_read(): void
    {
        $this->api('POST', '/notifications/read')->assertOk();
        $this->assertFalse(collect($this->api('GET', '/notifications')->json('data'))->contains('read', false));
    }
}
