<?php

namespace Jenko\Sunscreen\Tests\Processor;

use Jenko\Sunscreen\Processor\AdapterMethodsProcessor;
use Jenko\Sunscreen\Tests\Fixtures\Foo;

class AdapterMethodsProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getMethodsAndExpectedStrings
     * @param array $methods
     * @param string $expectedMethodsString
     */
    public function testProcessGeneratesExpectedMethodsString($methods, $expectedMethodsString)
    {
        $processor = new AdapterMethodsProcessor($methods, 'myInterface');

        \PHPUnit_Extensions_Assert_More::assertStringMatchIgnoreWhitespace(
            $expectedMethodsString,
            $processor->process(true)
        );
    }

    public function testProcessingWithoutStringFlagThrowsException()
    {
        $processor = new AdapterMethodsProcessor([], 'myInterface');

        $this->expectException('\LogicException');
        $processor->process();
    }

    public function getMethodsAndExpectedStrings()
    {
        return [
            [
                [new \ReflectionMethod(new Foo(), 'foo')],
                '
    /**
     * {@inheritdoc}
     */
    public function foo()
    {
        return $this->myInterface->foo();
    }
'
            ]
        ];
    }
}
