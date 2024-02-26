<?php

namespace App\Http\Controllers\Web_Api;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Models\Company;
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
        $str = Str::random(40);

        if ($company == null) {
            $company = Company::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password),
                'verification_token' => $str,
            ]);
        }
        $company['verification_link'] = env('PUBLIC_INSTENCE_URL')."/verify-email?token=".$company['verification_token'];
        $company = json_decode(json_encode($company), true);

        try {
           $res = Mail::to($company['email'])->send(new EmailVerification($company));
        //    return response()->json(['status' => 200, 'message' => 'Success ', 'company' => $res]);

        } catch (\Exception $e) {
            return response()->json(['status' => 422, 'message' => $e->getMessage()]);
        }

        return response()->json(['status' => 200, 'message' => 'Success ', 'company' => $company]);
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
