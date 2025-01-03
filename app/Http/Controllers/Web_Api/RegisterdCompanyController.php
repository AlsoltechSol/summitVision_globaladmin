<?php

namespace App\Http\Controllers\Web_Api;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Models\Company;
use App\Rules\UniqueCompanyName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class RegisterdCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('companies')->where(function ($query) {
                        $query->where('is_verified', 1);
                    }),
                ],
                'mobile' => 'required',
                'company_name' => [
                    'required',
                    'string',
                    // Rule::unique('companies')->where(function ($query) {
                    //     $query->where('is_verified', 1);
                    // }),
                    new UniqueCompanyName,
                ],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('companies')->where(function ($query) {
                        $query->where('is_verified', 1);
                    }),
                ],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status' => 422, 'message' => $messages->first()]);
        }

        $company = Company::where('email', $request->email)->first();
        $str = Str::random(200);
        // return response()->json(['status' => 422, 'message' => $request->company_name]);

        if ($company == null) {
            $company = Company::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password),
                'verification_token' => $str,
                'company_name' => $request->company_name,
            ]);
        }
        $company['verification_link'] = env('PUBLIC_INSTENCE_URL') . "/verify-email-address?token=" . $company['verification_token'];
        $company = json_decode(json_encode($company), true);

        try {
            $res = Mail::to($company['email'])->send(new EmailVerification($company));
            //    return response()->json(['status' => 200, 'message' => 'Success ', 'company' => $res]);

        } catch (\Exception $e) {
            return response()->json(['status' => 422, 'message' => $e->getMessage()]);
        }

        return response()->json(['status' => 200, 'message' => 'Success ', 'company' => $company]);
    }

    public function register_company_resend_email(Request $request)
    {

        $validator = \Validator::make(
            $request->all(),
            [
                'email' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status' => 422, 'message' => $messages->first()]);
        }

        $company = Company::where('email', $request->email)->first();

        $company['verification_link'] = env('PUBLIC_INSTENCE_URL') . "/verify-email-address?token=" . $company['verification_token'];
        $company = json_decode(json_encode($company), true);

        try {
            $res = Mail::to($company['email'])->send(new EmailVerification($company));
            //    return response()->json(['status' => 200, 'message' => 'Success ', 'company' => $res]);

        } catch (\Exception $e) {
            return response()->json(['status' => 422, 'message' => $e->getMessage()]);
        }

        return response()->json(['status' => 200, 'message' => 'Success ', 'company' => $company]);
    }

    public function verify_token(Request $request)
    {

        $validator = \Validator::make(
            $request->all(),
            [
                'token' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status' => 422, 'message' => $messages->first()]);
        }

        $company = Company::where('verification_token', $request->token)->first();

        if ($company != null) {

            return response()->json(['status' => 200, 'message' => 'Success ', 'company' => $company]);
        } else {
            return response()->json(['status' => 422, 'message' => 'Company Not found or token expired', 'company' => $company]);
        }
    }

    public function update_company_setup(Request $request)
    {

        try {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'sometimes|string',
                    'email' => 'sometimes|email',
                    'mobile' => 'sometimes|numeric',
                    'password' => 'sometimes|string',
                    'url' => 'sometimes',
                    'verification_token' => 'sometimes|string',
                    'company_name' => 'sometimes|string',
                    'server_setup_started_at' => 'nullable',
                    'DB_DATABASE' => 'sometimes',
                    'DB_USERNAME' => 'sometimes',
                    'DB_HOST' => 'sometimes',
                    'sub_domain' => 'sometimes',
                    'DB_PASSWORD' => 'sometimes',
                    'create_domain_and_dir' => 'sometimes',
                    'database_create_and_config' => 'sometimes',
                    'fileop' => 'sometimes',
                    'modify_env' => 'sometimes',
                    'server_config_status' => 'sometimes',
                    'is_verified' => 'sometimes',
                ]
            );
            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()->first()]);
            }

            $validatedData = $validator->validated();

            $company = Company::findOrFail($request->id);

            $filteredData = array_filter($validatedData, function ($value) {
                return $value !== null && $value !== '';
            });

            $company->fill($filteredData)->save();

            return response()->json(['status' => 200, 'company' => $company, 'message' => 'Company setup updated successfully']);
        } catch (\Exception $th) {
            return response()->json(['status' => 422, 'message' => $th->getMessage()]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
