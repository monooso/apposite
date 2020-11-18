<?php

namespace Monooso\Apposite\Tests\Rules;

use Illuminate\Support\Facades\Validator;
use Monooso\Apposite\Rules\ApplyAtLeastOne;
use Orchestra\Testbench\TestCase;

class ApplyAtLeastOneTest extends TestCase
{
    /** @test */
    public function passes_when_one_of_the_rules_are_applied()
    {
        $data = ['first_name' => 'Bryn', 'last_name' => 'Abdullah'];

        $rules = [
            'first_name' => 'required',
            'last_name'  => new ApplyAtLeastOne(
                true,
                ['in:Abdullah,Shahad', 'in:Evans,Jones,Williams'],
                'message'
            ),
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
        $this->assertEmpty($validator->errors());
    }

    /** @test */
    public function fails_if_doesnt_met_any_rules()
    {
        $data = ['first_name' => 'Bryn', 'last_name' => 'Reem'];

        $rules = [
            'first_name' => 'required',
            'last_name'  => new ApplyAtLeastOne(
                true,
                ['in:Abdullah,Shahad', 'in:Evans,Jones,Williams'],
                'message'
            ),
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertNotEmpty($validator->errors());
        $this->assertEquals('message', $validator->errors()->get('last_name')[0]);
    }

    /** @test */
    public function it_ignores_optional_rules_when_the_condition_is_not_met()
    {
        $data = ['first_name' => 'Bryn', 'last_name' => 'Morgan'];
        $rules = ['last_name' => new ApplyAtLeastOne(false, ['in:Evans,Jones,Williams'], 'test')];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_works_with_pipe_delimited_optional_rules()
    {
        $data = ['first_name' => 'Bryn', 'last_name' => 'Reem'];

        $rules = [
            'first_name' => 'required',
            'last_name'  => new ApplyAtLeastOne(
                true,
                'in:Abdullah,Shahad|in:Evans,Jones,Williams',
                'message'
            ),
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('last_name'));
    }

    /** @test */
    public function it_works_with_a_callback_condition()
    {
        $data = ['first_name' => 'Bryn', 'last_name' => 'Morgan'];

        $rules = [
            'last_name' => new ApplyAtLeastOne(
                function () {
                    return true;
                },
                ['in:Abdullah,Shahad', 'in:Evans,Jones,Williams'],
                'test'
            ),
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('last_name'));
    }
}
