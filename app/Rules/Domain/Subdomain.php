<?php

namespace App\Rules\Domain;

use Illuminate\Contracts\Validation\Rule;

class Subdomain implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!preg_match('/^[A-Za-z0-9](?:[A-Za-z0-9\-]{0,61}[A-Za-z0-9])?$/', $value)) {
            return false;
        }

        if (in_array($value, Reserved::$subdomains)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not available';
    }
}
