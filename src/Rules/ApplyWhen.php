<?php

namespace Monooso\Apposite\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ApplyWhen implements Rule
{
    /** @var bool */
    protected $conditional;

    /** @var array */
    protected $messages;

    /** @var array */
    protected $rules;

    /**
     * ApplyWhen constructor.
     *
     * @param  bool|callable  $conditional
     * @param  array|string   $rules
     */
    public function __construct($conditional, $rules)
    {
        $this->messages = [];
        $this->conditional = (bool)(is_callable($conditional) ? $conditional() : $conditional);
        $this->rules = $rules;
    }

    /**
     * Determine if the validation rule passes
     *
     * @param  string  $attribute
     * @param  mixed   $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (! $this->conditional) {
            return true;
        }

        $validator = Validator::make([$attribute => $value], [$attribute => $this->rules]);

        if ($validator->passes()) {
            $this->messages = [];
            return true;
        }

        $this->messages = $validator->errors()->get($attribute);
        return false;
    }

    /**
     * Get the validation error message
     *
     * @return array
     */
    public function message()
    {
        return $this->messages;
    }
}
