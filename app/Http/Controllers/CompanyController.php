<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\PlanRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

        try {
            //code...

            if (Auth::user()->type != 'super admin') {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
            // Validation
            $request->validate([
                'name'               => 'required|string|max:50',
                'email'              => 'required|email|max:50|unique:companies,email',
                'mobile'             => 'nullable|string|max:20',
                'password'           => 'required|string|max:255',
                'company_name'       => 'required|unique:companies,company_name',
            ]);

            $hashedPassword = Hash::make($request->input('password'));
            $str = Str::random(200);
            $company = Company::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $hashedPassword,
                'company_name' => $request->company_name,
                'verification_token' => $str
            ]);
            // $new_company = true;
            if ($company) {
                // $response = Http::put($new_company->url . '/api/companies/0', [
                //     'name' => $new_company->name,
                //     'email' => $new_company->email,
                //     'mobile' => $new_company->mobile,
                //     'password' => $new_company->url,
                //     'company_id' => $new_company->id,
                // ]);

                // $message = $response->json();

                // if ($response->successful()) {
                return view('companies.server_setup', compact('company'));
                // } else {
                //     return redirect()->back()->with('error', __('Faild to update company data error: ' . $message['message']));
                // }
            } else {
                return redirect()->back()->with('success', __('Faild to create Company.'));
            }

            // Redirect or return a response as needed
            return redirect()->route('companies.index')->with('success', 'Company created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('companies.index')->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $company = Company::where('id', $id)->first();

        return view('companies.show', compact('company'));
    }

    public function company_settings($id)
    {
        try {

            if (Auth::user()->type != 'super admin') {
                return redirect()->back()->with('error', __('Access denied.'));
            }
            $company = Company::findOrFail($id);

            if ($company) {
                $url = $company->url . '/api/get_settings_details';
                $options = [
                    'http' => [
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'ignore_errors' => true,
                    ],
                ];
                $context = stream_context_create($options);
                $json_data = file_get_contents($url, false, $context);
                $data = json_decode($json_data, true);

                // Access the data as needed
                $setting = $data['setting'];
                $file_type = $data['file_type'];
                $local_storage_validations = $data['local_storage_validations'];
                $s3_storage_validations = $data['s3_storage_validations'];
                $wasabi_storage_validations = $data['wasabi_storage_validations'];

                return view('companies.company_settings', compact('company', 'setting', 'file_type', 'local_storage_validations', 's3_storage_validations', 'wasabi_storage_validations'));
            } else {
                return redirect()->back()->with('error', __('Company Not Found.'));
            }
            //code...
        } catch (\Exception $th) {
            return redirect()->route('companies.index')->with('error', 'Something went wrong, this company pannel may not configured correctly');
        }
    }

    public function company_storage_setting_store(Request $request, $company)
    {
        // dd($company);

        $company = Company::find($company);
        if (!$company) {
            return redirect()->back()->with('error', __('Company Not Found.'));
        }
        $response = Http::post($company->url . '/api/storage-settings', $request);

        $response = $response->json();

        if ($response['status'] == 200) {

            return redirect()->back()->with('success', __($response['message']));
        } else {
            return redirect()->back()->with('error', __($response['message']));
        }
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);

        // dd($company);

        return view('companies.create', compact('company'));
    }



    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        if (Auth::user()->type != 'super admin') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:50',
                'email' => 'required|email|max:50',
                'mobile' => 'nullable|string|max:20',
                'password' => 'nullable|string|max:255',
                'url' => 'required'
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $hashedPassword = Hash::make($request->input('password'));

        $company = Company::findOrFail($id);

        // Update company attributes
        $company->name = $request->input('name');
        $company->email = $request->input('email');
        $company->mobile = $request->input('mobile');
        $company->company_name = $request->input('company_name');

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

            // dd($message);
            if ($response->successful()) {
                DB::commit();
                return redirect()->back()->with('success', __('Company updated, ' . $message['message']));
            } else {
                DB::rollback();
                return redirect()->back()->with('error', __('Failed to update data to company admin panel, response: ' . isset($message['message']) ? $message['message'] : ''));
            }
        } else {
            DB::rollback();
            return redirect()->back()->with('error', __('Failed to create company'));
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);

            $deleteFaildServerSetupCompany = $request->has('delete_faild_server_setup_company');
            $deleteSubdomain = $request->input('delete_subdomain') == 1;
            $deleteDatabase = $request->input('delete_database') == 1;
            $deleteProjectDirectory = $request->input('delete_project_directory') == 1;

            if (!$deleteFaildServerSetupCompany) {
                $this->deleteSubdomain($company);
                $this->deleteDatabase($company);
                $this->deleteUsername($company);
                $this->fileopTrash($company);

                $company->delete();
                return redirect()->back()->with('success', __('Company deleted successfully'));
            }

            if ($deleteSubdomain) {
                $this->deleteSubdomain($company);
            }

            if ($deleteDatabase) {
                $this->deleteDatabase($company);
                $this->deleteUsername($company);
            }

            if ($deleteProjectDirectory) {
                $this->fileopTrash($company);
            }
            $company->delete();

            return redirect()->route('companies.index')->with('success', __('Company deleted successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Internal Server Error. Unable to delete the company.'));
        }
    }


    private function deleteSubdomain($company)
    {

        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/deleteSubdomain', $company);
        $responseData = $response->json();
        //    dd($responseData);

        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops: ' . json_encode($responseData));
        }

        return true;
    }

    private function deleteDatabase($company)
    {
        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/deleteDatabase', $company);
        $responseData = $response->json();
        // dd($responseData);
        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops: ' . json_encode($responseData));
            // return false;
        }

        return true;
    }

    private function deleteUsername($company)
    {
        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/deleteUsername', $company);
        $responseData = $response->json();

        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops: ' . json_encode($responseData));
            // return false;
        }

        return true;
    }

    private function fileopTrash($company)
    {
        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/file_op_trash', $company);
        $responseData = $response->json();
        // dd($responseData);
        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops: ' . json_encode($responseData));
            // return false;
        }
        $company->DB_PASSWORD = NULL;
        return true;
    }
}
