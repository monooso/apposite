<?php

namespace Monooso\Apposite\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;

class ApplyMap implements Rule
{
    /** @var array */
    protected $messages;

    /** @var array|string */
    protected $rules;

    /**
     * Initialise the custom rule
     *
     * @param string $key
     * @param array  $map
     */
    public function __construct(string $key, array $map)
    {
        $this->rules = (array_key_exists($key, $map)) ? $map[$key] : [];
        $this->messages = [];
    }

    /**
     * Determine if the validation rule passes
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validator = $this->makeValidator($attribute, $value);

        $this->messages = $validator->fails() ? $validator->errors()->all() : [];

        return count($this->messages) === 0;
    }

    /**
     * Build the validator instance, to validate the given attribute and value
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return ValidatorContract
     */
    protected function makeValidator(string $attribute, $value): ValidatorContract
    {
        return Validator::make([$attribute => $value], [$attribute => $this->rules]);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return $this->messages;
    }
}
