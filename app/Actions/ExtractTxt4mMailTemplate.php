<?php


namespace App\Actions;

use App\Exceptions\ActionException;
use App\Models\EmailTemplate;
use App\Models\Package;
use App\Models\Plan;
use App\Models\Prefecture;
use App\Models\Subscription;
use App\Models\User;

/**
 * This will give the actual text from an email template by replacing the variables.
 * Variables are set in this format - ##variable_name## Example- Hi ##first_name##, your subscription has started...
 * So, if there was data like Hi #name#, this is an...
 * and name was Andrew,
 * it will return Hi Andrew, this is an...
 */
class ExtractTxt4mMailTemplate
{
    /**
     * @var ?User
     */
    public ?User $user = null;

    public ?EmailTemplate $emailTemplate = null;
    public ?string $email = null;
    public ?string $name = null;
    public ?string $phone = null;
    public ?string $gender = null;
    public ?string $birthday = null;
    public string|int|null $otp = null;
    public ?Prefecture $prefecture = null;
    public ?string $firstName = null;
    public ?string $firstNameCana = null;
    public ?string $docName = null;

    /**
     * @param mixed $firstNameCana
     * @return ExtractTxt4mMailTemplate
     * @noinspection PhpUnused
     */
    public function setFirstNameCana(string $firstNameCana = null): static
    {
        $this->firstNameCana = $firstNameCana;
        return $this;
    }

    public ?string $lastNameCana = null;

    /**
     * @param mixed $lastNameCana
     * @return ExtractTxt4mMailTemplate
     * @noinspection PhpUnused
     */
    public function setLastNameCana(string $lastNameCana = null): static
    {
        $this->lastNameCana = $lastNameCana;
        return $this;
    }


    /**
     * @param mixed $firstName
     * @return ExtractTxt4mMailTemplate
     */
    public function setFirstName(string $firstName = null): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public ?string $lastName = null;

    /**
     * @param mixed $lastName
     * @return ExtractTxt4mMailTemplate
     */
    public function setLastName(string $lastName = null): static
    {
        $this->lastName = $lastName;
        return $this;
    }


    /**
     * @param mixed $prefecture
     * @return ExtractTxt4mMailTemplate
     */
    public function setPrefecture(Prefecture $prefecture = null): static
    {
        $this->prefecture = $prefecture;
        return $this;
    }

    /**
     * ExtractTextFromEmailTemplate constructor.
     * -----------------------------
     * DON'T ADD ANYTHING INTO THE constructor
     * KEEP IT LIKE IT IT. ADDING ANYTHING INTO IT
     * WOULD END UP BREAKING ALL THE PARTS THAT ARE
     * USING IT FOR SENDING EMAIL. IF YOU WANT TO ADD ANYTHING,
     * ADD A SETTER METHOD
     * -----------------------------
     * @param EmailTemplate $emailTemplate
     */
    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * @param mixed $otp
     * @return ExtractTxt4mMailTemplate
     */
    public function setOtp(string|int $otp = null): static
    {
        $this->otp = $otp;
        return $this;
    }

    /**
     * @param mixed $email
     * @return ExtractTxt4mMailTemplate
     */
    public function setEmail(string $email = null): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param mixed $name
     * @return ExtractTxt4mMailTemplate
     */
    public function setName(string $name = null): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $phone
     * @return ExtractTxt4mMailTemplate
     */
    public function setPhone(string $phone = null): static
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @param mixed $gender
     * @return ExtractTxt4mMailTemplate
     */
    public function setGender(string $gender = null): static
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @param mixed $user
     * @return ExtractTxt4mMailTemplate
     */
    public function setUser(User $user = null): static
    {
        $this->user = $user;
        return $this;
    }

    public ?Subscription $subscription = null;
    public ?User $appliedToUser = null;
    public ?User $appliedByUser = null;

    /**
     * @param $text
     * @return mixed
     */
    public function replaceUserFields($text): mixed
    {
        $user = $this->user;
        if ($this->email) $text = str_replace('##email##', $this->email, $text);
        if ($this->name) $text = str_replace('##name##', $this->name, $text);
        if ($this->gender) $text = str_replace('##gender##', $this->gender, $text);
        if ($this->phone) $text = str_replace('##phone##', $this->phone, $text);
        if ($this->birthday) {
            $text = str_replace('##birthday##', $this->birthday, $text);
            $text = str_replace('##dob##', $this->birthday, $text);
        }

        if ($user) {
            $firstName = $user->first_name;
            $text = str_replace('##email##', $user->email, $text);
            $text = str_replace('##name##', ($firstName ? $firstName . ' ' : '') . $user->last_name, $text);
            $text = str_replace('##first_name##', $user->first_name, $text);
            $text = str_replace('##last_name##', $user->last_name, $text);
            $text = str_replace('##first_name_cana##', $user->first_name_cana, $text);
            $text = str_replace('##last_name_cana##', $user->last_name_cana, $text);
        }

        return $text;
    }


    /**
     * Replaces env variables
     * @param $text
     * @return string|string[]
     */
    public function replaceEnvVariables($text): array|string
    {
        return str_replace('##contact_email##', config('other.contact_email'), $text);
    }

    /**
     * @param $text
     * @return mixed
     */
    public function replaceSingleVariables($text): mixed
    {
        if ($this->otp) {
            $text = str_replace('##otp##', $this->otp, $text);
        }

        $prefecture = $this->prefecture;
        if ($prefecture) {
            $text = str_replace('##prefecture##', $prefecture->name, $text);
        }

        if ($this->firstName) $text = str_replace('##first_name##', $this->firstName, $text);
        if ($this->lastName) $text = str_replace('##last_name##', $this->lastName, $text);
        if ($this->firstNameCana) $text = str_replace('##first_name_cana##', $this->firstNameCana, $text);
        if ($this->lastNameCana) $text = str_replace('##last_name_cana##', $this->lastNameCana, $text);

        return $text;
    }

    /**
     * @param $text
     * @return mixed
     */
    public function replaceSubscription($text): mixed
    {
        $subscription = $this->subscription;
        if ($subscription) {
            $subscriptionItem = $subscription->subscriptionItem()->with(['plan', 'package'])->first();
            if (!$subscriptionItem) return $text;

            /**@var Package $package */
            $package = $subscriptionItem->package;
            /**@var Plan $plan */
            $plan = $subscriptionItem->plan;

            $text = str_replace('##package_name##', __($package->name), $text);
            $text = str_replace('##plan_interval##', __($plan->interval), $text);
            $text = str_replace('##plan_price##', $plan->price, $text);
            $text = str_replace('##plan_currency##', $plan->currency, $text);

            $user = $this->user;
            if (!$user) $this->setUser($subscription->user);
        }
        return $text;
    }

    /**
     * Replace the fields for applied to user
     * @param string $text
     * @return array|string
     */
    public function replaceAppliedTo(string $text): array|string
    {
        $appliedTo = $this->appliedToUser;
        if ($appliedTo) {
            $text = str_replace('##applied_to_user_type##', __($appliedTo->type), $text);
            $text = str_replace('##applied_to_first_name##', $appliedTo->first_name, $text);
            $text = str_replace('##applied_to_last_name##', $appliedTo->last_name, $text);
            $text = str_replace('##applied_to_first_name_cana##', $appliedTo->first_name_cana, $text);
            $text = str_replace('##applied_to_last_name_cana##', $appliedTo->last_name_cana, $text);
        }

        return $text;
    }

    /**
     * Replace the fields for applied by user
     * @param string $text
     * @return array|string
     */
    public function replaceAppliedBy(string $text): array|string
    {
        $appliedBy = $this->appliedByUser;
        if ($appliedBy) {
            $text = str_replace('##applied_by_user_type##', __($appliedBy->type), $text);
            $text = str_replace('##applied_by_first_name##', $appliedBy->first_name, $text);
            $text = str_replace('##applied_by_last_name##', $appliedBy->last_name, $text);
            $text = str_replace('##applied_by_first_name_cana##', $appliedBy->first_name_cana, $text);
            $text = str_replace('##applied_by_last_name_cana##', $appliedBy->last_name_cana, $text);
        }

        return $text;
    }

    /**
     * @param string $text
     * @return array|string
     */
    public function replaceDocName(string $text): array|string
    {
        if ($this->docName) {
            $translatedTxt = __($this->docName);
            $text = str_replace('##doc_name##', $translatedTxt, $text);
        }
        return $text;
    }

    /**
     * @param $body
     * @param $subject
     * @return array|string
     * @throws ActionException
     */
    public function replaceTagsToActualValue($body, $subject): array|string
    {
        if (!$body && !$subject) {
            throw new ActionException('Both body and subject cannot be false');
        } elseif ($body && $subject) {
            throw new ActionException('Both body and subject cannot be true');
        }
        $text = $body ? $this->emailTemplate->body : $this->emailTemplate->subject;

        $text = $this->replaceSingleVariables($text);
        $text = $this->replaceSubscription($text);
        $text = $this->replaceEnvVariables($text);
        $text = $this->replaceDocName($text);
        $text = $this->replaceAppliedBy($text);
        $text = $this->replaceAppliedTo($text);
        /**
         * Keep this line as sometimes when the user is not supplied,
         * the user would be dynamically set if the model has user relationship.
         * For example, if subscription provided and user is not provided,
         * then the user would be dynamically set since each subscription belongs to an user, there's
         * a relationship between the two. It's only if the user was not set.
         */
        return $this->replaceUserFields($text);
    }

    /**
     * @param bool $body
     * @param false $subject
     * @return array|string
     * @throws ActionException
     */
    public function execute(bool $body = true, bool $subject = false): array|string
    {
        return $this->replaceTagsToActualValue($body, $subject);
    }

    /**
     * @return array|string
     * @throws ActionException
     */
    public function getSubject(): array|string
    {
        return $this->replaceTagsToActualValue(false, true);
    }

    /**
     * @return array|string
     * @throws ActionException
     */
    public function getBody(): array|string
    {
        return $this->replaceTagsToActualValue(true, false);
    }

    /**
     * @param mixed|null $birthday
     * @return ExtractTxt4mMailTemplate
     * @noinspection PhpUnused
     */
    public function setBirthday(mixed $birthday = null): static
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @param Subscription $subscription
     * @return ExtractTxt4mMailTemplate
     */
    public function setSubscription(Subscription $subscription): ExtractTxt4mMailTemplate
    {
        $this->subscription = $subscription;
        return $this;
    }

    /**
     * @param User|null $appliedToUser
     * @return ExtractTxt4mMailTemplate
     */
    public function setAppliedToUser(?User $appliedToUser): ExtractTxt4mMailTemplate
    {
        $this->appliedToUser = $appliedToUser;
        return $this;
    }

    /**
     * @param User|null $appliedToUser
     * @return $this
     */
    public function setAppliedByUser(?User $appliedToUser): ExtractTxt4mMailTemplate
    {
        $this->appliedByUser = $appliedToUser;
        return $this;
    }

    /**
     * @param string|null $docName
     * @return ExtractTxt4mMailTemplate
     */
    public function setDocName(?string $docName = null): ExtractTxt4mMailTemplate
    {
        $this->docName = $docName;
        return $this;
    }
}
