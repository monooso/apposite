<?php

namespace Monooso\Apposite\Tests\Feature\Rules;

use Illuminate\Support\Facades\Validator;
use Monooso\Apposite\Rules\ApplyUnless;
use Orchestra\Testbench\TestCase;

class ApplyUnlessTest extends TestCase
{
    /** @test */
    public function it_applies_optional_rules_when_the_condition_is_met()
    {
        $data = ['first_name' => 'Bryn', 'last_name' => 'Morgan'];

        $rules = [
            'first_name' => 'required',
            'last_name'  => new ApplyUnless(false, ['required', 'in:Evans,Jones,Williams']),
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('last_name'));
    }

    /** @test */
    public function it_ignores_optional_rules_when_the_condition_is_not_met()
    {
        $data = ['first_name' => 'Bryn', 'last_name' => 'Morgan'];
        $rules = ['last_name' => new ApplyUnless(true, ['in:Evans,Jones,Williams'])];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }
}
