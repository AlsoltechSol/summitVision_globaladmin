<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyRecord extends Model
{
    use HasFactory;

    protected $table = 'companies_records';
    
    protected $fillable = [
        'id',
        'name', 'email', 'mobile', 'password', 'url', 'verification_token', 'company_name', 'server_setup_json', 'server_setup_started_at', 'DB_DATABASE',
        'DB_USERNAME',
        'DB_HOST',
        'sub_domain',
        'DB_PASSWORD',
        'create_domain_and_dir',
        'database_create_and_config',
        'fileop',
        'modify_env',
        'server_config_status',
        'is_verified',
        'created_at',
        'updated_at'
    ];

}
