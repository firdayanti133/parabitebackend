<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^\+?[0-9]{8,15}$/', trim($value))) {
            $fail('The :attribute must contain 8 to 15 digits and may start with +.');
        }
    }
}
