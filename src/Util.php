<?php

namespace Jenko\Sunscreen;

use Composer\Package\PackageInterface;

class Util
{
    /**
     * @var string
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * @param PackageInterface $package
     * @return string
     */
    public static function extractNamespaceFromPackage(PackageInterface $package)
    {
        $autoload = $package->getAutoload();

        if (isset($autoload['psr-4'])) {
            return rtrim(key($autoload['psr-4']), '\\');
        }

        if (isset($autoload['psr-0'])) {
            return rtrim(key($autoload['psr-0']), '\\');
        }

        return '';
    }

    /**
     * @param PackageInterface $package
     * @return string
     */
    public static function extractSourceDirectoryFromPackage(PackageInterface $package)
    {
        $autoload = $package->getAutoload();

        if (isset($autoload['psr-4'])) {
            return reset($autoload['psr-4']);
        }

        if (isset($autoload['psr-0'])) {
            return reset($autoload['psr-0']);
        }

        return '';
    }
}