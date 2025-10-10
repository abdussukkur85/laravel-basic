<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoBadWords implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $badWords = [
        'badword1', 'badword2', 'fuck', 'shit'
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->badWords as $word) {
            if (stripos($value, $word) !== false) {
                $fail("The {$attribute} contains inappropriate language.");
            }
        }
    }
}
