<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\CompanyRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DeleteSubdomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:delete-subdomain {companyId}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
  

    public function handle()
    {
        try {
            \Log::info("Executing command delete-subdomain");

            $companyId = $this->argument('companyId');
           
            $company = CompanyRecord::find($companyId);

            \Log::info("CompanyRecord model", ["company" => $company]);

            if ($company) {
                $this->deleteSubdomain($company);
            }

            if(!$company){
                $company = Company::find($companyId);

                \Log::info("Company model", ["company" => $company]);

                if ($company) {
                    $this->deleteSubdomain($company);
                }
            }


        } catch (\Exception $e) {
            \Log::error(['message' => 'DeleteSubdomain job failed', 'error' => $e->getMessage()]);
        }

        return Command::SUCCESS;
    }

    function deleteSubdomain($company)
    {

        $response = Http::get(env('PUBLIC_INSTENCE_URL') . '/deleteSubdomain', ['sub_domain' => $company['sub_domain']]);
        $responseData = $response->json();
        //    dd($responseData);

        if ($responseData['status'] !== 200) {
            \Log::error('An error occurred in Scheduler faild server ops (hits from destroy op): ' . json_encode($responseData));
        }

        return true;
    }
}
