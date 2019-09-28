<?php

namespace Monooso\Apposite\Rules;

use Illuminate\Contracts\Validation\Rule;

class ApplyUnless extends Apply implements Rule
{
    /**
     * @inheritDoc
     */
    public function __construct($conditional, $rules)
    {
        parent::__construct($conditional, $rules);

        $this->shouldApply = ! $this->shouldApply;
    }
}
