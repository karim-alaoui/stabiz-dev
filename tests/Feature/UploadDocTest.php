<?php

namespace Tests\Feature;

use App\Models\UploadedDoc;
use App\Models\User;
use App\Notifications\NotificationMail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Passport;
use Tests\AppBaseTestCase;

class UploadDocTest extends AppBaseTestCase
{
    private string $endpoint = 'api/v1/user/docs';
    private string $disk = 'docs'; // fake disk that is used in Storage for testing
    private string $verifyEndpoint = 'api/v1/docs/verify';

    public function test_upload_validation()
    {
        $this->authFounder();
        $req = $this->post($this->endpoint);
        $req->assertJsonValidationErrors(['file', 'doc_name']);
    }

    /**
     * Upload files for this user so that we can test to show and delete the files
     * @param User|null $user
     */
    private function uploadFiles(User $user = null): TestResponse
    {
        if (is_null($user)) $user = User::factory()->state(['type' => User::FOUNDER])->create();

        Passport::actingAs($user);
        $disk = $this->disk;
        Storage::fake($disk);
        $file = UploadedFile::fake()->image('document.jpg');
        $docName = UploadedDoc::DOC_NAMES[0];
        $data = [
            'doc_name' => $docName,
            'file' => $file
        ];
        return $this->post($this->endpoint, $data);
    }

    public function test_upload_doc_for_different_users()
    {
        /**
         * before there was problem in our code which was overlooked.
         * When checking if user uploaded a document and it was approved, we're using the user id in query
         * UploadedDoc::approvedDoc($docname)->first() // this was the query used
         * as a result of that, if you upload the same document for a different user,
         * it would show that this document is already approved even though the user never
         * uploaded that document in the first place. To make sure that we don't make that mistake
         * again, upload a document for an user and get it approved, then upload the same document
         * for another user and see if the document is uploaded successfully or not
         */
        $req = $this->uploadFiles(); // this will return data that has id of the uploaded doc
        $docId = $req->json('data.id');
        // manually approve the document
        UploadedDoc::query()
            ->where('id', $docId)
            ->update([
                'approved_at' => now(),
                'rejected_at' => null
            ]);

        // create another user and upload document for that user
        $user = User::factory()->founder()->create();
        $req = $this->uploadFiles($user);
        $req->assertSuccessful();

        $newUploadedDocId = $req->json('data.id'); // store the new uploaded document id

        // upload the same doc for the same user again
        // when you upload the same doc, the previous doc should be deleted
        $this->uploadFiles($user);

        $doc = DB::table((new UploadedDoc())->getTable())
            ->find($newUploadedDocId);

        $this->assertTrue((bool)$doc->file_deleted); // if delete, this should be true
        $this->assertTrue((bool)$doc->deleted_at); // if deleted, this should be true as well
        Storage::disk($doc->file_disk)->assertMissing($doc->filepath); // check if the file was deleted from the storage as well
    }

    public function test_upload()
    {
        /**@var User $user */
        $user = $this->authFounder();
        $disk = $this->disk;
        Storage::fake($disk);
        $file = UploadedFile::fake()->image('document.jpg');
        $docName = UploadedDoc::DOC_NAMES[0];
        $data = [
            'doc_name' => $docName,
            'file' => $file
        ];
        $req = $this->post($this->endpoint, $data);
        $req->assertCreated();
        self::assertNotEmpty($user->docs);
        $uploadedDoc = $user->docs()->where('doc_name', $docName)->first();
        Storage::disk($uploadedDoc->file_disk)->assertExists($uploadedDoc->filepath);

        // upload the file again and previous file should be deleted
        $req2 = $this->post($this->endpoint, $data);
        $req2->assertCreated();
        Storage::disk($uploadedDoc->file_disk)->assertMissing($uploadedDoc->filepath);

        // entrepreneur can't upload docs only founder can
        $this->authEntr();
        $req3 = $this->post($this->endpoint, $data);
        $req3->assertStatus(400);
    }

    public function test_upload_file_size()
    {
        $this->authFounder();
        // file size limit is 5mb. should return error if more than that
        $file = UploadedFile::fake()->image('img.jpg')->size(7000); //7mb
        $req = $this->post($this->endpoint, [
            'doc_name' => UploadedDoc::DOC_NAMES[0],
            'file' => $file
        ]);
        $req->assertJsonValidationErrors(['file'])->assertJsonMissingValidationErrors('doc_name');
    }

    public function test_upload_index()
    {
        // check if it returns the uploaded files by the user
        /**@var User $user */
        $user = $this->authFounder();

        $this->uploadFiles($user);

        $req = $this->get($this->endpoint);
        $req->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data.0.id') // 0.id means on index 0 has id key. This will make sure that the result is actually returning something
                ->etc());
    }

    public function test_upload_delete()
    {
        /**@var User $user */
        $user = $this->authFounder();

        $this->uploadFiles($user);
        $doc = $user->docs()->first();

        $req = $this->delete($this->endpoint . '/' . $doc->id);
        $req->assertNoContent();

        // make sure that it doesn't exist on the storage anymore
        Storage::disk($this->disk)->assertMissing($doc->filepath);

        // check if the all the database columns are updated to the right values or not
        $deleted = DB::table((new UploadedDoc())->getTable())
            ->where('id', $doc->id)
            ->whereNotNull('deleted_at') // since it's soft deleted, it should not null anymore
            ->where('file_deleted', true) // the file is deleted, so this should be true
            ->first();

        $this->assertTrue((bool)$deleted);
    }

    public function test_list_docs()
    {
        $this->uploadFiles(); // this will upload a doc
        $endpoint = 'api/v1/docs';
        $this->authStaff();

        $req = $this->get($endpoint);
        $req->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('data.0.id') // a document is uploaded. So, it should exists
                ->etc());

        $this->testPagination($endpoint);
    }

    public function test_unauthorized_for_lst_docs()
    {
        // user should not be able to see the list of all the documents
        $endpoint = 'api/v1/docs';
        $this->authUser();
        $req2 = $this->get($endpoint);
        $req2->assertUnauthorized();
    }

    public function test_verify_validation()
    {
        $this->authStaff();
        $req = $this->post($this->verifyEndpoint);
        $req->assertJsonValidationErrors(['id', 'state']);
    }

    public function test_verify_by_user()
    {
        // users should not able to access this and it should return 401 a.k.a unauthenticated
        $this->authUser();
        $req = $this->post($this->verifyEndpoint);
        $req->assertUnauthorized();
    }

    private function verifyDocData(string $state): array
    {
        $user = User::factory()->founder()->create();
        $this->uploadFiles($user); // this will upload a document
        $doc = $user->docs()->first();
        return [
            'id' => $doc->id,
            'state' => $state, // either rejected or approved
            'remarks' => Str::random()
        ];
    }

    public function test_reject_document()
    {
        Notification::fake();
        $data = $this->verifyDocData('rejected');
        $this->authStaff();
        $req = $this->post($this->verifyEndpoint, $data);
        $req->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('message', 'The document is marked as rejected and the user is notified')
                ->etc());

        $docId = $data['id'];
        $doc = UploadedDoc::find($docId);
        // check if the document was actually updated in the database
        $this->assertTrue($doc->rejected_at && is_null($doc->approved_at));

        Notification::assertSentTo($doc->user, NotificationMail::class); // check the notification mail is sent since the doc is rejected
    }

    public function test_approve_doc()
    {
        Notification::fake();
        $verifyDocData = $this->verifyDocData('approved');
        $this->authStaff();
        $req = $this->post($this->verifyEndpoint, $verifyDocData);
        $req->assertSuccessful()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('message', 'The document is marked as approved')
                ->etc());

        $docId = $verifyDocData['id'];
        $doc = UploadedDoc::find($docId);
        // check if the document was actually updated in the database
        $this->assertTrue($doc->approved_at && is_null($doc->rejected_at));
        // make sure that nothing is sent since the doc is approved. When it's approved, no notification,
        // only when it's rejected, notification is sent
        Notification::assertNothingSent();
    }
}
