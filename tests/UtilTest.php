<?php

namespace Jenko\Sunscreen\Tests;

use Composer\Package\Package;
use Jenko\Sunscreen\Util;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getAutoloadsAndExpectedNamespaces
     * @param $autoload
     * @param $expectedNamespace
     */
    public function testExtractNamespaceFromPackageReturnsExpectedNamespace($autoload, $expectedNamespace)
    {
        $package = new Package('a', '1.0.0', '1.0');
        $package->setAutoload($autoload);

        self::assertEquals($expectedNamespace, Util::extractNamespaceFromPackage($package));
    }

    public function getAutoloadsAndExpectedNamespaces()
    {
        return [
            [['psr-4' => ['Foo\\' => 'src']], 'Foo'],
            [['psr-4' => ['Foo\\Bar\\' => 'src']], 'Foo\\Bar'],
            [['psr-4' => ['Foo\\Bar\\' => 'foo/bar/src']], 'Foo\\Bar'],
            [['psr-0' => ['Foo\\' => 'foo/src']], 'Foo'],
            [['psr-0' => ['Foo\\Bar\\' => 'foo/bar/src']], 'Foo\\Bar'],
            [['psr-9999' => ['Foo\\Bar\\' => 'foo/bar/src']], ''],
        ];
    }

    /**
     * @dataProvider getAutoloadsAndExpectedSrcDirs
     * @param $autoload
     * @param $expectedSrcDir
     */
    public function testExtractSourceDirectoryFromPackage($autoload, $expectedSrcDir)
    {
        $package = new Package('a', '1.0.0', '1.0');
        $package->setAutoload($autoload);

        self::assertEquals($expectedSrcDir, Util::extractSourceDirectoryFromPackage($package));
    }

    public function getAutoloadsAndExpectedSrcDirs()
    {
        return [
            [['psr-4' => ['Foo\\' => 'src']], 'src'],
            [['psr-4' => ['Foo\\Bar\\' => 'src']], 'src'],
            [['psr-4' => ['Foo\\Bar\\' => 'foo/bar/src']], 'foo/bar/src'],
            [['psr-4' => ['Foo\\Bar\\' => ['foo/bar/src']]], 'foo/bar/src'],
            [['psr-4' => ['Foo\\Bar\\' => ['foo/bar/src']]], 'foo/bar/src'],
            [['psr-0' => ['Foo\\' => 'foo/src']], 'foo/src'],
            [['psr-0' => ['Foo\\Bar\\' => 'foo/bar/src']], 'foo/bar/src'],
            [['psr-0' => ['Foo\\Bar\\' => ['foo/bar/src']]], 'foo/bar/src'],
            [['psr-9999' => ['Foo\\Bar\\' => 'foo/bar/src']], ''],
        ];
    }

    /**
     * @dataProvider getInterfaceFqnAndExpectedClassName
     *
     * @param $interfaceFqn
     * @param $expectedClassName
     */
    public function testMakeClassNameReturnsExpectedClassName($interfaceFqn, $expectedClassName)
    {
        self::assertEquals($expectedClassName, Util::makeClassName($interfaceFqn));
    }

    public function getInterfaceFqnAndExpectedClassName()
    {
        return [
            ['Jenko\Sunscreen\Tests\Fixtures\BazInterface', 'JenkoBaz']
        ];
    }
}