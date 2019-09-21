# Apposite

<p>
  <a href="https://travis-ci.org/monooso/apposite"><img src="https://img.shields.io/travis/monooso/apposite/master.svg" alt="Build Status"/></a>
  <a href="https://scrutinizer-ci.com/g/monooso/apposite"><img src="https://img.shields.io/scrutinizer/g/monooso/apposite.svg" alt="Quality Score"/></a>
  <a href="https://scrutinizer-ci.com/g/monooso/apposite"><img src="https://img.shields.io/scrutinizer/coverage/g/monooso/apposite.svg" alt="Coverage"/></a>
  <a href="https://packagist.org/packages/monooso/apposite"><img src="https://poser.pugx.org/monooso/apposite/v/stable.svg" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/monooso/apposite"><img src="https://poser.pugx.org/monooso/apposite/license.svg" alt="License"></a>
</p>

## About Apposite
Apposite makes it easy to conditionally apply Laravel validation rules, even when you don't have access to [the validator instance](https://laravel.com/docs/6.x/validation#conditionally-adding-rules). For example, you may wish to validate the `email` field only if the `contact_method` field is "email".

## Requirements and installation
Apposite requires PHP 7.2, and has been tested against Laravel 6. It _should_ work just fine with any recent version from the 5.x branch, but it hasn't been tested in that environment.

Install Apposite using [Composer](https://getcomposer.org/):

```bash
composer require monooso/apposite
```

## Usage
Apposite provides two [custom Laravel validation rules](https://laravel.com/docs/6.x/validation#using-rule-objects), `ApplyWhen` and `ApplyUnless`.

```php
public function store(Request $request)
{
    $rules = [
        'contact_method' => ['required', 'in:email,phone'],
        'email' => [
            new ApplyWhen(function () use ($request) {
                return $request->get('contact_method') === 'email';
            }, ['required', 'email', 'max:255']),
        ],
        'phone' => [
            new ApplyUnless(
                ($request->get('contact_method') === 'email'),
                'required'
            ),
        ],
    ];
}
```

Both rules expect a conditional, and the rules to apply if the conditional evaluates to `true`.

The rules may be in [any format](https://laravel.com/docs/6.x/validation#quick-writing-the-validation-logic) recognised by the Laravel validator. The conditional may be a boolean value, or a closure which returns a boolean.

## License
Apposite is open source software, released under [the MIT license](https://github.com/monooso/apposite/blob/stable/LICENSE.txt).
