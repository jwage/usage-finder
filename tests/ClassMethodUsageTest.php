<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use UsageFinder\ClassMethodUsage;

final class ClassMethodUsageTest extends TestCase
{
    public function testGetFile() : void
    {
        self::assertSame('src/Test.php', (new ClassMethodUsage('src/Test.php', 1, '', ''))->getFile());
    }

    public function testGetLine() : void
    {
        self::assertSame(1, (new ClassMethodUsage('src/Test.php', 1, '', ''))->getLine());
    }

    public function testGetConsoleSnippet() : void
    {
        self::assertSame(
            '$variable-><info>method</info>();',
            (new ClassMethodUsage('src/Test.php', 1, '$variable->method();', 'method'))->getConsoleSnippet()
        );
    }
}
