# Laravel XML made easy!

[![Latest Version on Packagist](https://img.shields.io/packagist/v/flowgistics/laravel-xml.svg?style=flat-square)](https://packagist.org/packages/flowgistics/laravel-xml)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/flowgistics/laravel-xml/run-tests?label=tests)](https://github.com/flowgistics/laravel-xml/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/flowgistics/laravel-xml/Check%20&%20fix%20styling?label=code%20style)](https://github.com/flowgistics/laravel-xml/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/flowgistics/laravel-xml.svg?style=flat-square)](https://packagist.org/packages/flowgistics/laravel-xml)

This package handles importing and exporting XML data from your Laravel application.

The main features are

* Fast XML importing with the ability to cast to classes and models
* XML exporting from (nested / value only ) arrays
* Exporting Laravel views to XML

## Installation

You can install the package via composer:

```bash
composer require flowgistics/laravel-xml
```

## Usage
This packages comes with a facade which you can use like this `\XML::` or use it in your class like `use XML;`

In depth guides can be found here:

* [Exporting](https://github.com/flowgistics/laravel-xml/wiki/Exporting)
* [Importing](https://github.com/flowgistics/laravel-xml/wiki/Importing)


```php
$notes = XML::import("notes.xml")
    ->cast('note')->to(NoteModel::class)
    ->expect('note')->as('array')
    ->optimize('camelcase')
    ->get();

```


### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Flowgistics](https://github.com/Flowgistics)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
