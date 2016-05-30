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
            $src = reset($autoload['psr-4']);
            return is_array($src) ? $src[0] : $src;
        }

        if (isset($autoload['psr-0'])) {
            $src = reset($autoload['psr-0']);
            return is_array($src) ? $src[0] : $src;
        }

        return '';
    }

    /**
     * @param $interfaceFqn
     *
     * @return string
     */
    public static function makeClassName($interfaceFqn)
    {
        $reflected = new \ReflectionClass($interfaceFqn);
        $interfaceName = $reflected->getShortName();        
        $interfaceFqnParts = explode('\\', $interfaceFqn);
        
        return reset($interfaceFqnParts) . str_replace('Interface', '', $interfaceName);
    }
}