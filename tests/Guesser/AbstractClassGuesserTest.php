<?php

namespace Jenko\Sunscreen\Tests\Guesser;

use Composer\Package\Package;
use Jenko\Sunscreen\Guesser\AbstractClassGuesser;
use Jenko\Sunscreen\Util;
use PHPUnit\Framework\TestCase;

class AbstractClassGuesserTest extends TestCase
{
    /**
     * @dataProvider getAutoloadsAndExpectedClasses
     * @param $autoload
     * @param $expectedClasses
     */
    public function testGuessReturnsOnlyAbstractClasses($autoload, $expectedClasses)
    {
        $package = new Package('a', '1.0.0', '1.0');
        $package->setAutoload($autoload);

        $vendorDir = __DIR__ . Util::DS . '..' . Util::DS . 'Fixtures' . Util::DS . 'vendor';
        $guesser = new AbstractClassGuesser($vendorDir);

        self::assertEquals($expectedClasses, $guesser->guess($package));
    }

    public function getAutoloadsAndExpectedClasses()
    {
        return [
            [['psr-4' => ['Foo\\' => 'src/']], ['Foo\AbstractBar']],
            [['psr-0' => ['Foo\\' => 'src/foo/']], ['Foo\AbstractBar']],
            [['psr-0' => ['Foo\\Bar\\' => 'src/foo/bar/']], ['Foo\Bar\AbstractBaz']],
            [['psr-9999' => ['Foo\\Bar\\' => 'foo/bar/src/']], []],
        ];
    }
}
