# Package Path finder

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
