<?php

namespace Monooso\Apposite\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class ApplyAtLeastOne extends ApplyMap implements Rule
{
    private $message;

    /**
     * Initialise the custom rule
     *
     * @param bool|callable $conditional
     * @param array|string $rules
     * @param $message
     */
    public function __construct($conditional, $rules, $message)
    {
        $key = 'when';

        (bool)(is_callable($conditional) ? $conditional() : $conditional)
            ? parent::__construct($key, [$key => $rules])
            : parent::__construct($key, []);

        $this->message = $message;
    }

    /**
     * Determine if the validation rule passes
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($this->rules)) {
            return true;
        }

        $rules = collect($this->rules);

        $checks = $rules->map(
            function ($rule) use ($attribute, $value) {
                $this->rules = $rule;
                $validator = $this->makeValidator($attribute, $value);

                return $validator->fails() ? $validator->errors()->all() : [];
            }
        )
            ->filter()
            ->values();

        if ($rules->count() == $checks->count()) {
            $this->messages = Arr::wrap($this->message);

            return false;
        }

        return true;
    }
}
