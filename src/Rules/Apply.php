<?php

namespace Monooso\Apposite\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;

abstract class Apply implements Rule
{
    /** @var bool */
    protected $shouldApply;

    /** @var array */
    protected $messages;

    /** @var array|string */
    protected $rules;

    /**
     * Initialise the custom rule
     *
     * @param bool|callable $conditional
     * @param array|string  $rules
     */
    public function __construct($conditional, $rules)
    {
        $this->reset();
        $this->rules = $rules;
        $this->shouldApply = (bool)(is_callable($conditional) ? $conditional() : $conditional);
    }

    /**
     * Reset the instance variables
     */
    protected function reset()
    {
        $this->shouldApply = false;
        $this->messages = [];
        $this->rules = [];
    }

    /**
     * Determine if the validation rule passes
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->messages = $this->validateIf($this->shouldApply, $this->makeValidator($attribute, $value));

        return count($this->messages) === 0;
    }

    /**
     * Run the given validator, if the conditional evaluates to true
     *
     * @param bool              $shouldApply
     * @param ValidatorContract $validator
     *
     * @return array
     */
    protected function validateIf(bool $shouldApply, ValidatorContract $validator): array
    {
        return ($shouldApply && $validator->fails()) ? $validator->errors()->all() : [];
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
        return Validator::make(
            $this->makeValidatorDataArray($attribute, $value),
            $this->makeValidatorRulesArray($attribute, $this->getRules())
        );
    }

    /**
     * Build the validator data array
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return array
     */
    protected function makeValidatorDataArray(string $attribute, $value): array
    {
        return [$attribute => $value];
    }

    /**
     * Build the validator rules array
     *
     * @param string       $attribute
     * @param array|string $rules
     *
     * @return array
     */
    protected function makeValidatorRulesArray(string $attribute, $rules): array
    {
        return [$attribute => $rules];
    }

    /**
     * Get the validation rules to apply
     *
     * @return array|string
     */
    protected function getRules()
    {
        return $this->rules;
    }

    /**
     * Get the validation error messages
     *
     * @return array
     */
    public function message()
    {
        return $this->messages;
    }
}
