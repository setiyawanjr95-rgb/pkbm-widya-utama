<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\LearningMaterial;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Semua endpoint data untuk app Android warga belajar. Response selalu dibungkus "data". */
class StudentController extends Controller
{
    public static function present(Student $s): array
    {
        return [
            'id' => $s->id,
            'name' => $s->user->name,
            'username' => $s->user->username,
            'nisn' => $s->nisn,
            'paket' => $s->paket,
            'kelas' => $s->kelas,
            'email' => $s->user->email ?? '',
            'phone' => $s->phone ?? '',
            'address' => $s->address ?? '',
        ];
    }

    private function student(Request $request): Student
    {
        return $request->user()->student ?? abort(403, 'Khusus akun warga belajar.');
    }

    private function course(Request $request, int $id): Course
    {
        return $this->student($request)->courses()->findOrFail($id);
    }

    private static function fileUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    public function me(Request $request)
    {
        return ['data' => self::present($this->student($request))];
    }

    public function updateProfile(Request $request)
    {
        $student = $this->student($request);
        $data = $request->validate([
            'email' => ['nullable', 'email', 'unique:users,email,'.$student->user_id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);
        $student->user->update(['email' => $data['email'] ?? $student->user->email]);
        $student->update(collect($data)->only(['phone', 'address'])->all());

        return ['data' => self::present($student->fresh('user'))];
    }

    public function courses(Request $request)
    {
        $student = $this->student($request);
        $done = $student->completedMaterials()->pluck('learning_materials.id');

        $courses = $student->courses()->withCount(['materials', 'assignments'])
            ->with('materials:id,course_id')->orderBy('name')->get()
            ->map(fn (Course $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'paket' => "{$c->paket} · {$c->kelas}",
                'tutor' => $c->tutor,
                'description' => $c->description,
                'total_materi' => $c->materials_count,
                'total_tugas' => $c->assignments_count,
                'progress' => $c->materials_count
                    ? round($c->materials->pluck('id')->intersect($done)->count() / $c->materials_count, 2) : 0,
                'finished' => $c->finished,
                'icon' => $c->icon,
                'color' => $c->color,
            ]);

        return ['data' => $courses];
    }

    public function materials(Request $request, int $course)
    {
        $student = $this->student($request);
        $done = $student->completedMaterials()->pluck('learning_materials.id');

        $materials = $this->course($request, $course)->materials()->withCount('questions')->get()
            ->map(fn (LearningMaterial $m) => [
                'id' => $m->id,
                'course_id' => $m->course_id,
                'title' => $m->title,
                'type' => $m->type,
                'meta' => $m->meta(),
                'description' => $m->description ?? '',
                'done' => $done->contains($m->id),
                'attachment_name' => $m->file_name,
                'attachment_size' => $m->file_size ? format_bytes($m->file_size) : null,
                'url' => $m->type === 'video' && $m->url ? $m->url : self::fileUrl($m->file_path),
            ]);

        return ['data' => $materials];
    }

    private function material(Request $request, int $id): LearningMaterial
    {
        $material = LearningMaterial::findOrFail($id);
        $this->course($request, $material->course_id);

        return $material;
    }

    public function completeMaterial(Request $request, int $material)
    {
        $m = $this->material($request, $material);
        $this->student($request)->completedMaterials()->syncWithoutDetaching([$m->id]);

        return ['data' => ['done' => true]];
    }

    public function quiz(Request $request, int $material)
    {
        $m = $this->material($request, $material);
        abort_unless($m->type === 'quiz', 404);

        return ['data' => [
            'id' => $m->id,
            'title' => $m->title,
            'questions' => $m->questions->map(fn ($q) => [
                'id' => $q->id, 'question' => $q->question, 'options' => $q->options,
            ]),
        ]];
    }

    /** Body: answers[<question_id>] = index opsi. Nilai kuis disimpan ke rekap nilai. */
    public function submitQuiz(Request $request, int $material)
    {
        $m = $this->material($request, $material);
        abort_unless($m->type === 'quiz', 404);
        $answers = $request->validate(['answers' => ['required', 'array']])['answers'];

        $questions = $m->questions;
        $correct = $questions->filter(fn ($q) => isset($answers[$q->id]) && (int) $answers[$q->id] === $q->answer)->count();
        $score = $questions->count() ? (int) round($correct / $questions->count() * 100) : 0;

        $student = $this->student($request);
        Grade::updateOrCreate(
            ['student_id' => $student->id, 'source_type' => LearningMaterial::class, 'source_id' => $m->id],
            ['course_id' => $m->course_id, 'category' => 'Kuis', 'title' => $m->title, 'score' => $score],
        );
        $student->completedMaterials()->syncWithoutDetaching([$m->id]);

        return ['data' => [
            'score' => $score,
            'correct' => $correct,
            'total' => $questions->count(),
            'answers' => $questions->mapWithKeys(fn ($q) => [$q->id => $q->answer]),
        ]];
    }

    public function assignments(Request $request)
    {
        $student = $this->student($request);
        $submissions = $student->submissions()->get()->keyBy('assignment_id');

        $assignments = Assignment::whereIn('course_id', $student->courses()->select('id'))
            ->with('course:id,name')->orderByDesc('deadline')->get()
            ->map(function (Assignment $a) use ($submissions) {
                $sub = $submissions->get($a->id);

                return [
                    'id' => $a->id,
                    'course_id' => $a->course_id,
                    'course_name' => $a->course->name,
                    'title' => $a->title,
                    'deadline' => $a->deadline->toIso8601String(),
                    'instruction' => $a->instruction ?? '',
                    'attachment_name' => $a->file_name ?? '',
                    'attachment_size' => format_bytes($a->file_size),
                    'attachment_url' => self::fileUrl($a->file_path),
                    'status' => ! $sub ? 'belum' : ($sub->score === null ? 'dikumpulkan' : 'dinilai'),
                    'submitted_file' => $sub?->file_name,
                    'submitted_at' => $sub?->updated_at?->toIso8601String(),
                    'score' => $sub?->score,
                    'feedback' => $sub?->feedback,
                ];
            });

        return ['data' => $assignments];
    }

    public function submitAssignment(Request $request, int $assignment)
    {
        $student = $this->student($request);
        $a = Assignment::findOrFail($assignment);
        $this->course($request, $a->course_id);

        $file = $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx,zip'],
        ])['file'];

        $existing = Submission::where('assignment_id', $a->id)->where('student_id', $student->id)->first();
        abort_if($existing?->score !== null, 422, 'Tugas sudah dinilai, tidak bisa dikirim ulang.');
        if ($existing) {
            Storage::disk('local')->delete($existing->file_path);
        }

        $sub = Submission::updateOrCreate(
            ['assignment_id' => $a->id, 'student_id' => $student->id],
            [
                'file_path' => $file->store("submissions/{$a->id}", 'local'),
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ],
        );

        return response()->json(['data' => ['id' => $sub->id, 'status' => 'dikumpulkan']], 201);
    }

    /** Rata-rata nilai per mapel per kategori, plus rinciannya. */
    public function grades(Request $request)
    {
        $grades = $this->student($request)->grades()->with('course:id,name,icon,color')->latest()->get()
            ->groupBy(fn (Grade $g) => $g->course_id.'|'.$g->category)
            ->map(function ($items) {
                $course = $items->first()->course;

                return [
                    'course_id' => $course->id,
                    'course_name' => $course->name,
                    'category' => $items->first()->category,
                    'score' => (int) round($items->avg('score')),
                    'icon' => $course->icon,
                    'color' => $course->color,
                    'items' => $items->map(fn (Grade $g) => [
                        'title' => $g->title, 'score' => $g->score, 'date' => $g->updated_at->toIso8601String(),
                    ])->values(),
                ];
            })->sortBy('course_name')->values();

        return ['data' => $grades];
    }

    public function schedules(Request $request)
    {
        $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date']]);
        $student = $this->student($request);

        $schedules = Schedule::whereIn('course_id', $student->courses()->select('id'))
            ->whereBetween('date', [
                $request->date('from') ?? now()->subMonths(1)->startOfMonth(),
                $request->date('to') ?? now()->addMonths(2)->endOfMonth(),
            ])
            ->with('course:id,name,tutor')->orderBy('date')->orderBy('start_time')->get()
            ->map(fn (Schedule $s) => [
                'id' => $s->id,
                'date' => $s->date->toDateString(),
                'course_name' => $s->course->name,
                'time' => substr($s->start_time, 0, 5).' - '.substr($s->end_time, 0, 5),
                'tutor' => $s->tutor ?? $s->course->tutor ?? '-',
                'room' => $s->room ?? '-',
            ]);

        return ['data' => $schedules];
    }

    public function announcements(Request $request)
    {
        $items = Announcement::visibleTo($this->student($request))->latest('published_at')->limit(50)->get()
            ->map(fn (Announcement $a) => [
                'id' => $a->id, 'title' => $a->title, 'body' => $a->body, 'date' => $a->published_at->toIso8601String(),
            ]);

        return ['data' => $items];
    }

    public function notifications(Request $request)
    {
        $items = $this->student($request)->notifications()->latest()->limit(100)->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'body' => $n->body,
                'time' => $n->created_at->locale('id')->diffForHumans(),
                'created_at' => $n->created_at->toIso8601String(),
                'read' => $n->read_at !== null,
            ]);

        return ['data' => $items];
    }

    public function readNotifications(Request $request)
    {
        $this->student($request)->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return ['data' => ['message' => 'OK']];
    }
}
