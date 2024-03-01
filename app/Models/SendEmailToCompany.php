<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendEmailToCompany extends Model
{
    use HasFactory;

    protected $table = "send_email_to_companies";
    
    protected $fillable = [
        'company_id',
        'sent_date',
        'is_demo_acc',
    ];
}
