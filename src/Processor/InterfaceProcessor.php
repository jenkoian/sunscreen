<?php

namespace Jenko\Sunscreen\Processor;

use Jenko\Sunscreen\Util;

class InterfaceProcessor implements ProcessorInterface 
{
    /**
     * @var string
     */
    const DIRECTORY = 'Contracts';
    
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
        
        // TODO: Inject a factory into class then create methods processor through that.
        $methodsProcessor = new InterfaceMethodsProcessor($reflected->getMethods());

        $interface = $reflected->getShortName();
        $methods = $methodsProcessor->process(true);

        $interfaceString = strtr(
            file_get_contents(__DIR__ . '/../../template/InterfaceTemplate.tpl'),
            [
                '<namespace>'  => $this->namespace,
                '<interfaceName>' => $interface,
                '<methods>' => $methods
            ]
        );

        if ($asString === true) {
            return $interfaceString;
        }

        $dir = $this->fileLocation . Util::DS . self::DIRECTORY;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return (bool)file_put_contents(
            $dir . Util::DS . $interface.'.php',
            $interfaceString
        );
    }
}
