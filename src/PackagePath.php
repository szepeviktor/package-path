<?php

namespace SzepeViktor\Composer;

use Composer\Autoload\ClassLoader;
use ReflectionClass;

class PackagePath
{
    public const INSTALLED_PACKAGES_DATA_PATH = '/composer/installed.json';

    public static function get(string $packageName): ?string
    {
        $packageData = static::getInstallPackages();
        if ($packageData === []) {
            // No installed packages
            return null;
        }

        $packages = array_column($packageData, null, 'name');
        if (!array_key_exists($packageName, $packages) || !array_key_exists('install-path', $packages[$packageName])) {
            // Package not found or missing 'install-path' key
            return null;
        }

        $path = realpath(sprintf('%s/composer/%s', static::getVendorPath(), $packages[$packageName]['install-path']));
        if ($path === false) {
            // Directory does not exist
            return null;
        }

        return $path;
    }

    /**
     * @throws \RuntimeException
     */
    public static function getVendorPath(): string
    {
        static $cache;

        if (is_string($cache)) {
            return $cache;
        }

        $reflector = new ReflectionClass(ClassLoader::class);
        $classLoaderPath = $reflector->getFileName();
        if ($classLoaderPath === false) {
            throw new \RuntimeException('Unable to find Composer ClassLoader file.');
        }

        $vendorPath = dirname($classLoaderPath, 2);
        if (!is_dir($vendorPath)) {
            throw new \RuntimeException('Unable to detect vendor path.');
        }

        return $cache = $vendorPath;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected static function getInstallPackages(): array
    {
        static $cache;

        if (is_array($cache)) {
            return $cache;
        }

        $content = file_get_contents(static::getVendorPath() . static::INSTALLED_PACKAGES_DATA_PATH);
        if ($content === false) {
            // installed.json not found
            return [];
        }

        /** @var array<array-key, mixed> $packageData */
        $packageData = json_decode($content, true);
        if (json_last_error() !== \JSON_ERROR_NONE || !array_key_exists('packages', $packageData)) {
            // JSON error or missing 'packages' key
            return [];
        }

        return $cache = $packageData['packages'];
    }
}
