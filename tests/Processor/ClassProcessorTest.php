<?php

namespace Jenko\Sunscreen\Tests\Processor;

use Jenko\Sunscreen\Processor\ClassProcessor;
use Jenko\Sunscreen\Util;

class ClassProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getClassAndExpectedStrings
     *
     * @param $classFqn
     * @param $expectedClassString
     */
    public function testProcessGeneratesExpectedClassString($classFqn, $expectedClassString)
    {
        $namespace = 'TestProject';
        $fileLocation = __DIR__ . Util::DS . '..' . Util::DS . 'Fixtures';

        $processor = new ClassProcessor($classFqn, $namespace, $fileLocation);

        \PHPUnit_Extensions_Assert_More::assertStringMatchIgnoreWhitespace(
            $expectedClassString,
            $processor->process(true)
        );
    }

    public function getClassAndExpectedStrings()
    {
        return [
            [
                'Jenko\Sunscreen\Tests\Fixtures\AbstractFoo',
                '
<?php

namespace TestProject;

abstract class AbstractFoo
{
    abstract public function foo();
}
                
                '
            ]
        ];
    }
}
