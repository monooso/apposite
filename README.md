# Apposite

<p>
  <a href="https://travis-ci.org/monooso/apposite"><img src="https://img.shields.io/travis/monooso/apposite/master.svg" alt="Build Status"/></a>
  <a href="https://scrutinizer-ci.com/g/monooso/apposite"><img src="https://img.shields.io/scrutinizer/g/monooso/apposite.svg" alt="Quality Score"/></a>
  <a href="https://scrutinizer-ci.com/g/monooso/apposite"><img src="https://img.shields.io/scrutinizer/coverage/g/monooso/apposite.svg" alt="Coverage"/></a>
  <a href="https://packagist.org/packages/monooso/apposite"><img src="https://poser.pugx.org/monooso/apposite/v/stable.svg" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/monooso/apposite"><img src="https://poser.pugx.org/monooso/apposite/license.svg" alt="License"></a>
</p>

## About Apposite
Apposite makes it easy to conditionally apply Laravel validation rules, even when you don't have access to [the validator instance](https://laravel.com/docs/validation#conditionally-adding-rules).

## Requirements and installation
Select the appropriate branch for your version of Laravel.

| Branch | Laravel Versions | PHP Version |
|:-------|:-----------------|:------------|
| 1.x    | `^6.0`           | `^7.2`      |
| 2.x    | `^7.0`           | `^7.2.5`    |
| 3.x    | `^8.0`           | `^7.3`      |
| 4.x    | `^8.0`           | `^8.0`      |

Install Apposite using [Composer](https://getcomposer.org/):

```bash
composer require monooso/apposite
```

## Usage
Apposite provides three [custom Laravel validation rules](https://laravel.com/docs/8.x/validation#using-rule-objects):

- [`ApplyWhen`](#apply-when)
- [`ApplyUnless`](#apply-unless)
- [`ApplyMap`](#apply-map)

### `ApplyWhen` <a name="apply-when"></a>
Use `ApplyWhen` to apply one or more validation rules when a condition is met. For example, validate the `email` field if the `contact_method` is "email".

The `ApplyWhen` constructor expects two arguments:

- A conditional, which determines whether the validation rules are applied. This may be a boolean value, or a closure which returns a boolean.
- The validation rules to apply if the conditional evaluates to `true`. The may be in [any format](https://laravel.com/docs/8.x/validation#quick-writing-the-validation-logic) recognised by the Laravel validator.

For example:

```php
new ApplyWhen($foo === $bar, 'required|string|min:10');

new ApplyWhen(function () {
    return random_int(1, 10) % 2 === 0;
}, ['required', 'string', 'min:10']);
```

Add the `ApplyWhen` rule to your validation rules array in the normal way.

```php
public function store(Request $request)
{
    $rules = [
        'contact_method' => ['required', 'in:email,phone'],
        'email'          => [
            new ApplyWhen($request->contact_method === 'email', ['required', 'email', 'max:255']),
        ],
    ];

    $validated = $this->validate($rules);
}
```

### `ApplyUnless` <a name="apply-unless"></a>
`ApplyUnless` is the opposite of `ApplyWhen`. Use it to apply one or more validation rules when a condition _is not_ met.

For example:

```php
public function store(Request $request)
{
    $rules = [
        'contact_method' => ['required', 'in:email,phone'],
        'email'          => [
            new ApplyUnless($request->contact_method === 'phone', ['required', 'email', 'max:255']),
        ],
    ];

    $validated = $this->validate($rules);
}
```

Refer to the [`ApplyWhen`](#apply-when) documentation for full usage instructions.

### `ApplyMap` <a name="apply-map"></a>
Use `ApplyMap` when you need to choose between different sets of validation rules. For example, when validating that the chosen `delivery_service` is offered by the chosen `delivery_provider`.

```php
public function store(Request $request)
{
    $rules = [
        'delivery_provider' => ['required', 'in:fedex,ups,usps'],
        'delivery_service'  => [
            'required',
            new ApplyMap($request->delivery_provider, [
                'fedex' => 'in:one_day,two_day',
                'ups'   => 'in:overnight,standard',
                'usps'  => 'in:two_day,someday',
            ]),
        ],
    ]; 

    $validated = $this->validate($rules);
}
```

The `ApplyMap` constructor expects two arguments:

- The "key" value, which determines which rules to apply (if any). For example, "fedex".
- A "map" of keys to validation rules. The validation rules may be in any format recognised by the Laravel validator.

## License
Apposite is open source software, released under [the MIT license](https://github.com/monooso/apposite/blob/stable/LICENSE.txt).
