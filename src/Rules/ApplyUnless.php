<?php

namespace Monooso\Apposite\Rules;

use Illuminate\Contracts\Validation\Rule;

class ApplyUnless extends ApplyWhen implements Rule
{
    /**
     * Initialise the custom rule
     *
     * @param bool|callable $conditional
     * @param array|string  $rules
     */
    public function __construct($conditional, $rules)
    {
        parent::__construct($conditional, $rules);

        $this->conditional = ! $this->conditional;
    }
}
