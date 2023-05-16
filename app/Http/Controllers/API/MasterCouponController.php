<?php

namespace App\Http\Controllers\API;

use App\Actions\CreateMstrCpn;
use App\Exceptions\ActionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMasterCouponReq;
use App\Http\Resources\MasterCouponResource;
use App\Models\MasterCoupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Master Coupon
 */
class MasterCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        //
    }

    /**
     * Create master coupon
     * @param CreateMasterCouponReq $request
     * @return JsonResponse
     * @throws ActionException
     */
    public function store(CreateMasterCouponReq $request): JsonResponse
    {
        $coupon = CreateMstrCpn::execute($request->validated());
        return (new MasterCouponResource($coupon))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(MasterCoupon $coupon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, MasterCoupon $coupon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterCoupon $coupon)
    {
        //
    }
}
