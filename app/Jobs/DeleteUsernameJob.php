<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CompanyRecord;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class DeleteUsernameJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \Log::info(['message' => 'Deleting subdomain', 'company_id' => $this->companyId]);

            // Replace this with your deleteSubdomain logic
            // app()->call('App\Http\Controllers\CompanyController@deleteUsername', ['company' => $this->company]);
            $company = CompanyRecord::find($this->companyId);

            if ($company) {
            $this->deleteUsername($company);
            }

        } catch (Exception $e) {
            \Log::error(['message' => 'DeleteSubdomain job failed', 'error' => $e->getMessage()]);
        }
    }

    function deleteUsername($company)
    {
        $response = Http::get(env('PUBLIC_INSTENCE_URL') . '/deleteUsername', $company);
        $responseData = $response->json();

        if ($responseData['status'] !== 200) {
            \Log::error('An error occurred in Scheduler faild server ops (hits from destroy op):: ' . json_encode($responseData));
            // return false;
        }

        return true;
    }

}
