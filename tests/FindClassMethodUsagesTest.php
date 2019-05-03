<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use UsageFinder\ClassMethodReference;
use UsageFinder\ClassMethodUsage;
use UsageFinder\FindClassMethodUsages;
use function file_exists;

final class FindClassMethodUsagesTest extends TestCase
{
    public function testInvoke() : void
    {
        $rootDir      = __DIR__ . '/..';
        $composerPath = $rootDir . '/composer.phar';

        if (! file_exists($composerPath)) {
            self::markTestSkipped('Download composer with the ./download-composer.sh shell script.');
        }

        $process = new Process(['php', $composerPath, 'install'], __DIR__ . '/example');
        $process->run();

        echo $process->getOutput();

        $classMethodReference = new ClassMethodReference('Doctrine\Common\Collections\Collection::slice');

        $expectedUsages = [
            new ClassMethodUsage('src/AppCode.php', 14),
            new ClassMethodUsage('src/AppCode.php', 17),
            new ClassMethodUsage('src/AppCode.php', 20),
        ];

        $examplePath = __DIR__ . '/example';

        $usages = (new FindClassMethodUsages())->
            __invoke($examplePath, $classMethodReference);

        self::assertCount(3, $usages);
        self::assertEquals($expectedUsages, $usages);
    }
}
