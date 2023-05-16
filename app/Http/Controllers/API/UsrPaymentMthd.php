<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\PaymentMethodResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @group Payment method
 *
 * Payment method means each card stored for paying for premium package.
 * Delete, get payment methods
 */
class UsrPaymentMthd extends BaseApiController
{
    /**
     * Get payment methods
     *
     */
    public function index(): AnonymousResourceCollection
    {
        /**@var User $user */
        $user = auth()->user();
        return PaymentMethodResource::collection($user->paymentMethods());
    }

    /**
     * Delete payment method
     *
     * @urlParam payment_method required the payment method id. No-example
     * @param string $payment_method
     * @return Response|JsonResponse
     */
    public function destroy(string $payment_method): Response|JsonResponse
    {
        /**@var User $user */
        $user = auth()->user();
        $method = $user->findPaymentMethod($payment_method);

        if (!$method) return $this->errorMsg(__('Invalid payment method ID'));
        $method->delete();

        return $this->noContent();
    }

    /**
     * Delete all payment methods
     */
    public function destroyAll(): Response
    {
        /**@var User $user */
        $user = auth()->user();

        $user->deletePaymentMethods();
        return $this->noContent();
    }
}
