<?php

namespace Tests\Feature;

use App\Models\AssignmentSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssignmentSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_a_student_can_submit_and_download_their_assignment(): void
    {
        $student = User::factory()->create();

        $response = $this->actingAs($student)->post(route('submissions.store'), [
            'title' => 'Research paper',
            'course' => 'Biology',
            'notes' => 'Chapter three',
            'file' => UploadedFile::fake()->create('research.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('assignment_submissions', [
            'user_id' => $student->id,
            'title' => 'Research paper',
            'course' => 'Biology',
            'original_filename' => 'research.pdf',
            'status' => 'submitted',
        ]);

        $submission = AssignmentSubmission::firstOrFail();
        Storage::disk('local')->assertExists($submission->file_path);

        $this->actingAs($student)
            ->get(route('submissions.download', $submission))
            ->assertOk()
            ->assertDownload('research.pdf');
    }

    public function test_a_student_cannot_view_download_or_delete_another_students_assignment(): void
    {
        $owner = User::factory()->create();
        $otherStudent = User::factory()->create();

        $this->actingAs($owner)
            ->post(route('submissions.store'), [
                'title' => 'Private paper',
                'course' => 'Biology',
                'file' => UploadedFile::fake()->create('private.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('dashboard'));

        $submission = AssignmentSubmission::firstOrFail();

        $this->actingAs($otherStudent)
            ->get(route('dashboard'))
            ->assertDontSee('Private paper');

        $this->actingAs($otherStudent)
            ->get(route('submissions.download', $submission))
            ->assertNotFound();

        $this->actingAs($otherStudent)
            ->delete(route('submissions.destroy', $submission))
            ->assertNotFound();

        $this->assertDatabaseHas('assignment_submissions', ['id' => $submission->id]);
        Storage::disk('local')->assertExists($submission->file_path);
    }

    public function test_a_student_can_delete_their_assignment_and_file(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->post(route('submissions.store'), [
                'title' => 'Remove this paper',
                'course' => 'Biology',
                'file' => UploadedFile::fake()->create('remove.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('dashboard'));

        $submission = AssignmentSubmission::firstOrFail();
        Storage::disk('local')->assertExists($submission->file_path);

        $this->actingAs($student)
            ->delete(route('submissions.destroy', $submission))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('assignment_submissions', ['id' => $submission->id]);
        Storage::disk('local')->assertMissing($submission->file_path);
    }

    public function test_invalid_file_types_and_oversized_files_are_rejected(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->post(route('submissions.store'), [
                'title' => 'Executable',
                'course' => 'Biology',
                'file' => UploadedFile::fake()->create('program.exe', 10, 'application/octet-stream'),
            ])
            ->assertSessionHasErrors('file');

        $this->actingAs($student)
            ->post(route('submissions.store'), [
                'title' => 'Too large',
                'course' => 'Biology',
                'file' => UploadedFile::fake()->create('large.pdf', 20 * 1024 + 1, 'application/pdf'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('assignment_submissions', 0);
    }
}
