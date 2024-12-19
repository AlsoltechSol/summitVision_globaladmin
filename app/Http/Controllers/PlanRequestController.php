<?php

namespace App\Http\Controllers;

use App\Exports\PlanRequestExport;
use App\Models\Company;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Excel;
use Illuminate\Support\Facades\DB;

class PlanRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (Auth::user()->type == 'super admin' || Gate::check('Manage Plan Request')) {

            $plan_requests = PlanRequest::query();

            if (!empty($request->start_date)) {
                $plan_requests->where('created_at', '>=', $request->start_date);
            }

            if (!empty($request->end_date)) {
                $plan_requests->where('created_at', '<=', $request->end_date);
            }

            if (!empty($request->company_id)) {
                $plan_requests->where('company_id', $request->company_id);
            }

            $plan_requests = $plan_requests->get();

            $companies = Company::select('id', 'name')->pluck('name', 'id')->toArray();
            $companies = ['' => 'All'] + $companies;

            // dd($plan_requests, $companies);
            return view('plan_request.index', compact('plan_requests', 'companies'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function export(Request $request)
    {
        $name = 'Orders_' . date('Y-m-d i:h:s');
        $data = Excel::download(new PlanRequestExport($request), $name . '.xlsx');


        return $data;
    }
    /*
     *@plan_id = Plan ID encoded
    */
    public function requestView($plan_id)
    {
        if (Auth::user()->type != 'super admin') {
            $planID = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
            $plan   = Plan::find($planID);

            if (!empty($plan)) {
                return view('plan_request.show', compact('plan'));
            } else {
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    /*
     * @plan_id = Plan ID encoded
     * @duration = what duration is selected by user while request
    */
    //-----------------------------------------------------------------------------
    //             this method is in api connecting global and tenent admins
    //-----------------------------------------------------------------------------
    // public function userRequest($plan_id)
    // {
    //     $objUser = Auth::user();

    //     if ($objUser->requested_plan == 0) {
    //         $planID = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);

    //         if (!empty($planID)) {
    //             PlanRequest::create([
    //                 'user_id' => $objUser->id,
    //                 'plan_id' => $planID,

    //             ]);

    //             // Update User Table

    //             $objUser['requested_plan'] = $planID;
    //             $objUser->update();

    //             return redirect()->back()->with('success', __('Request Send Successfully.'));
    //         } else {
    //             return redirect()->back()->with('error', __('Something went wrong.'));
    //         }
    //     } else {
    //         return redirect()->back()->with('error', __('You already send request to another plan.'));
    //     }
    // }
    //--------------------------------------------------------------------------------------------------
    /*
     * @id = Project ID
     * @response = 1(accept) or 0(reject)
    */
    public function acceptRequest($id, $response)
    {
        DB::beginTransaction();
        if (Auth::user()->type == 'super admin' || Gate::check('Manage Plan Request')) {
            
            $plan_request = PlanRequest::find($id);

            // dd($plan_request);
            $company = Company::where('id', $plan_request->company_id)->first();

            if (!empty($plan_request) && $company) {
                // $user = User::find($plan_request->user_id);

                if ($response == 1) {

                    // dd(Plan::all());
                    $plan       = Plan::find($plan_request->plan_id);
                    $price      = $plan->price;

                    // dd($plan);
                    if ($plan) {
                        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                        $new_order = Order::create([
                            'order_id' => $orderID,
                            'company_id' => $plan_request->company_id,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price,
                            'price_currency' => !empty(env('CURRENCY_CODE')) ? env('CURRENCY_CODE') : 'usd',
                            'txn_id' => '',
                            'payment_type' => __('Manually Upgrade By The Global Admin'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $plan_request->user_id,
                        ]);

                        if ($new_order) {
                            // dd($company);
                            $put_accept_data = Http::post($company->url . '/api/plan_request_accept_or_reject/' . $id . '/' . $response, [
                                'new_order' => json_encode($new_order)
                            ]);
                            $put_accept_data = $put_accept_data->json();
                            // dd($put_accept_data);
                        
                            if ($put_accept_data && $put_accept_data['status'] == 200) {
                                $plan_request->delete();
                                DB::commit();
                                return redirect()->back()->with('success', __($put_accept_data['message']));
                            } else {
                                return redirect()->back()->with('error', __($put_accept_data ? $put_accept_data['message'] : 'Something went wrong.'));
                            }
                        }

                        return redirect()->back()->with('error', __('Plan fail to upgrade.'));
                    } else {
                        return redirect()->back()->with('error', __('Plan Not found.'));
                    }
                } else {
                    $put_accept_data = Http::post($company->url . '/api/plan_request_accept_or_reject/' . $id . '/' . $response);

                    $put_accept_data = $put_accept_data->json();

                    // dd($put_accept_data );
                    if ($put_accept_data['status'] == 200) {
                        $plan_request->delete();
                        DB::commit();
                        return redirect()->back()->with('success', __($put_accept_data['message']));
                    } else {
                        return redirect()->back()->with('error', __($put_accept_data['message']));
                    }
                }
            } else {
                DB::rollback();
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
        } else {
            DB::rollback();
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /*
     * @id = User ID
    */
    public function cancelRequest($id)
    {

        $user = User::find($id);

        $user['requested_plan'] = '0';
        $user->update();

        PlanRequest::where('user_id', $id)->delete();

        return redirect()->back()->with('success', __('Request Canceled Successfully.'));
    }

    public function show(PlanRequest $planRequest)
    {
    }
}
