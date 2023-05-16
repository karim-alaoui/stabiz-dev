<?php

namespace App\Http\Controllers\API;

use App\Actions\DeleteUploadedDoc;
use App\Actions\ListUploadedDocs;
use App\Actions\UploadDoc;
use App\Actions\UploadDocLink;
use App\Actions\VerifyDoc;
use App\Exceptions\ActionException;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\ListUploadDocReq;
use App\Http\Requests\UploadDocReq;
use App\Http\Requests\VerifyDocReq;
use App\Http\Resources\UploadedDocResource;
use App\Models\Staff;
use App\Models\UploadedDoc;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @group Upload docs
 * Upload documents to verify
 */
class UploadDocController extends BaseApiController
{
    /**
     * Uploaded docs
     *
     * See uploaded docs of the user. This is only for the user
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        /**@var User $user */
        $user = auth()->user();
        $docs = $user->docs()->latest()->get();
        return UploadedDocResource::collection($docs);
    }

    /**
     * Upload document
     *
     * @throws ActionException
     */
    public function store(UploadDocReq $request): JsonResponse
    {
        /**@var User $user */
        $user = auth()->user();
        $uploadedDoc = UploadDoc::execute($user, $request->file('file'), $request->doc_name);
        return (new UploadedDocResource($uploadedDoc))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show a document
     *
     * Documents are really sensitive information. Thus, it will generate a link
     * which is only valid for only 2 mins. You can redirect the user there.
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show($id): JsonResponse
    {
        if (is_null($id)) {
            return $this->errorMsg(__('No document ID is sent'));
        }
        /**@var User|Staff $user */
        $user = auth()->user();
        $this->authorize('operateOnDoc', UploadedDoc::findOrFail($id));
        $link = UploadDocLink::execute($id, $user, 2);
        return $this->success(['link' => $link]);
    }

    /**
     * Delete uploaded file
     *
     * @param $id
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy($id): Response
    {
        $doc = UploadedDoc::findOrFail($id);
        $this->authorize('operateOnDoc', $doc);

        DeleteUploadedDoc::execute($doc);
        return $this->noContent();
    }

    /**
     * List documents
     *
     * This can be only accessed by the staff.
     * This will return all the documents which are uploaded by the users.
     * By default it will show the ones which are uploaded first but were neither approved not rejected
     * @param ListUploadDocReq $request
     * @return AnonymousResourceCollection
     */
    public function lstDocs(ListUploadDocReq $request): AnonymousResourceCollection
    {
        $docs = ListUploadedDocs::execute($request->all());
        return UploadedDocResource::collection($docs);
    }

    /**
     * Verify document
     *
     * When a document is rejected, the user is notified using mail.
     * @param VerifyDocReq $request
     * @return JsonResponse
     * @throws ActionException
     */
    public function verify(VerifyDocReq $request): JsonResponse
    {
        /**@var Staff $staff */
        $staff = auth()->user();
        $doc = UploadedDoc::findOrFail($request->id);
        $doc = VerifyDoc::execute($doc, $staff, $request->state, $request->remarks);

        if ($doc->approved_at) {
            return $this->successMsg(__('The document is marked as approved'));
        } elseif ($doc->rejected_at) {
            return $this->successMsg(__('The document is marked as rejected and the user is notified'));
        } else {
            return $this->errorMsg(__('exception.something_wrong'));
        }
    }
}
