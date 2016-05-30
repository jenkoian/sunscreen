<?php

namespace Jenko\Sunscreen\Guesser;

use Composer\Package\PackageInterface;
use Jenko\Sunscreen\Util;

class AbstractClassGuesser implements GuesserInterface
{
    /**
     * @param PackageInterface $package
     *
     * @return array
     */
    public function guess(PackageInterface $package)
    {
        $src = Util::extractSourceDirectoryFromPackage($package);
        $dir = __DIR__ . Util::DS . '..' . Util::DS . '..' . Util::DS . 
               self::VENDOR_DIR . Util::DS . $package->getName() . Util::DS . $src;

        $classes = [];
        foreach (glob($dir . "Abstract*.php") as $filename) {
            $namespace = Util::extractNamespaceFromPackage($package);
            $filenameParts = explode('/', rtrim($filename, '.php'));
            $classes[] = $namespace . '\\' . end($filenameParts);
        }

        return $classes;
    }
}