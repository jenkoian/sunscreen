<?php

namespace Jenko\Sunscreen\Guesser;

use Composer\Package\PackageInterface;
use Jenko\Sunscreen\Util;

class InterfaceGuesser implements GuesserInterface
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

        $interfaces = [];
        foreach (glob($dir . "*Interface.php") as $filename) {
            $namespace = Util::extractNamespaceFromPackage($package);
            $filenameParts = explode('/', rtrim($filename, '.php'));
            $interfaces[] = $namespace . '\\' . end($filenameParts);
        }

        return $interfaces;
    }
}