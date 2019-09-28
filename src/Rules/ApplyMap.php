<?php

namespace Monooso\Apposite\Rules;

use Illuminate\Contracts\Validation\Rule;

class ApplyMap extends Apply implements Rule
{
    /**
     * Initialise the custom rule
     *
     * @param string $key
     * @param array  $map
     */
    public function __construct(string $key, array $map)
    {
        (array_key_exists($key, $map))
            ? parent::__construct(true, $map[$key])
            : parent::__construct(false, []);
    }
}
