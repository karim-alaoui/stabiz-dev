<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Models\Application;
use App\Models\EmailTemplate;
use App\Models\Package;
use App\Models\Recommendation;
use App\Models\Staff;
use App\Models\User;
use App\Notifications\NotificationMail;
use Exception;

/**
 * Apply to entrepreneurs/founders
 * Class Apply
 * @package App\Actions
 */
class Apply
{
    /**
     * If the user is applying to another user and this was recommendation to each other
     * by any staff, then notify that staff.
     * @param Application $application
     */
    public static function notifyStaff(Application $application)
    {
        try {
            /**
             * An user would apply to someone when it comes into their recommendation list.
             * Recommendation list would come after a staff has recommended an user to another user.
             */

            /**@var Recommendation $recommendation */
            $recommendation = Recommendation::query()
                ->where([
                    'recommended_to_user_id' => $application->applied_by_user_id,
                    'recommended_user_id' => $application->applied_to_user_id
                ])->first();
            /**@var Staff $staff */
            $staff = $recommendation->staff;

            if ($recommendation && $recommendation?->by_staff_id) {
                $template = EmailTemplate::name(EmailTemplate::USER_APPLIED_NOTIFY_STAFF)->first();
                if (is_null($template)) return;
                $extract = (new ExtractTxt4mMailTemplate($template))
                    ->setAppliedByUser($application->appliedBy)
                    ->setAppliedToUser($application->appliedTo);

                $staff->notify(new NotificationMail($template, $extract->getSubject(), $extract->getBody()));
            }
        } catch (Exception) {
        }
    }

    /**
     * @param User $user
     * @param User $applyToUser
     * @return mixed
     * @throws ActionException
     */
    public static function execute(User $user, User $applyToUser): mixed
    {
        $userType = $user->type;

        if ($user->id == $applyToUser->id) {
            throw new ActionException(__('Can not apply to oneself'));
        }

        $applyToUserType = $applyToUser->type;
        // user can only apply to opposite user type
        if ($userType == $applyToUserType) {
            if ($userType == User::ENTR) {
                throw new ActionException(__('Entrepreneur user apply to founders'));
            } else {
                throw new ActionException(__('Founder user apply to entrepreneurs'));
            }
        }

        if (!$user->subscribed(Package::PREMIUM)) {
            // remove this restriction for now. In the future, it will be there.
//            throw new ActionException(__('You have to be a premium user to apply'));
        }

        $where = [
            'applied_to_user_id' => $applyToUser->id,
            'applied_by_user_id' => $user->id
        ];
        // check if already applied or not
        $active = Application::query()
            ->active()
            ->where($where)
            ->first();

        if ($active) throw new ActionException(__('Already applied to this user'));

        $apl = Application::create($where);

        self::notifyStaff($apl);

        return $apl;
    }
}
