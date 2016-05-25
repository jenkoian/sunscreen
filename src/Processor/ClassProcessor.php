<?php

namespace Jenko\Sunscreen\Processor;

class ClassProcessor implements ProcessorInterface 
{
    /**
     * @var string
     */
    private $classFqn;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @param string $classFqn
     * @param string $namespace
     */
    public function __construct($classFqn, $namespace)
    {
        $this->classFqn = $classFqn;
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($asString = false)
    {
        $reflected = new \ReflectionClass($this->classFqn);
        
        // TODO: Inject a factory into class then create methods processor through that.
        $methodsProcessor = new InterfaceMethodsProcessor($reflected->getMethods());

        $class = $reflected->getShortName();
        $methods = $methodsProcessor->generate();

        $classString = strtr(
            file_get_contents(__DIR__ . '/../../template/ClassTemplate.tpl'),
            [
                '<namespace>'  => $this->namespace,
                '<className>' => $class,
                '<methods>' => $methods
            ]
        );

        return $classString;
    }
}