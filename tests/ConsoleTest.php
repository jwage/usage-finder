<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\Process;
use UsageFinder\Command\FindClassMethodUsagesCommand;

final class ConsoleTest extends TestCase
{
    public function testCommand() : void
    {
        $application = new Application();
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'find',
            'path' => 'tests/example',
            'find' => 'Doctrine\Common\Collections\Collection::slice',
        ]);

        $output = new BufferedOutput();

        $application->add(new FindClassMethodUsagesCommand());

        $application->run($input, $output);

        self::assertProcessOutput($output->fetch());
    }

    public function testBin() : void
    {
        $process = new Process([
            'bin/usage-finder',
            'find',
            'tests/example',
            'Doctrine\Common\Collections\Collection::slice',
        ], __DIR__ . '/..');

        $process->mustRun();

        $output = $process->getOutput();

        self::assertProcessOutput($output);
    }

    private static function assertProcessOutput(string $output) : void
    {
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
