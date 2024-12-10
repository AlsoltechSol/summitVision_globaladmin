<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CompanyRecord;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Log;

class DeleteSubdomainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    public function handle()
    {
        try {
            \Log::info(['message' => 'Deleting subdomain', 'company_id' => $this->companyId]);

            // Replace this with your deleteSubdomain logic
            // app()->call('App\Http\Controllers\CompanyController@deleteSubdomain', ['company' => $this->company]);
            $company = CompanyRecord::find($this->companyId);

            if ($company) {
                $this->deleteSubdomain($company);
            }
        } catch (Exception $e) {
            \Log::error(['message' => 'DeleteSubdomain job failed', 'error' => $e->getMessage()]);
        }
    }

    function deleteSubdomain($company)
    {

        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/deleteSubdomain', $company);
        $responseData = $response->json();
        //    dd($responseData);

        if ($responseData['status'] !== 200) {
            \Log::error('An error occurred in Scheduler faild server ops (hits from destroy op): ' . json_encode($responseData));
        }

        return true;
    }
}
