# Addresses

[//]: # ([![Latest Version on Packagist]&#40;https://img.shields.io/packagist/v/geordiejackson/addresses.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/geordiejackson/addresses&#41;)

[//]: # ()

[//]: # ([![GitHub Tests Action Status]&#40;https://img.shields.io/github/workflow/status/geordiejackson/addresses/run-tests?label=tests&#41;]&#40;https://github.com/geordiejackson/addresses/actions?query=workflow%3Arun-tests+branch%3Amain&#41;)

[//]: # ()

[//]: # ([![GitHub Code Style Action Status]&#40;https://img.shields.io/github/workflow/status/geordiejackson/addresses/Check%20&%20fix%20styling?label=code%20style&#41;]&#40;https://github.com/geordiejackson/addresses/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain&#41;)

[//]: # ()

[//]: # ([![Total Downloads]&#40;https://img.shields.io/packagist/dt/geordiejackson/addresses.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/geordiejackson/addresses&#41;)

### Remit

This package is for where many models are required to have multiple addresses; for example, a Management Informations
System might have models for employees, contractors, suppliers, etc., who may all have more than one address.

This package adds addresses to any existing model via the use of a single-line config entry and adding the trait to the
model. All other configuration is handled by the package.

It uses a many-to-many polymorphic relation to achieve this.

### Installation

You can install the package via composer:

```bash
composer require geordiejackson/addresses
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="addresses-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="addresses-config"
```
†
Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="addresses-views"
```

### Adding addresses to an existing model

In ```config/addresses.php``` import your model and create a key => value pair of the models plus the name you want to use for reverse lookups (i.e. when you're looking up the parent model for an address). NOTE: It will default to the plural of the model name if the value is omitted (e.g. 'users' below).

```php
    use Your\Namespace\Supplier;
    use Your\Namespace\User;
  
   return [
     Supplier::class => 'suppliers',
     User::class
   ];
```



## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [John Jackson](https://github.com/GeordieJackson)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
