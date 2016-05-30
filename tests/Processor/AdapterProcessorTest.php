<?php

namespace Jenko\Sunscreen\Tests\Processor;

use Jenko\Sunscreen\Processor\AdapterProcessor;
use Jenko\Sunscreen\Util;

class AdapterProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getInterfaceAndExpectedStrings
     *
     * @param $interfaceFqn
     * @param $expectedAdapterString
     */
    public function testProcessGeneratesExpectedAdapterString($interfaceFqn, $expectedAdapterString)
    {
        $namespace = 'TestProject';
        $fileLocation = __DIR__ . Util::DS . '..' . Util::DS . 'Fixtures';

        $processor = new AdapterProcessor($interfaceFqn, $namespace, $fileLocation);

        \PHPUnit_Extensions_Assert_More::assertStringMatchIgnoreWhitespace($expectedAdapterString, $processor->process(true));
    }

    public function getInterfaceAndExpectedStrings()
    {
        return [
            [
                'Jenko\Sunscreen\Tests\Fixtures\BazInterface',
                '
<?php

namespace TestProject;

use Jenko\Sunscreen\Tests\Fixtures\BazInterface;
use TestProject\BazInterface as LocalBazInterface;

final class JenkoBaz implements LocalBazInterface
{
    /**
     * @var BazInterface
     */
    private $bazInterface;

    /**
     * @var BazInterface $bazInterface
     */
    public function __construct(BazInterface $bazInterface)
    {
        $this->bazInterface = $bazInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function bar()
    {
        return $this->bazInterface->bar();
    }
    
}
                
                '
            ]
        ];
    }
}
