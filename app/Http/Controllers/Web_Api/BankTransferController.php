<?php

namespace App\Http\Controllers\Web_Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Helpers\ApiHelpers;

class BankTransferController extends Controller
{
    public function banktransferstore(Request $request)
    {

        try {

            $plan = $request->plan;
            $tempFilePath = null;

            $res = ApiHelpers::processBase64File($request->payment_receipt, $request->fileName, $request->mime_type);

            if ($res == null) {
                return response()->json(['status' => 422, 'message' => 'Unable to process base64 file.']);
            }

            $request['payment_receipt'] = $res['file'];
            $tempFilePath = $res['tempFilePath'];

            $coupon_id = '';
            $plan = $request->plan;
            $authuser = $request->authuser;

            if ($plan) {

                $price = $plan['price'];
                if (isset($request->coupon) && !empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();

                    if (!empty($coupons)) {
                        $usedCoupun     = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $price          = $plan->price - $discount_value;

                        if ($coupons->limit == $usedCoupun) {
                            return response()->json(['status' => 422, 'message' => 'This coupon code has expired.']);
                        }
                        $coupon_id = $coupons->id;
                    } else {
                        return response()->json(['status' => 422, 'message' => 'This coupon code is invalid or has expired.']);
                    }
                }

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                if (!empty($request->payment_receipt)) {
                    $dir        = 'uploads/order';
                    $path = Utility::upload_file($request, 'payment_receipt', $request->fileName, $dir, []);

                    if (file_exists($tempFilePath)) {
                        unlink($tempFilePath);
                    }

                    if ($path['flag'] != 1) {
                        return response()->json(['status' => 422, 'error' =>  $path['msg']]);
                    }
                }
                try {
                    $new_order = Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan['name'],
                            'plan_id' => $plan['id'],
                            'price' => $price,
                            'price_currency' => !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD',
                            'txn_id' => '',
                            'payment_type' => 'Bank Transfer',
                            'payment_status' => 'Pending',
                            'receipt' => $request->fileName,
                            'user_id' => $authuser['id'],
                            'company_id' => $authuser['company_id'],
                        ]
                    );
                } catch (\Exception $th) {
                    \Log::error('Error creating order: ' . $th->getMessage());
                    \Log::error('Exception trace: ' . $th->getTraceAsString());
                    return response()->json([
                        'status' => 422, 'message' => 'order_error: ' . $th->getMessage()
                    ]);
                }


                if (!empty($request->coupon)) {
                    $userCoupon         = new UserCoupon();
                    $userCoupon->user   = $authuser->id;
                    $userCoupon->coupon = $coupons->id;
                    $userCoupon->order  = $orderID;
                    $userCoupon->order  = $authuser->company_id;
                    $userCoupon->save();

                    $usedCoupun = $coupons->used_coupon();
                    if ($coupons->limit <= $usedCoupun) {
                        $coupons->is_active = 0;
                        $coupons->save();
                    }
                }

                return response()->json(['status' => 200, 'new_order' => $new_order, 'message' => 'Plan payment request send successfully']);
            } else {
                return response()->json(['status' => 422, 'message' => 'Plan is deleted.']);
            }
        } catch (\Exception $th) {
            return response()->json([
                'status' => 422, 'message' => 'error_overall: ' . $th->getMessage()
            ]);
        }
    }
}
