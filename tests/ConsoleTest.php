<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use Symfony\Component\Process\Process;

final class ConsoleTest extends TestCase
{
    public function testExecute() : void
    {
        $process = new Process([
            'bin/usage-finder',
            'find',
            'tests/example',
            'Doctrine\Common\Collections\Collection::slice',
        ], __DIR__ . '/..');

        $process->mustRun();

        $output = $process->getOutput();

        self::assertStringContainsString(
            'Searching for Doctrine\Common\Collections\Collection::slice in tests/example.',
            $output
        );

        self::assertStringContainsString(
            'Found usage in src/AppCode.php on line 14.',
            $output
        );

        self::assertStringContainsString(
            '->slice(0, 1);',
            $output
        );

        self::assertStringContainsString(
            'Found usage in src/AppCode.php on line 17.',
            $output
        );

        self::assertStringContainsString(
            '->slice(0, 2);',
            $output
        );

        self::assertStringContainsString(
            'Found usage in src/AppCode.php on line 20.',
            $output
        );

        self::assertStringContainsString(
            '->slice(0, 3);',
            $output
        );

        self::assertRegExp('/Finished in (.*)ms/', $output);
    }
}
