<?php

namespace Jenko\Sunscreen;

use Composer\Installer\PackageEvent;

class Sunscreen implements SunscreenInterface
{
    public static function postPackageInstall(PackageEvent $event)
    {
        $installedPackage = $event->getOperation()->getPackage();
        $extra = $installedPackage->getExtra();

        // Get extra config
        if (isset($extra['sunscreen'])) {
            $mainFilename = self::configuredMainFilename($extra['sunscreen']);
        } else {
            $mainFilename = self::guessMainFilename($installedPackage);
        }

        if ($mainFilename === null) {
            // TODO: Write to console that no main interface/class could be identified.
        }

        // TODO: The magic.
    }

    private static function configuredMainFilename($sunscreenConfig)
    {
        $isInterface = isset($sunscreenConfig['interface']);
        $isClass = isset($sunscreenConfig['class']);

        if ($isInterface) {
            return $sunscreenConfig['interface'];
        }

        return $isClass ? $sunscreenConfig['class'] : null;
    }

    private static function guessMainFilename($package)
    {
        $psr4 = $package->getAutoload()['psr-4'];
        $namespace = key($psr4);
        $packageParts = explode('\\', rtrim($namespace, '\\'));

        $filename =  __DIR__ . '/../vendor/' . $package->getName() . '/' . reset($psr4) . end($packageParts) . 'Interface.php';

        if (!is_file($filename)) {
            $filename =  __DIR__ . '/../vendor/' . $package->getName() . '/' . reset($psr4) . end($packageParts) . '.php';
        }

        return is_file($filename) ? $filename : null;
    }
}

