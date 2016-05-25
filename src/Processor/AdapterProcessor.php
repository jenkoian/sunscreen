<?php

namespace Jenko\Sunscreen\Processor;

class AdapterProcessor implements ProcessorInterface
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

        $interfaceName = $reflected->getShortName();
        $interfaceProperty = lcfirst($interfaceName);

        // TODO: Inject a factory into class then create methods processor through that.
        $methodsProcessor = new AdapterMethodsProcessor($reflected->getMethods(), $interfaceProperty);

        $adapterString = strtr(
            file_get_contents(__DIR__ . '/../../template/AdapterTemplate.tpl'),
            [
                '<namespace>'  => $this->namespace,
                '<interfaceFqn>' => $this->interfaceFqn,
                '<className>' => $this->makeClassName($interfaceName),
                '<interfaceName>' => $interfaceName,
                '<interfaceProperty>' => $interfaceProperty,
                '<methods>' => $methodsProcessor->generate(true)
            ]
        );

        if ($asString === true) {
            return $adapterString;
        }

        // TODO: Generate file 
    }
    
    private function makeClassName($interfaceName)
    {
        $interfaceFqnParts = explode('\\', $this->interfaceFqn);
        
        return reset($interfaceFqnParts) . str_replace('Interface', '', $interfaceName);
    }
}