<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use UsageFinder\GuessCodePath;
use function sys_get_temp_dir;

final class GuessCodePathTest extends TestCase
{
    public function testInvoke() : void
    {
        self::assertSame('src', (new GuessCodePath())->__invoke(__DIR__ . '/example'));
    }

    public function testInvokeReturnsNull() : void
    {
        $path = sys_get_temp_dir() . '/usage-finder-does-not-exist';

        self::assertNull((new GuessCodePath())->__invoke($path));
    }
}
