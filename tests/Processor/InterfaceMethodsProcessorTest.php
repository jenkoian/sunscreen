<?php

namespace Jenko\Sunscreen\Tests\Processor;

use Jenko\Sunscreen\Processor\InterfaceMethodsProcessor;

class InterfaceMethodsProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getMethodsAndExpectedStrings
     * @param array $methods
     * @param string $expectedMethodsString
     */
    public function testProcessGeneratesExpectedMethodsString($methods, $expectedMethodsString)
    {
        $processor = new InterfaceMethodsProcessor($methods);

        \PHPUnit_Extensions_Assert_More::assertStringMatchIgnoreWhitespace(
            $expectedMethodsString,
            $processor->process(true)
        );
    }

    public function testProcessingWithoutStringFlagThrowsException()
    {
        $processor = new InterfaceMethodsProcessor([]);

        $this->expectException('\LogicException');
        $processor->process();
    }

    public function getMethodsAndExpectedStrings()
    {
        return [
            [
                [new \ReflectionMethod('Jenko\Sunscreen\Tests\Fixtures\BazInterface', 'bar')],
                'public function bar();'
            ]
        ];
    }
}
