<?php

namespace Jenko\Sunscreen;

use Composer\Installer\PackageEvent;

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
        } else {
            $interfaces = self::guessedInterfaces($installedPackage);
        }

        if (empty($interfaces)) {
            // TODO: Write to console that no main interface/class could be identified.
        }

        $class = new \ReflectionClass($interfaces);
        $generatedInterface = self::interfaceTemplate(self::mainNamespace($mainPackage), $class->getShortName(), $class->getMethods());

        // TODO: Write generated code to file in appropriate location.
    }

    /**
     * TODO: Support multiple interfaces/classes
     * @param $sunscreenConfig
     * @return null
     */
    private static function configuredInterfaces($sunscreenConfig)
    {
        $isInterface = isset($sunscreenConfig['interface']);
        $isClass = isset($sunscreenConfig['class']);

        if ($isInterface) {
            return $sunscreenConfig['interface'];
        }

        return $isClass ? $sunscreenConfig['class'] : null;
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
            return $namespace . end($packageParts) . 'Interface';
        }

        $filename =  __DIR__ . '/../vendor/' . $package->getName() . '/' . reset($psr4) . end($packageParts) . '.php';

        return is_file($filename) ? $namespace . end($packageParts) : null;
    }

    /**
     * @param $namespace
     * @param $interfaceName
     * @param $methods
     * @return mixed
     */
    private static function interfaceTemplate($namespace, $interfaceName, $methods)
    {
        $template = <<<EOF
<?php

namespace <namespace>;

interface <interfaceName>
{
    <methods>
}
EOF;

        $placeHolders = [
            '<namespace>',
            '<interfaceName>',
            '<methods>',
        ];

        $replacements = [
            $namespace,
            $interfaceName,
            self::methodsTemplate($methods),
        ];

        return str_replace($placeHolders, $replacements, $template);
    }

    /**
     * @param $classMethods
     * @return string
     */
    private static function methodsTemplate($classMethods)
    {
        $template = <<<EOF
<docBlock>
    public function <methodName>(<parameters>);
EOF;
        $methods = '';
        /** @var \ReflectionMethod $method */
        foreach ($classMethods as $k => $method) {
            // Only want public methods on our interface.
            if (!$method->isPublic()) {
                continue;
            }

            $params = [];
            /** @var \ReflectionParameter $parameter */
            foreach ($method->getParameters() as $parameter) {
                $params[] = '$' . $parameter->getName();
            }

            $placeHolders = [
                '<docBlock>',
                '<methodName>',
                '<parameters>',
            ];

            $replacements = [
                $method->getDocComment(),
                $method->getName(),
                rtrim(implode(', ', $params), ',')
            ];

            $methods .= str_replace($placeHolders, $replacements, $template);

            if ($k !== count($classMethods)-1) {
                $methods .= "\n";
            }
        }

        return $methods;
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

