<?php

namespace Jenko\Sunscreen\Processor;

class AdapterMethodsProcessor implements ProcessorInterface
{
    /**
     * @var array
     */
    private $methods;

    /**
     * @var string
     */
    private $interfaceProperty;

    /**
     * @param array $methods
     * @param $interfaceProperty
     */
    public function __construct(array $methods, $interfaceProperty)
    {
        $this->methods = $methods;
        $this->interfaceProperty = $interfaceProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($asString = false)
    {
        $methodsString = '';
        /** @var \ReflectionMethod $method */
        foreach ($this->methods as $k => $method) {
            // Only want public methods on our interface.
            if (!$method->isPublic()) {
                continue;
            }

            $params = [];
            /** @var \ReflectionParameter $parameter */
            foreach ($method->getParameters() as $parameter) {
                $params[] = '$' . $parameter->getName();
            }

            $methodsString .= strtr(
                file_get_contents(__DIR__ . '/../../template/AdapterMethodsTemplate.tpl'),
                [
                    '<methodName>' => $method->getName(),
                    '<parameters>' => rtrim(implode(', ', $params), ','),
                    '<interfaceProperty>' => $this->interfaceProperty
                ]
            );
        }

        if ($asString === true) {
            return $methodsString;   
        }
        
        // TODO: Generate file or throw exception?
    }
}