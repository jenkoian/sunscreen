<?php

namespace Jenko\Sunscreen;

use Composer\Installer\PackageEvent;
use Jenko\Sunscreen\Processor\AdapterProcessor;
use Jenko\Sunscreen\Processor\ClassProcessor;
use Jenko\Sunscreen\Processor\InterfaceProcessor;

/**
 * TODO: Tidy this up, extract loads of it out to static helpers etc.
 */
class Sunscreen implements SunscreenInterface
{
    /**
     * @param PackageEvent $event
     */
    public static function postPackageInstall(PackageEvent $event)
    {
        $mainPackage = $event->getComposer()->getPackage();
        $installedPackage = $event->getOperation()->getPackage();
        $extra = $installedPackage->getExtra();

        if (isset($extra['sunscreen'])) {
            $interfaces = self::configuredInterfaces($extra['sunscreen']);
            $classes = self::configuredClasses($extra['sunscreen']);
        } else {
            $interfaces = self::guessedInterfaces($installedPackage);
            $classes = [];
            //$classes = self::guessedClasses($installedPackage);
        }

        if (empty($interfaces) && empty($classes)) {
            // TODO: Write to console that no main interface/class could be identified.
            return;
        }

        $mainNamespace = self::mainNamespace($mainPackage);

        if (!empty($interfaces)) {
            foreach ($interfaces as $interface) {
                $interfaceProcessor = new InterfaceProcessor($interface, $mainNamespace);
                $interfaceProcessor->generate();

                $adapterProcessor = new AdapterProcessor($interface, $mainNamespace);
                $adapterProcessor->generate();
            }
        }

        if (!empty($classes)) {
            foreach ($classes as $class) {
                $classProcessor = new ClassProcessor($class, self::mainNamespace($mainPackage));
                $classProcessor->generate();

                $adapterProcessor = new AdapterProcessor($class, $mainNamespace);
                $adapterProcessor->generate();                
            }
        }
    }

    /**
     * @param $sunscreenConfig
     * @return array ['FQN']
     */
    private static function configuredInterfaces($sunscreenConfig)
    {
        if (isset($sunscreenConfig['interfaces'])) {
            return $sunscreenConfig['interfaces'];
        }

        return [];
    }

    /**
     * @param $sunscreenConfig
     * @return array ['FQN']
     */
    private static function configuredClasses($sunscreenConfig)
    {
        if (isset($sunscreenConfig['classes'])) {
            return $sunscreenConfig['classes'];
        }

        return [];
    }    

    /**
     * TODO: Support multiple interfaces/classes
     * TODO: Support psr-0 too
     * @param $package
     * @return null|string
     */
    private static function guessedInterfaces($package)
    {
        $psr4 = $package->getAutoload()['psr-4'];
        $namespace = key($psr4);
        $packageParts = explode('\\', rtrim($namespace, '\\'));

        $filename =  __DIR__ . '/../vendor/' . $package->getName() . '/' . reset($psr4) . end($packageParts) . 'Interface.php';

        if (is_file($filename)) {
            return [$namespace . end($packageParts) . 'Interface'];
        }

        $filename =  __DIR__ . '/../vendor/' . $package->getName() . '/' . reset($psr4) . end($packageParts) . '.php';

        return is_file($filename) ? [$namespace . end($packageParts)] : [];
    }

    /**
     * TODO: Support psr-0 too.
     * @param $mainPackage
     * @return mixed
     */
    private static function mainNamespace($mainPackage)
    {
        $psr4 = $mainPackage->getAutoload()['psr-4'];

        return rtrim(key($psr4), '\\');
    }
}

