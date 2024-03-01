<?php

namespace App\Console\Commands;

use App\Mail\SendPlanExpireMail;
use App\Models\Company;
use App\Models\SendEmailToCompany;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPlanExpireMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendPlanExpireMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will send mail to those companies whose plans will be expired in next 7 days. or demo account will be deleted.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $today = Carbon::now();
        $sevendaysLaterDate = $today->addDays(7);

        $companies = Company::where('plan_expire_date', '<=', $sevendaysLaterDate)->select('id', 'email', 'company_name', 'url', 'plan', 'plan_expire_date')->get();

        foreach ($companies as $key => $company) {
            $mail_sent = SendEmailToCompany::where('sent_date', '!=', $today)
                ->where('company_id', '=', $company->id)
                ->latest()
                ->first();

            if (!$mail_sent) {
                $send_mail = Mail::to($company['email'])->send(new SendPlanExpireMail($company));
                $mail_sent = new SendEmailToCompany();
                $mail_sent->company_id = $company->id;
                $mail_sent->sent_date  = $today;
                if ($company->plan == null) {
                    $mail_sent->is_demo_acc = 1;
                }
                $mail_sent->save();
            }
        }

        return Command::SUCCESS;
    }
}
