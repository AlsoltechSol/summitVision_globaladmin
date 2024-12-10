<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeleteProjectDirectoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $company;
    
    public function __construct($company)
    {
        $this->company = $company;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->fileopTrash($this->company);

    }

    private function fileopTrash($company)
    {
        // dd('');
        $response = Http::get(env('PUBLIC_INSTENCE_URL') . '/file_op_trash', ['sub_domain' => $company->sub_domain]);
        $responseData = $response->json();
        // dd($responseData);
        if ($responseData['status'] !== 200) {
            Log::error('An error occurred in Scheduler faild server ops (hits from destroy op): ' . json_encode($responseData));
            // return false;
        }
        $company->DB_PASSWORD = NULL;
        return true;
    }
}
