<?php

namespace Jenko\Sunscreen\Tests\Processor;

use Jenko\Sunscreen\Processor\ClassProcessor;
use Jenko\Sunscreen\Tests\CustomAssertionsTrait;
use Jenko\Sunscreen\Util;
use PHPUnit\Framework\TestCase;

class ClassProcessorTest extends TestCase
{
    use CustomAssertionsTrait;

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

        self::assertStringMatchIgnoreWhitespace(
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
