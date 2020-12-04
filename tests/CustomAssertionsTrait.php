<?php

namespace Jenko\Sunscreen\Tests;

trait CustomAssertionsTrait
{
    public static function assertStringMatchIgnoreWhitespace( string $expecting, string $actual )
    {
        $expected = preg_replace('#\&. #', '', implode(' ', preg_split('/\s+/', trim($expecting))));
        $actual = preg_replace('#\&. #', '', implode(' ', preg_split('/\s+/', trim($actual))));

        return self::assertEquals(
            $expected,
            $actual
        );
    }
}
