<?php


namespace App\Actions;


use App\Models\User;

/**
 * Class UpdateStripeCust
 * @package App\Actions
 */
class UpdateStripeCust
{
    /**
     * @param User $user
     * @return void
     */
    public static function execute(User $user): void
    {
        $name = "$user->first_name $user->last_name";
        $description = null;
        if ($user->first_name || $user->last_name)
            $description = "Stripe profile of $name";

        $address = [
            'country' => 'jp' // keep this hardcoded since it's used in Japan only
        ];
        if ($user->type == User::ENTR) {
            $address['line1'] = $user?->entrProfile?->address;
        }


        $income = $user->income;
        $incomeTxt = null;
        if ($income) {
            $upper = $income->upper_limit;
            $incomeTxt = "from $income->lower_limit to " . ($upper ?: 'unlimited') . " $income->currency";
        }
        $metadata = [
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'first_name_cana' => $user->first_name_cana,
            'last_name_cana' => $user->last_name_cana,
            'type' => $user->type,
            'dob' => $user->dob,
            'gender' => $user->gender,
            'income' => $incomeTxt
        ];

        $user->updateStripeCustomer([
            'description' => $description,
            'name' => $name,
            'address' => $address,
            'metadata' => $metadata
        ]);
    }
}
