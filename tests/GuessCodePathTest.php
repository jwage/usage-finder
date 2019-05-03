<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use UsageFinder\GuessCodePath;
use function sprintf;
use function sys_get_temp_dir;

final class GuessCodePathTest extends TestCase
{
    public function testInvoke() : void
    {
        self::assertSame('src', (new GuessCodePath())->__invoke(__DIR__ . '/example'));
    }

    public function testInvokeThrowsRuntimeException() : void
    {
        $path = sys_get_temp_dir() . '/usage-finder-does-not-exist';

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(sprintf('Could not guess code path for %s', $path));

        (new GuessCodePath())->__invoke($path);
    }
}
