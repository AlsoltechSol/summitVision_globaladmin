<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\PlanRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {

        if (Auth::user()->type != 'super admin') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        // Validation
        $request->validate([
            'name'      => 'required|string|max:50',
            'email'     => 'required|email|max:50',
            'mobile'    => 'nullable|string|max:20',
            'password'  => 'required|string|max:255',
            'url'       => 'required',
        ]);

        $hashedPassword = Hash::make($request->input('password'));

        $new_company = Company::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
            'password' => $hashedPassword,
            'url' => $request->url
        ]);
        // $new_company = true;
        if ($new_company) {
            $response = Http::put($new_company->url . '/api/companies/0', [
                'name' => $new_company->name,
                'email' => $new_company->email,
                'mobile' => $new_company->mobile,
                'password' => $new_company->url,
                'company_id' => $new_company->id,
            ]);

            $message = $response->json();

            if ($response->successful()) {
                return redirect()->back()->with('success', __('Company created, ' . $message['message']));
            } else {
                return redirect()->back()->with('error', __('Faild to update company data error: ' . $message['message']));
            }
        } else {
            return redirect()->back()->with('success', __('Faild to create Company.'));
        }

        // Redirect or return a response as needed
        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    public function show($id)
    {
        if (Auth::user()->type != 'super admin') {
            return redirect()->back()->with('error', __('Access denied.'));
        }
        $company = Company::findOrFail($id);

        $companyInfo = Company::leftJoin('plan_requests', 'companies.id', '=', 'plan_requests.company_id')
            ->leftJoin('subscription_plans', 'plan_requests.subs_plan_id', '=', 'subscription_plans.id')
            ->select(
                'plan_requests.start_date',
                'plan_requests.transaction_id',
                'plan_requests.end_date',
                'plan_requests.status',
                'plan_requests.status as request_status',
                'subscription_plans.plan',
                'subscription_plans.total_users',
                'subscription_plans.duration',
                'subscription_plans.price'
            )
            ->where('companies.id', $id)
            ->orderByRaw("FIELD(plan_requests.status,  'active', 'pending', 'hold', 'expired', 'rejected')")
            ->get();
        // dd($companyInfo);
        return view('companies.show', compact('companyInfo', 'company'));
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);

        // dd($company);

        return view('companies.create', compact('company'));
    }



    public function update(Request $request, $id)
    {

        if (Auth::user()->type != 'super admin') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        // Validation
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'mobile' => 'nullable|string|max:20',
            'password' => 'nullable|string|max:255',
            'url' => 'required'
        ]);

        $hashedPassword = Hash::make($request->input('password'));

        $company = Company::findOrFail($id);

        // Update company attributes
        $company->name = $request->input('name');
        $company->email = $request->input('email');
        $company->mobile = $request->input('mobile');

        if ($request->has('password')) {
            $company->password = bcrypt($request->input('password'));
        }
        $company->url = $request->url;

        if ($company->save()) {
            $response = Http::put($company->url . '/api/companies/0', [
                'name' => $company->name,
                'email' => $company->email,
                'password' => $company->password,
                'company_id' => $company->id,
            ]);
            $message = $response->json();
            if ($response->successful()) {
                return redirect()->back()->with('success', __('Company updated, ' . $message['message']));
            } else {
                return redirect()->back()->with('error', __('Failed to update data to company admin panel, response: ' . $message['message']));
            }
        } else {
            return redirect()->back()->with('error', __('Failed to create company'));
        }
    }


    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();
            redirect()->back()->with('success', __('Company deleted successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Internal Server Error. Unable to delete the company.'));
        }
    }
}
