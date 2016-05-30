<?php

namespace Jenko\Sunscreen\Guesser;

use Composer\Package\PackageInterface;
use Jenko\Sunscreen\Util;

class AbstractClassGuesser extends AbstractGuesser
{
    /**
     * {@inheritdoc}
     */
    public function guess(PackageInterface $package)
    {
        $src = Util::extractSourceDirectoryFromPackage($package);
        $dir = $this->vendorDir . Util::DS . $package->getName() . Util::DS . $src;

        echo $dir . "\n";

        $classes = [];
        foreach (glob($dir . "Abstract*.php") as $filename) {
            $namespace = Util::extractNamespaceFromPackage($package);
            $filenameParts = explode('/', rtrim($filename, '.php'));

            $classes[] = $namespace . '\\' . end($filenameParts);
        }

        return $classes;
    }
}