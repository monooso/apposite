<?php

namespace Monooso\Apposite\Tests\Rules;

use Illuminate\Support\Facades\Validator;
use Monooso\Apposite\Rules\ApplyWhen;
use Orchestra\Testbench\TestCase;

class ApplyWhenTest extends TestCase
{
    /** @test */
    public function it_applies_optional_rules_when_the_condition_is_met()
    {
        $data = ['first_name' => 'Bryn', 'last_name' => 'Morgan'];

        $rules = [
            'first_name' => 'required',
            'last_name'  => new ApplyWhen(true, ['required', 'in:Evans,Jones,Williams']),
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('last_name'));
    }

    /** @test */
    public function it_ignores_optional_rules_when_the_condition_is_not_met()
    {
        $data = ['first_name' => 'Bryn', 'last_name' => 'Morgan'];
        $rules = ['last_name' => new ApplyWhen(false, ['in:Evans,Jones,Williams'])];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_works_with_pipe_delimited_optional_rules()
    {
        $data = ['age' => 9.99];
        $rules = ['age' => new ApplyWhen(true, 'numeric|min:10')];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('age'));
    }

    /** @test */
    public function it_works_with_a_callback_condition()
    {
        $data = ['first_name' => 'Bryn', 'last_name' => 'Morgan'];

        $rules = [
            'last_name' => new ApplyWhen(function () {
                return true;
            }, ['required', 'in:Evans,Jones,Williams']),
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('last_name'));
    }

    /** @test */
    public function it_returns_an_array_of_error_messages()
    {
        $data = ['age' => 5];
        $rules = ['age' => ['required', new ApplyWhen(true, 'numeric|lt:5|gt:5')]];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertCount(1, $validator->errors()->messages());
        $this->assertArrayHasKey('age', $validator->errors()->messages());

        $this->assertSame(
            ['The age must be less than 5.', 'The age must be greater than 5.'],
            $validator->errors()->get('age')
        );
    }
}
