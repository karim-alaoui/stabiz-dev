<?php

namespace App\Actions;

use App\Exceptions\ActionException;
use App\Models\EmailTemplate;
use App\Models\Staff;
use App\Models\UploadedDoc;
use App\Models\User;
use App\Notifications\NotificationMail;
use Exception;

class VerifyDoc
{
    /**
     * @param UploadedDoc $doc
     * @param string $state
     */
    private static function sendNotification(UploadedDoc $doc, string $state)
    {
        // wrap in try catch so that an error doesn't stop the entire verification process
        try {
            $doc->refresh();
            if ($state == 'rejected') {
                /**@var EmailTemplate $emailTemplate */
                $emailTemplate = EmailTemplate::query()
                    ->where('name', EmailTemplate::DOC_REJECTED)
                    ->first();
                if (!$emailTemplate) return;
                $extract = (new ExtractTxt4mMailTemplate($emailTemplate))->setDocName($doc->doc_name);
                /**@var User $user */
                $user = $doc->user;
                $user->notify(new NotificationMail($emailTemplate, $extract->getSubject(), $extract->getBody()));
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @param UploadedDoc $doc
     * @param Staff $staff - the staff who is verifying the doc
     * @param string $state - either approved or rejected
     * @param string|null $remarks
     * @return UploadedDoc
     * @throws ActionException
     */
    public static function execute(UploadedDoc $doc, Staff $staff, string $state, string $remarks = null): UploadedDoc
    {
        $doc->refresh();
        $state = trim(strtolower($state));

        if ($state == 'approved') {
            if ($doc->approved_at) {
                throw new ActionException(__('This document is already approved'));
            }
        } elseif ($state == 'rejected') {
            if ($doc->rejected_at) {
                throw new ActionException(__('This document is already rejected'));
            }
        } else {
            $msg = 'state can be either rejected or approved';
            throw new ActionException(__($msg));
        }

        $doc->approved_at = $state == 'approved' ? now() : null;
        $doc->rejected_at = $state == 'rejected' ? now() : null;
        $doc->verified_by_staff_id = $staff->id;
        $doc->remarks = $remarks;
        $doc->save();

        self::sendNotification($doc, $state);

        return $doc;
    }
}
