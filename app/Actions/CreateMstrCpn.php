<?php


namespace App\Actions;


use App\Exceptions\ActionException;
use App\Models\MasterCoupon;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

/**
 * Create master coupon
 * Class CreateMstrCpn
 * @package App\Actions
 */
class CreateMstrCpn
{
    /**
     * @param array $data
     * @return mixed
     * @throws ActionException
     */
    public static function execute(array $data): mixed
    {
        $name = Arr::get($data, 'name');
        $currency = Arr::get($data, 'currency', 'jpy');
        $duration = Arr::get($data, 'duration', 'once');
        $stripeData = [
            'name' => $name,
            'currency' => $currency,
            'duration' => $duration,
        ];

        if (strtolower($duration) == 'repeating') {
            $stripeData['duration_in_months'] = Arr::get($data, 'duration_in_months');
        }

        $percentOff = Arr::get($data, 'percent_off');
        $amountOff = Arr::get($data, 'amount_off');
        if ($percentOff) {
            $stripeData['percent_off'] = $percentOff;
        } else {
            $stripeData['amount_off'] = $amountOff;
        }

        $redeemBy = Arr::get($data, 'redeem_by');
        if ($redeemBy) {
            $redeemBy = Carbon::parse($redeemBy)->unix();
            $stripeData['redeem_by'] = $redeemBy;
        }

        $stripe = new StripeClient(config('services.stripe.secret'));
        try {
            $stripeCoupon = $stripe->coupons->create($stripeData);
        } catch (ApiErrorException) {
            throw new ActionException(__('Could not create coupon'));
        }

        return MasterCoupon::create([
            'stripe_id' => $stripeCoupon->id,
            'name' => $name,
            'amount_off' => $amountOff,
            'percent_off' => $percentOff,
            'currency' => $currency,
            'duration' => $duration,
            'duration_in_months' => Arr::get($data, 'duration_in_months'),
            'redeem_by' => $redeemBy,
            'assign_after' => Arr::get($data, 'assign_after'),
            'is_a_campaign' => Arr::get($data, 'is_a_campaign')
        ]);
    }
}
