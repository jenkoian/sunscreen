<?php

namespace Jenko\Sunscreen\Processor;

class ClassProcessor implements ProcessorInterface 
{
    /**
     * @var string
     */
    const DIRECTORY = 'Contracts';
    
    /**
     * @var string
     */
    private $classFqn;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $fileLocation;

    /**
     * @param string $classFqn
     * @param string $namespace
     * @param string $fileLocation
     */
    public function __construct($classFqn, $namespace, $fileLocation)
    {
        $this->classFqn = $classFqn;
        $this->namespace = $namespace;
        $this->fileLocation = $fileLocation;
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

        if ($asString === true) {
            return $classString;
        }
        
        $dir = $this->fileLocation . self::DIRECTORY;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }        

        return (bool)file_put_contents(
            $dir . DIRECTORY_SEPARATOR . $class . '.php',
            $classString
        );
    }
}
