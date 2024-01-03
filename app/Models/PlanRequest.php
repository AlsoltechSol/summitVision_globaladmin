<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanRequest extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'duration',
        'company_id',
    ];

    public function plan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }
}
