<?php

namespace Jenko\Sunscreen;

use Composer\Installer\PackageEvent;
use Jenko\Sunscreen\Guesser\AbstractClassGuesser;
use Jenko\Sunscreen\Guesser\InterfaceGuesser;
use Jenko\Sunscreen\Processor\AdapterProcessor;
use Jenko\Sunscreen\Processor\ClassProcessor;
use Jenko\Sunscreen\Processor\InterfaceProcessor;

class Sunscreen implements SunscreenInterface
{
    /**
     * @param PackageEvent $event
     *
     * @return mixed|void
     */
    public static function postPackageInstall(PackageEvent $event)
    {
        $mainPackage = $event->getComposer()->getPackage();
        $installedPackage = $event->getOperation()->getPackage();
        $extra = $installedPackage->getExtra();
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $baseDir = $vendorDir . Util::DS  . '..';

        $mainNamespace = Util::extractNamespaceFromPackage($mainPackage);
        $src = Util::extractSourceDirectoryFromPackage($mainPackage);
        if (empty($mainNamespace)) {
            // TODO: Write to console that no namespace was found.
            return;
        }

        if (isset($extra['sunscreen'])) {
            $interfaces = self::configuredInterfaces($extra['sunscreen']);
            $classes = self::configuredClasses($extra['sunscreen']);
        } else {
            $interfaceGuesser = new InterfaceGuesser($vendorDir);
            $interfaces = $interfaceGuesser->guess($installedPackage);
            $classGuesser = new AbstractClassGuesser($vendorDir);
            $classes = $classGuesser->guess($installedPackage);
        }

        if (empty($interfaces) && empty($classes)) {
            // TODO: Write to console that no main interface/class could be identified.
            return;
        }

        if (!empty($interfaces)) {
            foreach ($interfaces as $interface) {
                $interfaceProcessor = new InterfaceProcessor($interface, $mainNamespace, $baseDir . Util::DS . $src);
                $interfaceProcessor->process();

                $adapterProcessor = new AdapterProcessor($interface, $mainNamespace, $baseDir . Util::DS . $src);
                $adapterProcessor->process();
            }
        }

        if (!empty($classes)) {
            foreach ($classes as $class) {
                $classProcessor = new ClassProcessor($class, $mainNamespace, $baseDir . Util::DS . $src);
                $classProcessor->process();

                $adapterProcessor = new AdapterProcessor($class, $mainNamespace, $baseDir . Util::DS . $src);
                $adapterProcessor->process();
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
}

