<?php

namespace App\Http\Controllers\API;

use App\Actions\CreateStripeCustIfNotExist;
use App\Actions\CreateSubscription;
use App\Actions\DelAllSub;
use App\Exceptions\ActionException;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\CreateSubReq;
use App\Http\Requests\GetSubReq;
use App\Http\Resources\SubscriptionResource;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\SetupIntent;

/**
 * @group Subscription
 *
 * Subscription plans, subscribe etc
 */
class SubscriptionController extends BaseApiController
{
    /**
     * Display all subscriptions
     *
     * All the subscriptions created in the past and the ones active
     */
    public function index(GetSubReq $request): AnonymousResourceCollection
    {
        /**@var User $user */
        $user = auth()->user();
        $query = $user->subscriptions()
            ->with(['subscriptionItem.package', 'subscriptionItem.plan']);
        if ($request->boolean('active')) $query = $query->active();
        return SubscriptionResource::collection($query->get());
    }

    /**
     * Create subscription
     *
     * @param CreateSubReq $request
     * @return JsonResponse
     * @throws ActionException
     * @throws IncompletePayment
     */
    public function store(CreateSubReq $request): JsonResponse
    {
        /**@var User $user */
        $user = auth()->user();
        $subscription = CreateSubscription::execute($user, $request->payment_method_id, Plan::findOrFail($request->plan_id));

        $subscription->load(['subscriptionItem.package', 'subscriptionItem.plan']);
        return (new SubscriptionResource($subscription))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Cancel subscription
     *
     * @responseFile status=204 storage/responses/delete.json
     */
    public function destroyAll(): Response
    {
        /**
         * This will stop any subscription
         * However, There's only one subscription active at a time in our app
         */
        DelAllSub::execute(auth()->user());
        return $this->noContent();
    }

    /**
     * Return setup intent for Stripe
     *
     * This is needed on the front end when collecting the user card info
     * and sending it to Stripe. This intent is added in that request to Stripe
     * @return SetupIntent
     */
    public function returnIntent(): SetupIntent
    {
        /**@var User $user */
        $user = auth()->user();
        // must create the customer if not exists, since without creating a customer on Stripe
        // you can't add a payment method. A payment method can be only added on a stripe customer
        CreateStripeCustIfNotExist::execute($user);
        return $user->createSetupIntent();
    }
}
