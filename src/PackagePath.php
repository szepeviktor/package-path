<?php

namespace SzepeViktor\Composer;

use Composer\Autoload\ClassLoader;
use ReflectionClass;

class PackagePath
{
    private const INSTALLED_PACKAGES_DATA_PATH = '/composer/installed.json';

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
        static $vendorPath;

        if (is_string($vendorPath)) {
            return $vendorPath;
        }

        $reflector = new ReflectionClass(ClassLoader::class);

        $classLoaderPath = $reflector->getFileName();
        if ($classLoaderPath === false) {
            throw new \RuntimeException('Unable to find Composer ClassLoader.');
        }

        $vendorPath = dirname($classLoaderPath, 2);
        if (!is_dir($vendorPath)) {
            throw new \RuntimeException('Unable to detect vendor path.');
        }

        return $vendorPath;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function getInstallPackages(): array
    {
        static $packageData;

        if (is_array($packageData)) {
            return $packageData['packages'];
        }

        // TODO error handling
        $content = file_get_contents(static::getVendorPath() . static::INSTALLED_PACKAGES_DATA_PATH);
        $packageData = json_decode($content, true);
        if (!array_key_exists('packages', $packageData)) {
            return [];
        }

        return $packageData['packages'];
    }
}
