<?php

namespace App\Actions;

use App\Exceptions\ActionException;
use App\Models\Application;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Notifications\NotificationMail;
use Exception;

/**
 * Accept applications by founder/entr
 */
class AcceptApl
{
    /**
     * @param Application $application
     * @throws ActionException
     */
    public static function execute(Application $application)
    {
        if (!is_null($application->accepted_at)) throw new ActionException(__('Application already accepted'));
        $application->accepted_at = now();
        $application->rejected_at = null;
        $application->save();

        // send notification regarding this
        // wrap in the try catch so that it doesn't stop
        // the accepting if there's error in sending notification
        try {
            $emailTemplate = EmailTemplate::where('name', EmailTemplate::APPL_ACCEPTED)
                ->first();
            $extract = new ExtractTxt4mMailTemplate($emailTemplate);
            /**@var User $appliedBy */
            $appliedBy = $application->appliedBy;
            $extract->setAppliedToUser($appliedBy);

            $appliedBy->notify(new NotificationMail($emailTemplate, $extract->getSubject(), $extract->getBody()));
        } catch (Exception) {
        }
    }
}
