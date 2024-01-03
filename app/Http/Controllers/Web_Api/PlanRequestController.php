<?php

namespace App\Http\Controllers\Web_Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PlanRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /*
     * @plan_id = Plan ID encoded
     * @duration = what duration is selected by user while request
    */
    public function store(Request $request)
    {
        // dd($request);
        $company = Company::where('id', $request->company_id)->first();

        if ($request->plan_id && $company) {
            $plan_request = PlanRequest::create([
                'user_id' => $request->user_id,
                'plan_id' => $request->plan_id,
                'company_id' => $request->company_id,
            ]);

            return response()->json(['status' => 200, 'plan_request' => $plan_request]);
        } else {
            if (!$company) {
                return response()->json(['status' => 422, 'message' => 'This admin panel is not yet linked. please contact the support team.']);
            } else {
                return response()->json(['status' => 422, 'message' => 'Something went wrong. Try again later or if this issue persist contact the support team.']);
            }
        }
    }

    /*
     * @id = Project ID
     * @response = 1(accept) or 0(reject)
    */
    public function destroy($id)
    {
        if (PlanRequest::where('company_id', $id)->delete()) {
            return response()->json(['status' => 200, 'message' => 'Request Canceled Successfully.']);
        } else {
            return response()->json(['status' => 422, 'message' => 'Faild to cancel Plan Request.']);
        }
    }
    /*
     * @id = User ID
    */
}
