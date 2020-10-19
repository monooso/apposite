<?php

namespace Monooso\Apposite\Tests\Rules;

use Illuminate\Support\Facades\Validator;
use Monooso\Apposite\Rules\ApplyMap;
use Orchestra\Testbench\TestCase;

class ApplyMapTest extends TestCase
{
    /** @test */
    public function it_applies_the_correct_rules_from_the_map()
    {
        $data = ['delivery_service' => 'one_day'];

        $map = [
            'fedex'      => 'required|in:one_day,two_day',
            'ups'        => 'required|in:express,standard',
            'royal_mail' => 'required|in:two_day,someday',
        ];

        $rules = ['delivery_service' => new ApplyMap('ups', $map)];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('delivery_service'));
    }

    /** @test */
    public function it_does_not_apply_any_rules_if_the_map_key_does_not_exist()
    {
        $data = ['delivery_service' => 'one_day'];
        $map = ['fedex' => 'required|in:one_day,two_day'];
        $rules = ['delivery_service' => new ApplyMap('ups', $map)];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }
}
