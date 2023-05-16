<?php

namespace App\Actions;

use App\Models\UploadedDoc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteUploadedDoc
{
    public static function execute(UploadedDoc $doc)
    {
        DB::transaction(function () use ($doc) {
            if (Storage::disk($doc->file_disk)->exists($doc->filepath)) {
                Storage::disk($doc->file_disk)->delete($doc->filepath);
                $doc->file_deleted = true;
                $doc->save();
            }

            $doc->delete();
        });
    }
}
