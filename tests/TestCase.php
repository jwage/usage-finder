<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Symfony\Component\Process\Process;
use function file_exists;
use function is_dir;

class TestCase extends PHPUnitTestCase
{
    /** @var string */
    protected $rootDir;

    protected function setUp() : void
    {
        $this->rootDir = __DIR__ . '/..';
        $composerPath  = $this->rootDir . '/composer.phar';

        if (! file_exists($composerPath)) {
            self::markTestSkipped('Download composer with the ./download-composer.sh shell script.');
        }

        if (is_dir(__DIR__ . '/example/vendor')) {
            return;
        }

        $process = new Process(['php', $composerPath, 'install'], __DIR__ . '/example');
        $process->run();
    }
}
