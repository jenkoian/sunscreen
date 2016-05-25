<?php

namespace Jenko\Sunscreen\Processor;

class InterfaceProcessor implements ProcessorInterface 
{
    /**
     * @var string
     */
    private $interfaceFqn;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @param string $interfaceFqn
     * @param string $namespace
     */
    public function __construct($interfaceFqn, $namespace)
    {
        $this->interfaceFqn = $interfaceFqn;
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($asString = false)
    {
        $reflected = new \ReflectionClass($this->interfaceFqn);
        
        // TODO: Inject a factory into class then create methods processor through that.
        $methodsProcessor = new InterfaceMethodsProcessor($reflected->getMethods());

        $interface = $reflected->getShortName();
        $methods = $methodsProcessor->generate(true);

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

        // TODO: Generate file
        return true;
    }
}