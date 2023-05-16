<?php

namespace App\Actions;

use App\Exceptions\ActionException;
use App\Models\UploadedDoc;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Upload document to verify
 * Currently, only for founder
 */
class UploadDoc
{
    /**
     * @param User $user
     * @param UploadedFile $file
     * @param string $docName
     * @return UploadedDoc|Model
     * @throws ActionException
     */
    public static function execute(User $user, UploadedFile $file, string $docName): Model|UploadedDoc
    {
        try {
            if ($user->type != User::FOUNDER) {
                throw new ActionException(__('You have to be a founder to upload these docs'));
            }

            $docName = strtolower($docName);
            if (!in_array($docName, UploadedDoc::DOC_NAMES)) {
                throw new ActionException(__('Invalid doc name'));
            }

            $mimeTypes = ['image/jpeg', 'image/png', 'application/pdf', 'image/jpg'];
            $extensions = ['jpg', 'png', 'pdf'];
            if (!in_array($file->getClientMimeType(), $mimeTypes) || !in_array($file->getClientOriginalExtension(), $extensions)) {
                $msg = 'The file must be a file of type: jpg, png, pdf.';
                throw new ActionException(__($msg));
            }

            $sizeInKb = $file->getSize() / 1000;
            if ($sizeInKb > 5000) {
                throw new ActionException(__('exception.max_size', ['size' => '5MB']));
            }

            // check if already approved or not
            $approved = $user->docs()->approvedDoc($docName)->first();
            if ($approved) {
                throw new ActionException(__('This document is already approved'));
            }

            $existingDoc = $user->docs()
                ->where('doc_name', 'ilike', $docName)
                ->first();

            $disk = config('filesystems.default');
            $filepath = Storage::disk($disk)->put('docs', $file);
            $doc = $user->docs()->create([
                'filepath' => $filepath,
                'file_disk' => $disk,
                'doc_name' => $docName
            ]);

            // keep it after creating the new record
            // so that we can delete it when a record is successfully created
            if ($existingDoc) {
                $exists = Storage::disk($existingDoc->file_disk)
                    ->exists($existingDoc->filepath);
                if ($exists) {
                    Storage::disk($existingDoc->file_disk)->delete($existingDoc->filepath);
                    $existingDoc->file_deleted = true;
                    $existingDoc->save();
                }
                $existingDoc->delete();
            }
            return $doc;
        } catch (Exception $e) {
            // if file was uploaded, delete it, otherwise the storage would be used
            // for unnecessary files that has no reference to any user
            if (isset($disk) && isset($filepath)) Storage::disk($disk)->delete($filepath);
            report($e);
            throw new ActionException($e->getMessage());
        }
    }
}
