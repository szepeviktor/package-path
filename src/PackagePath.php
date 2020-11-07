<?php

namespace SzepeViktor\Composer;

use Composer\Autoload\ClassLoader;
use ReflectionClass;

class PackagePath
{
    public const INSTALLED_PACKAGES_DATA_PATH = '/composer/installed.json';

    public static function get(string $packageName): ?string
    {
        // TODO error handling
        $packageData = static::getInstallPackages();
        if ($packageData === []) {
            return null;
        }

        $packages = array_combine(array_column($packageData, 'name'), $packageData);
        if (!array_key_exists($packageName, $packages) || !array_key_exists('install-path', $packages[$packageName])) {
            return null;
        }

        return realpath(sprintf('%s/composer/%s', static::getVendorPath(), $packages[$packageName]['install-path']));
    }

    /**
     * throws \RuntimeException
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

        // TODO error handling
        $content = file_get_contents(static::getVendorPath() . static::INSTALLED_PACKAGES_DATA_PATH);
        $packageData = json_decode($content, true);
        if (!array_key_exists('packages', $packageData)) {
            return [];
        }

        return $cache = $packageData['packages'];
    }
}
