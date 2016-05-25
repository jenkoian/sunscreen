<?php

namespace Jenko\Sunscreen\Processor;

class InterfaceMethodsProcessor implements ProcessorInterface
{
    /**
     * @var array
     */
    private $methods;

    /**
     * @param array $methods
     */
    public function __construct(array $methods)
    {
        $this->methods = $methods;
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
                file_get_contents(__DIR__ . '/../../template/InterfaceMethodsTemplate.tpl'),
                [
                    '<docBlock>'  => $method->getDocComment(),
                    '<methodName>' => $method->getName(),
                    '<parameters>' => rtrim(implode(', ', $params), ',')
                ]
            );

            if ($k !== count($this->methods)-1) {
                $methodsString .= "\n";
            }
        }

        if ($asString === true) {
            return $methodsString;   
        }
        
        // TODO: Generate file or throw exception?
    }
}