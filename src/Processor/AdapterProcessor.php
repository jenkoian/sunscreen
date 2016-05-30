<?php

namespace Jenko\Sunscreen\Processor;

use Jenko\Sunscreen\Util;

class AdapterProcessor implements ProcessorInterface
{
    /**
     * @var string
     */
    const DIRECTORY = 'Adapters';

    /**
     * @var string
     */
    private $interfaceFqn;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $fileLocation;

    /**
     * @param string $interfaceFqn
     * @param string $namespace
     * @param string $fileLocation
     */
    public function __construct($interfaceFqn, $namespace, $fileLocation)
    {
        $this->interfaceFqn = $interfaceFqn;
        $this->namespace = $namespace;
        $this->fileLocation = $fileLocation;
    }

    /**
     * {@inheritdoc}
     */
    public function process($asString = false)
    {
        $reflected = new \ReflectionClass($this->interfaceFqn);

        $interfaceName = $reflected->getShortName();
        $className = Util::makeClassName($this->interfaceFqn);
        $interfaceProperty = lcfirst($interfaceName);

        // TODO: Inject a factory into class then create methods processor through that.
        $methodsProcessor = new AdapterMethodsProcessor($reflected->getMethods(), $interfaceProperty);

        $adapterString = strtr(
            file_get_contents(__DIR__ . '/../../template/AdapterTemplate.tpl'),
            [
                '<namespace>'  => $this->namespace,
                '<interfaceFqn>' => $this->interfaceFqn,
                '<className>' => $className,
                '<interfaceName>' => $interfaceName,
                '<interfaceProperty>' => $interfaceProperty,
                '<methods>' => $methodsProcessor->process(true)
            ]
        );

        if ($asString === true) {
            return $adapterString;
        }

        $dir = $this->fileLocation . Util::DS . self::DIRECTORY;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return (bool)file_put_contents(
            $dir . Util::DS . $className . '.php',
            $adapterString
        );
    }
}
