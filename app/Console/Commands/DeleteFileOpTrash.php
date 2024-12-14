<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\CompanyRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DeleteFileOpTrash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:fileop-trash {companyId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move the project directory to trash for a company';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            \Log::info("Executing command delete fileop-trash");
            $companyId = $this->argument('companyId');
            
            $company = CompanyRecord::find($companyId);
            \Log::info("CompanyRecord model", ["company" => $company]);

            if ($company) {
                $this->fileopTrash($company);
            }

            if(!$company){
                $company = Company::find($companyId);
                \Log::info("Company model", ["company" => $company]);

                if ($company) {
                    $this->fileopTrash($company);
                }
            }
        } catch (\Exception $e) {
            \Log::error(['message' => 'FileopTrash job failed', 'error' => $e->getMessage()]);
        }

        return Command::SUCCESS;
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
