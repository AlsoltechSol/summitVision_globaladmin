<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Plan;
use App\Models\PlanRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlanRequestExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $usr = \Auth::user();
        if ($usr->can('Manage Plan Request') || $usr->type == 'super admin') {
            $plan_requests = PlanRequest::query();

            if (!empty($this->request->start_date) && $this->request->start_date != 'null') {
                $plan_requests->where('created_at', '>=', $this->request->start_date);
            }

            if (!empty($this->request->end_date) && $this->request->end_date != 'null') {
                $plan_requests->where('created_at', '<=', $this->request->end_date);
            }

            if (!empty($this->request->company_id) && $this->request->company_id != 'null') {
                $plan_requests->where('company_id', $this->request->company_id);
            }

            $plan_requests = $plan_requests->get();


            // dd($plan_requests);
            foreach ($plan_requests as &$plan_request) {
                $company = Company::find($plan_request['company_id']);
                $plan = Plan::select('name')->where('id', $plan_request->plan_id)->first();
                $plan_request['user_id'] = $company->name;
                $plan_request['plan_id'] = $plan->name;

                    $arr = ['updated_at'];

                foreach ($arr as $key) {
                    unset($plan_request[$key]);
                }
            }

            return $plan_requests;
        }
    }

    public function headings(): array
    {
        return [
            "PLAN REQUEST ID",
            "COMPANY ID",
            "COMPANY ADMIN NAME",
            "PLAN NAME",
            "DURATION",
            "REQUEST CREATED AT",
        ];
    }
}
