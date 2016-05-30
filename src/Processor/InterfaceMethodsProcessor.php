<?php

namespace Jenko\Sunscreen\Processor;

class InterfaceMethodsProcessor implements ProcessorInterface
{
    /**
     * @var array
     */
    private $methods;

    /**
     * @var bool
     */
    private $isInterface;

    /**
     * @param array $methods
     * @param bool $isInterface
     */
    public function __construct(array $methods, $isInterface = true)
    {
        $this->methods = $methods;
        $this->isInterface = $isInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function process($asString = false)
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
                    '<abstract>' => $method->isAbstract() && !$this->isInterface ? 'abstract ' : '',
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

        throw new \LogicException('Interface methods cannot be generated as a file.');
    }
}