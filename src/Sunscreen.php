<?php

namespace Jenko\Sunscreen;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Jenko\Sunscreen\Guesser\AbstractClassGuesser;
use Jenko\Sunscreen\Guesser\InterfaceGuesser;
use Jenko\Sunscreen\Processor\AdapterProcessor;
use Jenko\Sunscreen\Processor\ClassProcessor;
use Jenko\Sunscreen\Processor\InterfaceProcessor;

class Sunscreen implements PluginInterface, EventSubscriberInterface, SunscreenInterface
{
    /**
     * @var string
     */
    const PRECONFIGURED_DIR = 'preconfigured';

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => 'onPostPackageInstall'
        ];
    }

    /**
     * @param PackageEvent $event
     *
     * @return mixed|void
     */
    public function onPostPackageInstall(PackageEvent $event)
    {
        $mainPackage = $this->composer->getPackage();
        $installedPackage = $event->getOperation()->getPackage();
        $extra = $installedPackage->getExtra();

        if ($installedPackage->isDev()) {
            if ($this->io->isVeryVerbose()) {
                $this->io->write('Sunscreen: Ignoring dev dependency.' . "\n");
            }
            return;
        }

        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $baseDir = $vendorDir . Util::DS  . '..';

        $mainNamespace = Util::extractNamespaceFromPackage($mainPackage);
        $src = Util::extractSourceDirectoryFromPackage($mainPackage);
        if (empty($mainNamespace)) {
            $this->io->writeError('Sunscreen: Main Namespace not found.' . "\n");
            return;
        }

        if (isset($extra['sunscreen'])) {
            $interfaces = $this->configuredInterfaces($extra['sunscreen']);
            $classes = $this->configuredClasses($extra['sunscreen']);
        } elseif ($preconfiguredExtra = $this->findPreconfiguredExtra($installedPackage->getName())) {
            $interfaces = $this->configuredInterfaces($preconfiguredExtra['sunscreen']);
            $classes = $this->configuredClasses($preconfiguredExtra['sunscreen']);
        } else {
            $interfaceGuesser = new InterfaceGuesser($vendorDir);
            $interfaces = $interfaceGuesser->guess($installedPackage);
            $classGuesser = new AbstractClassGuesser($vendorDir);
            $classes = $classGuesser->guess($installedPackage);
        }

        if (empty($interfaces) && empty($classes)) {
            if ($this->io->isVerbose()) {
                $this->io->write('Sunscreen: No interfaces or classes could be found.' . "\n");
            }
            return;
        }

        if (!empty($interfaces)) {
            $this->processInterfaces($interfaces, $mainNamespace, $baseDir, $src);
        }

        if (!empty($classes)) {
            $this->processClasses($classes, $mainNamespace, $baseDir, $src);
        }
    }

    /**
     * @param $sunscreenConfig
     * @return array ['FQN']
     */
    private function configuredInterfaces($sunscreenConfig)
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
    private function configuredClasses($sunscreenConfig)
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
     */
    private function processInterfaces(array $interfaces, $mainNamespace, $baseDir, $src)
    {
        foreach ($interfaces as $interface) {
            $interfaceProcessor = new InterfaceProcessor($interface, $mainNamespace, $baseDir . Util::DS . $src);
            $interfaceProcessor->process();

            if ($this->io->isVeryVerbose()) {
                $this->io->write('Sunscreen: Interface created.' . "\n");
            }

            $adapterProcessor = new AdapterProcessor($interface, $mainNamespace, $baseDir . Util::DS . $src);
            $adapterProcessor->process();

            if ($this->io->isVeryVerbose()) {
                $this->io->write('Sunscreen: Adapter created.' . "\n");
            }
        }
    }

    /**
     * @param array $classes
     * @param string $mainNamespace
     * @param string $baseDir
     * @param string $src
     */
    private function processClasses(array $classes, $mainNamespace, $baseDir, $src)
    {
        foreach ($classes as $class) {
            $classProcessor = new ClassProcessor($class, $mainNamespace, $baseDir . Util::DS . $src);
            $classProcessor->process();

            if ($this->io->isVeryVerbose()) {
                $this->io->write('Sunscreen: Class created.' . "\n");
            }

            $adapterProcessor = new AdapterProcessor($class, $mainNamespace, $baseDir . Util::DS . $src);
            $adapterProcessor->process();

            if ($this->io->isVeryVerbose()) {
                $this->io->write('Sunscreen: Adapter created.' . "\n");
            }
        }
    }

    /**
     * @param string $packageName
     * @return null
     */
    private function findPreconfiguredExtra($packageName)
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

