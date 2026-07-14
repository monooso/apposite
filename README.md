# Apposite

<p>
  <img src="https://github.com/monooso/unobserve/actions/workflows/lint-and-test.yml/badge.svg" alt="Lint and Test Status"/></a>
  <a href="https://scrutinizer-ci.com/g/monooso/apposite"><img src="https://img.shields.io/scrutinizer/g/monooso/apposite.svg" alt="Quality Score"/></a>
  <a href="https://scrutinizer-ci.com/g/monooso/apposite"><img src="https://img.shields.io/scrutinizer/coverage/g/monooso/apposite.svg" alt="Coverage"/></a>
  <a href="https://packagist.org/packages/monooso/apposite"><img src="https://poser.pugx.org/monooso/apposite/v/stable.svg" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/monooso/apposite"><img src="https://poser.pugx.org/monooso/apposite/license.svg" alt="License"></a>
</p>

## About Apposite
Apposite makes it easy to conditionally apply Laravel validation rules, even when you don't have access to [the validator instance](https://laravel.com/docs/validation#conditionally-adding-rules).

## Requirements and installation
Apposite supports only the latest major version of [Laravel](https://laravel.com/); see [`composer.json`](composer.json) for the exact version requirements.

Install it using [Composer](https://getcomposer.org/):

```bash
composer require monooso/apposite
```

All development happens on `main`; releases are tagged.

## Usage
Apposite provides three [custom Laravel validation rules](https://laravel.com/docs/validation#using-rule-objects):

- [`ApplyWhen`](#apply-when)
- [`ApplyUnless`](#apply-unless)
- [`ApplyMap`](#apply-map)

### `ApplyWhen` <a name="apply-when"></a>
Use `ApplyWhen` to apply one or more validation rules when a condition is met. For example, validate the `email` field if the `contact_method` is "email".

The `ApplyWhen` constructor expects two arguments:

- A conditional, which determines whether the validation rules are applied. This may be a boolean value, or a closure which returns a boolean.
- The validation rules to apply if the conditional evaluates to `true`. The may be in [any format](https://laravel.com/docs/validation#quick-writing-the-validation-logic) recognised by the Laravel validator.

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

## Local development
Development and testing happen entirely inside [Podman](https://podman.io/) containers, driven by the `./dev` script. You do **not** need to install PHP, Composer, or any project dependencies on your machine.

The `dev` helper script builds a disposable PHP + Composer image for the PHP version you name, mounts your working tree into it, and runs your command. Any changes to `composer.json` and `composer.lock` are written back to your disk so they can be committed.

### Prerequisites
[Podman](https://podman.io/) 5.x or later.

### Common tasks
The `dev` script always accepts the target PHP version as the first argument. The first time you run a command with a given PHP version, it builds the image automatically.

| Command | Purpose |
|---|---|
| `./dev 8.3` | Open a shell on PHP 8.3 |
| `./dev 8.3 test` | Run the test suite |
| `./dev 8.3 lint` | Run Laravel Pint |
| `./dev 8.3 composer install` | Install dependencies from the lock file |
| `./dev 8.3 composer update` | Re-resolve dependencies (rewrites `composer.lock`) |
| `./dev 8.3 composer outdated` | List outdated packages |
| `./dev 8.3 php -v` | Run any command in the container |
| `./dev build 8.3` | Rebuild the image for a version |

Run `./dev --help` for the full reference.

### Upgrading PHP or Laravel

1. Create a branch.
2. Edit `composer.json` to widen the relevant constraints, e.g. `"php": "^8.3"` and `"illuminate/support": "^13.0"`.
3. Re-resolve dependencies against the target PHP version:

   ```bash
   ./dev 8.3 composer update
   ```

4. In the event of a conflict, investigate and iterate:

   ```bash
   ./dev 8.3 composer why-not laravel/framework 13.0.0
   ./dev 8.3 composer require illuminate/support:"^13.0" --no-update
   ./dev 8.3 composer update
   ```

5. Run the tests and linter:

   ```bash
   ./dev 8.3 test
   ./dev 8.3 lint
   ```

6. Commit `composer.json` and `composer.lock`.

Each PHP version keeps its own dependency cache, so several versions can be tested side by side (e.g. `./dev 8.2 test`, `./dev 8.3 test`) without interfering with each other.

## License
Apposite is open source software, released under [the MIT license](https://github.com/monooso/apposite/blob/stable/LICENSE.txt).
