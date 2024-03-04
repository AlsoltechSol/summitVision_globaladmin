<?php

namespace App\Console\Commands;

use App\Mail\SendAccountCreationMail;
use App\Mail\SendPlanExpireMail;
use App\Models\Company;
use App\Models\SendEmailToCompany;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AccountCreationSuccessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AccountCreationSuccessCommand';

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
        Log::info('started AccountCreationSuccessCommand');
        $today = Carbon::now();
        $fiveMinutesAgo = $today->subMinutes(5);


        $companies = Company::where('server_setup_started_at', '<=', $fiveMinutesAgo)
        ->where('is_verified', 1)->where('server_config_status', 1)->select('id', 'email', 'company_name', 'url', 'plan', 'plan_expire_date')->get();
        

        foreach ($companies as $key => $company) {
            $mail_sent = SendEmailToCompany::where('company_id', '=', $company->id)
                ->where('reason', '=', 'acc_created')
                ->latest()
                ->first();

            if (!$mail_sent) {
                $send_mail = Mail::to($company['email'])->send(new SendAccountCreationMail($company));
                $mail_sent = new SendEmailToCompany();
                $mail_sent->company_id = $company->id;
                $mail_sent->sent_date  = $today;
                $mail_sent->reason     = 'acc_created';
                
                if ($company->plan == null) {
                    $mail_sent->is_demo_acc = 1;
                }
                $mail_sent->save();
            }
        }

        return Command::SUCCESS;
    }
}
