# Laravel Persona KYC

[![Latest Stable Version](http://poser.pugx.org/do-inc/laravel-persona-kyc/v)](https://packagist.org/packages/do-inc/laravel-persona-kyc)
[![Tests](https://github.com/Do-inc/laravel-persona-kyc/actions/workflows/php.yml/badge.svg?branch=master)](https://github.com/Do-inc/laravel-persona-kyc/actions/workflows/php.yml)
[![Total Downloads](http://poser.pugx.org/do-inc/laravel-persona-kyc/downloads)](https://packagist.org/packages/do-inc/laravel-persona-kyc)
[![License](http://poser.pugx.org/do-inc/laravel-persona-kyc/license)](https://packagist.org/packages/do-inc/laravel-persona-kyc)
[![PHP Version Require](http://poser.pugx.org/do-inc/laravel-persona-kyc/require/php)](https://packagist.org/packages/do-inc/laravel-persona-kyc)

This package helps with the identity verification of your customers. It provides a simple yet powerful interface based
on method concatenation to semantically construct your requests.

Users verification become as easy as writing a couple of lines:

**Backend**
```php
\Doinc\PersonaKyc\Persona::init()->accounts()->create("my-account-reference-id");
```

**Frontend**
```javascript
const client = new Persona.Client({
    templateId: "itmpl_Ygs16MKTkA6obnF8C3Rb17dm",
    environment: "sandbox",
    referenceId: "my-account-reference-id",
    onReady: () => client.open(),
    onComplete: ({inquiryId, status, fields}) => console.log("onComplete"),
    onCancel: ({inquiryId, sessionToken}) => console.log('onCancel'),
    onError: (error) => console.log("onError"),
});
```

## Installation

You can install the package via composer:

```bash
composer require do-inc/laravel-persona-kyc
php artisan persona:install
```

Running the installation command will automatically publish the configuration files, the migrations and compile all the
stubs.

You can always publish all the assets manually running:

```bash
php artisan vendor:publish --tag="persona-kyc-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="persona-kyc-config"
```

Additionally, a couple of environment variable should also be defined:
```dotenv
PERSONA_API_KEY="persona_sandbox_XXX"
PERSONA_WEBHOOK_SECRET="wbhsec_XXX"
```

## Usage

#### Basic
[Persona](https://withpersona.com/) offers the possibility to verify users identity with easy without the need to create
custom flows or deal with long and complicated verification procedures.

The verification process begins with the creation of an account, accounts must be generated with a reference id in order
to link multiple inquiries together.
In order to reduce complexity and easily query for remote data consider using your _user id_ as _reference id_.

```php
$account = \Doinc\PersonaKyc\Persona::init()->accounts()->create("1234");
```

Lots of different methods are available out of the box, these will easy the development of simple and custom solution 
with Persona as a verification provider. 

Refer to this [method list](docs/method-list.md) for a complete list of the available methods.

#### Webhooks

Persona supports webhooks out of the box. In order to enforce a secure usage of the webhooks without any tampering 
possibility a default endpoint is provided at `/persona/hook` additionally a prefix may be added via configuration 
options.

The webhook will emit events depending on the request received, each event will receive a pre-parsed model as this will
avoid errors.

This means that accessing persona webhooks is as simple as setting up a listener for the event you're interested into!

Refer to this [event list](docs/event-list.md) for a complete list of the available events.

## Testing

Copy the `.env.example` file into `.env` and fill in all the variables then run

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security related issues, please email security@do-inc.co instead of using the issue tracker.

## Credits

- [Emanuele (ebalo) Balsamo](https://github.com/ebalo55)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
