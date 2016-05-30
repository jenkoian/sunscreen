<?php

namespace Jenko\Sunscreen\Processor;

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
    public function generate($asString = false)
    {
        $reflected = new \ReflectionClass($this->interfaceFqn);

        $interfaceName = $reflected->getShortName();
        $className = $this->makeClassName($interfaceName);
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
                '<methods>' => $methodsProcessor->generate(true)
            ]
        );

        if ($asString === true) {
            return $adapterString;
        }

        $dir = $this->fileLocation . self::DIRECTORY;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return (bool)file_put_contents(
            $dir . DIRECTORY_SEPARATOR . $className . '.php',
            $adapterString
        );
    }
    
    private function makeClassName($interfaceName)
    {
        $interfaceFqnParts = explode('\\', $this->interfaceFqn);
        
        return reset($interfaceFqnParts) . str_replace('Interface', '', $interfaceName);
    }
}
