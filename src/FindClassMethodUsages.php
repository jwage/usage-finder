<?php

declare(strict_types=1);

namespace UsageFinder;

use Symfony\Component\Process\Process;
use function copy;
use function json_decode;
use function realpath;
use function sprintf;
use function unlink;

final class FindClassMethodUsages
{
    /**
     * @return array<int, ClassMethodUsage>
     */
    public function __invoke(string $path, ClassMethodReference $classMethodReference) : array
    {
        $this->copyFindClassMethodUsagesPlugin($path);

        $configFile = (new CreateTemporaryPsalmXmlFile())->__invoke($path);

        $rootDir = realpath(__DIR__ . '/..');

        $process = new Process([
            'vendor/bin/psalm',
            sprintf('--config=%s', $configFile),
            sprintf('--root=%s', $path),
            '--output-format=json',
        ], $rootDir, [
            'USAGE_FINDER_NAME'        => $classMethodReference->getName(),
            'USAGE_FINDER_CLASS_NAME'  => $classMethodReference->getClassName(),
            'USAGE_FINDER_METHOD_NAME' => $classMethodReference->getMethodName(),
        ]);

        $process->mustRun();

        $this->cleanupFindClassMethodUsagesPlugin($path);

        $output = $process->getOutput();

        unlink($configFile);

        if ($output === '') {
            return [];
        }

        $results = json_decode($output, true);

        $usages = [];

        foreach ($results as $result) {
            $usages[] = new ClassMethodUsage(
                $result['file_name'],
                $result['line_from']
            );
        }

        return $usages;
    }

    private function copyFindClassMethodUsagesPlugin(string $path) : void
    {
        copy(__DIR__ . '/FindClassMethodUsagesPlugin.php', $path . '/FindClassMethodUsagesPlugin.php');
    }

    private function cleanupFindClassMethodUsagesPlugin(string $path) : void
    {
        unlink($path . '/FindClassMethodUsagesPlugin.php');
    }
}
