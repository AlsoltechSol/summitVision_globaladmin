<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueCompanyName implements Rule
{
    public function passes($attribute, $value)
    {
        // Normalize the company name
        $normalizedValue = strtolower(str_replace(' ', '', $value));

        // Check if the normalized company name exists in the sub_domain column
        $exists = DB::table('companies')
        // ->where('is_verified', 1)
        ->where(function ($query) use ($normalizedValue, $value) {
            $query->whereRaw('LOWER(REPLACE(sub_domain, " ", "")) = ?', [$normalizedValue])
                  ->orWhere('company_name', $value);
        })
        ->exists();

        return !$exists;
    }

    public function message()
    {
        return 'The :attribute has already been taken.';
    }
}
