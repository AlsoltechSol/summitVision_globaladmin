<?php

namespace App\Jobs;

use App\Models\Company; // Adjust the namespace based on your Company model's location
use App\Models\CompanyRecord;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Log;

class FileopTrashJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $companyId;

    /**
     * Create a new job instance.
     */
    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Call the fileopTrash logic here
            \Log::info(['message' => 'Running fileopTrash job for company', 'company_id' => $this->companyId]);

            // You can copy the logic from your fileopTrash function here or call the method directly if it's reusable
            // app()->call('App\Http\Controllers\CompanyController@fileopTrash', ['company' => $this->company]);
            $company = CompanyRecord::find($this->companyId);

            if ($company) {
                $this->fileopTrash($company);
            }

            // \Log::info(['message' => 'FileopTrash job completed for company', 'company_id' => $this->company->id]);

        } catch (Exception $e) {
            \Log::error(['message' => 'FileopTrash job failed', 'error' => $e->getMessage()]);
        }
    }

    private function fileopTrash($company)
    {
        // dd('');
        $response = Http::get(env('PUBLIC_INSTENCE_URL') . '/file_op_trash', ['sub_domain' => $company->sub_domain]);
        $responseData = $response->json();
        // dd($responseData);
        if ($responseData['status'] !== 200) {
            \Log::error('An error occurred in Scheduler faild server ops (hits from destroy op): ' . json_encode($responseData));
            // return false;
        }
        $company->DB_PASSWORD = NULL;
        return true;
    }
}
