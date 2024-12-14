<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\CompanyRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DeleteDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:delete-database {companyId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the database associated with a company';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            \Log::info("Execute the console command delete database");
            $companyId = $this->argument('companyId');
            
            $company = CompanyRecord::find($companyId);
            \Log::info("CompanyRecord model", ["company" => $company]);

            if ($company) {
                $this->deleteDatabase($company);
            }

            
            if(!$company){
                $company = Company::find($companyId);
                \Log::info("Company model", ["company" => $company]);

                if ($company) {
                    $this->deleteDatabase($company);
                }
            }

            \Log::info("Database deleted successfully");
        } catch (\Exception $e) {
            \Log::error(['message' => 'DeleteDatabase job failed', 'error' => $e->getMessage()]);
        }

        return Command::SUCCESS;
    }

    function deleteDatabase($company)
    {
        $response = Http::get(env('PUBLIC_INSTENCE_URL') . '/deleteDatabase', $company);
        $responseData = $response->json();
        // dd($responseData);
        if ($responseData['status'] !== 200) {
            \Log::error('An error occurred in Scheduler faild server ops (hits from destroy op):: ' . json_encode($responseData));
            // return false;
        }

        return true;
    }
}
