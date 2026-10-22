<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionDownloadController extends Controller
{
    public function __invoke(Request $request, Submission $submission)
    {
        abort_unless($request->user()?->isStaff(), 403);

        return Storage::disk('local')->download(
            $submission->file_path,
            "{$submission->student->nisn}_{$submission->file_name}",
        );
    }
}
