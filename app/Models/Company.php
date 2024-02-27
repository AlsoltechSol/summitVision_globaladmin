<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';
    
    protected $fillable = [
        'name', 'email', 'mobile', 'password', 'url', 'verification_token', 'company_name', 'server_setup_json', 'server_setup_started_at'
    ];

}
