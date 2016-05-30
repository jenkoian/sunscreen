<?php

namespace Jenko\Sunscreen\Tests\Processor;

use Jenko\Sunscreen\Processor\InterfaceProcessor;
use Jenko\Sunscreen\Util;

class InterfaceProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getInterfaceAndExpectedStrings
     *
     * @param $interfaceFqn
     * @param $expectedClassString
     */
    public function testProcessGeneratesExpectedInterfaceString($interfaceFqn, $expectedClassString)
    {
        $namespace = 'TestProject';
        $fileLocation = __DIR__ . Util::DS . '..' . Util::DS . 'Fixtures';

        $processor = new InterfaceProcessor($interfaceFqn, $namespace, $fileLocation);

        \PHPUnit_Extensions_Assert_More::assertStringMatchIgnoreWhitespace(
            $expectedClassString,
            $processor->process(true)
        );
    }

    public function getInterfaceAndExpectedStrings()
    {
        return [
            [
                'Jenko\Sunscreen\Tests\Fixtures\BazInterface',
                '
<?php

namespace TestProject;

interface BazInterface
{
    public function bar();
}
                
                '
            ]
        ];
    }
}
