<?php

namespace Monooso\Apposite\Rules;

use Illuminate\Contracts\Validation\Rule;

class ApplyUnless extends ApplyMap implements Rule
{
    /**
     * Initialise the custom rule
     *
     * @param bool|callable $conditional
     * @param array|string  $rules
     */
    public function __construct($conditional, $rules)
    {
        $key = 'unless';

        (bool)(is_callable($conditional) ? $conditional() : $conditional)
            ? parent::__construct($key, [])
            : parent::__construct($key, [$key => $rules]);
    }
}
