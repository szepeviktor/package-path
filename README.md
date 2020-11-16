# Package Path finder

[![Build Status](https://travis-ci.com/szepeviktor/package-path.svg?branch=master)](https://travis-ci.com/github/szepeviktor/package-path)
[![Packagist Version](https://img.shields.io/packagist/v/szepeviktor/package-path)](https://packagist.org/packages/szepeviktor/package-path)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-239922)](https://phpstan.org/)

Get full installation path of any Composer package.

### Installation

```bash
composer require szepeviktor/package-path
```

### Usage

```php
/** @var string|null $packagePath */
$packagePath = \SzepeViktor\Composer\PackagePath::get('phpstan/phpstan');

$vendorPath = \SzepeViktor\Composer\PackagePath::getVendorPath();
// returns "/full/path/to/vendor"
```

### Credits

[David Vander Elst](https://github.com/david-vde) https://stackoverflow.com/a/43543482
