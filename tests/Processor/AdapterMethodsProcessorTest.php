<?php

namespace Jenko\Sunscreen\Tests\Processor;

use Jenko\Sunscreen\Processor\AdapterMethodsProcessor;
use Jenko\Sunscreen\Tests\CustomAssertionsTrait;
use Jenko\Sunscreen\Tests\Fixtures\Foo;
use PHPUnit\Framework\TestCase;

class AdapterMethodsProcessorTest extends TestCase
{
    use CustomAssertionsTrait;

    /**
     * @dataProvider getMethodsAndExpectedStrings
     * @param array $methods
     * @param string $expectedMethodsString
     */
    public function testProcessGeneratesExpectedMethodsString($methods, $expectedMethodsString)
    {
        $processor = new AdapterMethodsProcessor($methods, 'myInterface');

        self::assertStringMatchIgnoreWhitespace(
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
