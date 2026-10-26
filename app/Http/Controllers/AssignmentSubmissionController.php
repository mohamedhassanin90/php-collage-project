<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use RuntimeException;
use Throwable;

class AssignmentSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $submissions = $request->user()
            ->assignmentSubmissions()
            ->latest()
            ->get();

        return view('dashboard', compact('submissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'course' => ['required', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'file' => [
                'required',
                File::types(['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip'])
                    ->max(20 * 1024),
            ],
        ]);

        $file = $request->file('file');
        $filePath = $file->store('private/submissions', 'local');

        if ($filePath === false) {
            throw new RuntimeException('The assignment file could not be saved.');
        }

        try {
            $request->user()->assignmentSubmissions()->create([
                'title' => $validated['title'],
                'course' => $validated['course'],
                'notes' => $validated['notes'] ?? null,
                'original_filename' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'status' => 'submitted',
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($filePath);

            throw $exception;
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'تم تسليم الواجب بنجاح.');
    }

    public function download(Request $request, AssignmentSubmission $submission)
    {
        abort_unless($submission->user_id === $request->user()->id, 404);

        return Storage::disk('local')->download(
            $submission->file_path,
            $submission->original_filename
        );
    }

    public function destroy(Request $request, AssignmentSubmission $submission)
    {
        abort_unless($submission->user_id === $request->user()->id, 404);

        Storage::disk('local')->delete($submission->file_path);
        $submission->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'تم حذف تسليم الواجب.');
    }
}
