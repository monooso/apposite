<?php

namespace Monooso\Apposite\Rules;

use Illuminate\Contracts\Validation\Rule;

class ApplyUnless extends ApplyWhen implements Rule
{
    /**
     * ApplyUnless constructor.
     *
     * @param  bool|callable  $conditional
     * @param  array|string   $rules
     */
    public function __construct($conditional, $rules)
    {
        parent::__construct($conditional, $rules);

        $this->conditional = ! $this->conditional;
    }
}
