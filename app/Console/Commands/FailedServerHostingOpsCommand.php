<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ParagonIE\Sodium\Compat;

class FailedServerHostingOpsCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:faildServerHostingOps';

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

    // private $setup_by_cron = 1;


    public function handle()
    {

        Log::info('Scheduler faild server ops executed at: ' . now());

        $companies = Company::where('server_setup_started_at', '<', now()->subMinutes(5))
            ->where('server_config_status', '!=', 1)
            ->where('is_verified', 1)
            ->get();
        // dd($companies);
        Log::info('Scheduler faild server ops company: ' . json_encode($companies));

        foreach ($companies as $key => $company) {
            // dd($company);
            $this->deleteSubdomain($company);
            $this->deleteDatabase($company);
            $this->deleteUsername($company);
            $this->fileopTrash($company);

            if ($company->setup_by_cron != 0) {
                $this->create_subdomain_and_dir($company);
                $this->createDatabaseAndPutData($company);
                $this->create_user($company);
                $this->set_privileges_on_database($company);
                $this->importSQLFile($company);
                $this->fileop($company);
                $this->upload_env($company);
                
            }

            $company->setup_by_cron = ($company->setup_by_cron < 3 || !$company->setup_by_cron) ? $company->setup_by_cron + 1 : 0;
            $company->save();            
            
        }

        return Command::SUCCESS;
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

    private function create_subdomain_and_dir($company)
    {
        $req = [
            'company' => json_encode($company)
        ];

        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/create_subdomain_and_dir', $req);
        $responseData = $response->json();
        // dd($responseData);
        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops: ' . json_encode($responseData));
            // return false;
        }

        return true;
    }

    private function createDatabaseAndPutData($company)
    {
        $req = [
            '_token' => @csrf_token(),
            'company' => json_encode($company)
        ];
        // dd($req);
        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/cpannel_create_database_put_data_cron', $req);
        $responseData = $response->json();
        // dd($responseData);
        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops: ' . json_encode($responseData));
            // return false;
        }

        return true;
    }

    private function create_user($company)
    {
        $req = [
            '_token' => @csrf_token(),
            'company' => json_encode($company),
            'data' => [
                'dbname' => $company->DB_DATABASE
            ]
        ];
        // dd($req);
        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/create_db_user', $req);
        $responseData = $response->json();
        // dd($responseData);
        // echo json_encode($responseData);
        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops: ' . json_encode($responseData));
            // return false;
        }

        return true;
    }

    private function set_privileges_on_database($company)
    {
        $req = [
            '_token' => @csrf_token(),
            'company' => json_encode($company),
            'data' => [
                'dbname' => $company->DB_DATABASE
            ]
        ];
        // dd($req);
        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/set_privileges_on_database', $req);
        $responseData = $response->json();
        // dd($responseData);
        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops: ' . json_encode($responseData));
            // return false;
        }

        return true;
    }

    private function importSQLFile($company)
    {
        $req = [
            '_token' => @csrf_token(),
            'company' => json_encode($company),
            'data' => [
                'dbname' => $company->DB_DATABASE
            ]
        ];
        // dd($req);
        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/importSQLFile', $req);
        $responseData = $response->json();
        // dd($responseData);
        // echo json_encode($responseData);
        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops: ' . json_encode($responseData));
            // return false;
        }

        return true;
    }

    private function fileop($company)
    {
        $req = [
            '_token' => @csrf_token(),
            'company' => json_encode($company),
            'data' => [
                'dbname' => $company->DB_DATABASE
            ]
        ];
        // dd($req);
        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/fileop', $req);
        $responseData = $response->json();
        // dd($responseData);
        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops: ' . json_encode($responseData));
            // return false;
        }

        return true;
    }

    private function upload_env($company)
    {
        $req = [
            '_token' => @csrf_token(),
            'company' => json_encode($company),
            'data' => [
                'dbname' => $company->DB_DATABASE
            ]
        ];
        // dd($req);
        $response = Http::withOptions(['verify' => false])->get(env('PUBLIC_INSTENCE_URL') . '/upload_env', $req);
        $responseData = $response->json();

        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops: ' . json_encode($responseData));
            // return false;
        }

        return true;
    }
}
