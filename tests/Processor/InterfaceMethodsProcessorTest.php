<?php

namespace Jenko\Sunscreen\Tests\Processor;

use Jenko\Sunscreen\Processor\InterfaceMethodsProcessor;
use Jenko\Sunscreen\Tests\CustomAssertionsTrait;
use PHPUnit\Framework\TestCase;

class InterfaceMethodsProcessorTest extends TestCase
{
    use CustomAssertionsTrait;

    /**
     * @dataProvider getMethodsAndExpectedStrings
     * @param array $methods
     * @param string $expectedMethodsString
     */
    public function testProcessGeneratesExpectedMethodsString($methods, $expectedMethodsString)
    {
        $processor = new InterfaceMethodsProcessor($methods);

        self::assertStringMatchIgnoreWhitespace(
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
