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
     * @var string
     */
    const PRECONFIGURED_DIR = 'preconfigured';

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
        $io = $event->getIO();

        if ($installedPackage->isDev()) {
            if ($io->isVeryVerbose()) {
                $io->write('Sunscreen: Ignoring dev dependency.' . "\n");
            }
            return;
        }

        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $baseDir = $vendorDir . Util::DS  . '..';

        $mainNamespace = Util::extractNamespaceFromPackage($mainPackage);
        $src = Util::extractSourceDirectoryFromPackage($mainPackage);
        if (empty($mainNamespace)) {
            $io->writeError('Sunscreen: Main Namespace not found.' . "\n");
            return;
        }

        if (isset($extra['sunscreen'])) {
            $interfaces = self::configuredInterfaces($extra['sunscreen']);
            $classes = self::configuredClasses($extra['sunscreen']);
        } elseif ($preconfiguredExtra = self::findPreconfiguredExtra($installedPackage->getName())) {
            $interfaces = self::configuredInterfaces($preconfiguredExtra['sunscreen']);
            $classes = self::configuredClasses($preconfiguredExtra['sunscreen']);
        } else {
            $interfaceGuesser = new InterfaceGuesser($vendorDir);
            $interfaces = $interfaceGuesser->guess($installedPackage);
            $classGuesser = new AbstractClassGuesser($vendorDir);
            $classes = $classGuesser->guess($installedPackage);
        }

        if (empty($interfaces) && empty($classes)) {
            if ($io->isVerbose()) {
                $io->write('Sunscreen: No interfaces or classes could be found.' . "\n");
            }
            return;
        }

        if (!empty($interfaces)) {
            self::processInterfaces($interfaces, $mainNamespace, $baseDir, $src, $io);
        }

        if (!empty($classes)) {
            self::processClasses($classes, $mainNamespace, $baseDir, $src, $io);
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
     * @param array $interfaces
     * @param string $mainNamespace
     * @param string $baseDir
     * @param string $src
     * @param IO $io
     */
    private static function processInterfaces(array $interfaces, $mainNamespace, $baseDir, $src, $io)
    {
        foreach ($interfaces as $interface) {
            $interfaceProcessor = new InterfaceProcessor($interface, $mainNamespace, $baseDir . Util::DS . $src);
            $interfaceProcessor->process();

            if ($io->isVeryVerbose()) {
                $io->write('Sunscreen: Interface created.' . "\n");
            }

            $adapterProcessor = new AdapterProcessor($interface, $mainNamespace, $baseDir . Util::DS . $src);
            $adapterProcessor->process();

            if ($io->isVeryVerbose()) {
                $io->write('Sunscreen: Adapter created.' . "\n");
            }
        }
    }

    /**
     * @param array $classes
     * @param string $mainNamespace
     * @param string $baseDir
     * @param string $src
     * @param IO $io
     */
    private static function processClasses(array $classes, $mainNamespace, $baseDir, $src, $io)
    {
        foreach ($classes as $class) {
            $classProcessor = new ClassProcessor($class, $mainNamespace, $baseDir . Util::DS . $src);
            $classProcessor->process();

            if ($io->isVeryVerbose()) {
                $io->write('Sunscreen: Class created.' . "\n");
            }

            $adapterProcessor = new AdapterProcessor($class, $mainNamespace, $baseDir . Util::DS . $src);
            $adapterProcessor->process();

            if ($io->isVeryVerbose()) {
                $io->write('Sunscreen: Adapter created.' . "\n");
            }
        }
    }

    /**
     * @param string $packageName
     * @return null
     */
    private static function findPreconfiguredExtra($packageName)
    {
        list($dirName, $filename) = explode('/', $packageName);
        $filePath = __DIR__ . Util::DS . '..' . Util::DS . self::PRECONFIGURED_DIR . Util::DS . $dirName . Util::DS . $filename . '.json';

        if (!is_file($filePath)) {
            return null;
        }

        $jsonDecoded = json_decode(file_get_contents($filePath), true);

        return isset($jsonDecoded['extra']) ? $jsonDecoded['extra'] : null;
    }
}

