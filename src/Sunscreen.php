<?php

namespace Jenko\Sunscreen;

use Composer\Installer\PackageEvent;
use Jenko\Sunscreen\Processor\AdapterProcessor;
use Jenko\Sunscreen\Processor\ClassProcessor;
use Jenko\Sunscreen\Processor\InterfaceProcessor;

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
        $baseDir = $event->getComposer()->getConfig()->get('vendor-dir') . '/..';

        $mainNamespace = self::extractNamespaceFromPackage($mainPackage);
        $src = self::extractSourceDirectoryFromPackage($mainPackage);
        if (empty($mainNamespace)) {
            // TODO: Write to console that no namespace was found.
            return;
        }

        if (isset($extra['sunscreen'])) {
            $interfaces = self::configuredInterfaces($extra['sunscreen']);
            $classes = self::configuredClasses($extra['sunscreen']);
        } else {
            $interfaces = self::guessedInterfaces($installedPackage, $mainNamespace);
            // TODO: Add guessedClasses functionality
            $classes = [];
        }

        if (empty($interfaces) && empty($classes)) {
            // TODO: Write to console that no main interface/class could be identified.
            return;
        }

        if (!empty($interfaces)) {
            foreach ($interfaces as $interface) {
                $interfaceProcessor = new InterfaceProcessor(
                    $interface,
                    $mainNamespace,
                    $baseDir . DIRECTORY_SEPARATOR . $src
                );
                $interfaceProcessor->generate();

                $adapterProcessor = new AdapterProcessor(
                    $interface,
                    $mainNamespace,
                    $baseDir . DIRECTORY_SEPARATOR . $src
                );
                $adapterProcessor->generate();
            }
        }

        if (!empty($classes)) {
            foreach ($classes as $class) {
                $classProcessor = new ClassProcessor($class, $mainNamespace, $baseDir . DIRECTORY_SEPARATOR . $src);
                $classProcessor->generate();

                $adapterProcessor = new AdapterProcessor($class, $mainNamespace, $baseDir . DIRECTORY_SEPARATOR . $src);
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
     * @param $package
     * @return null|string
     */
    private static function guessedInterfaces($package)
    {
        $namespace = self::extractNamespaceFromPackage($package);
        $src = self::extractSourceDirectoryFromPackage($package);

        $packageParts = explode('\\', rtrim($namespace, '\\'));

        $filename =  __DIR__ . '/../vendor/' . $package->getName() . '/' . $src . end($packageParts) . 'Interface.php';

        if (is_file($filename)) {
            return [$namespace . '\\' . end($packageParts) . 'Interface'];
        }

        $filename =  __DIR__ . '/../vendor/' . $package->getName() . '/' . $src . end($packageParts) . '.php';

        return is_file($filename) ? [$namespace . '\\' . end($packageParts)] : [];
    }

    /**
     * @param $package
     * @return string
     */
    private static function extractNamespaceFromPackage($package)
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
     * @param $package
     * @return string
     */
    private static function extractSourceDirectoryFromPackage($package)
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

