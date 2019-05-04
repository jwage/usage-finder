<?php

declare(strict_types=1);

namespace UsageFinder;

use RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use UsageFinder\Exception\PsalmError;
use function array_filter;
use function array_map;
use function copy;
use function is_array;
use function json_decode;
use function realpath;
use function sprintf;
use function trim;
use function unlink;

final class FindClassMethodUsages
{
    /**
     * @return array<int, ClassMethodUsage>
     */
    public function __invoke(
        string $path,
        ClassMethodReference $classMethodReference,
        int $threads = 1
    ) : array {
        $this->copyFindClassMethodUsagesPlugin($path);

        $configFile = (new CreateTemporaryPsalmXmlFile())->__invoke($path);

        $rootDir = realpath(__DIR__ . '/..');

        $process = new Process([
            'vendor/bin/psalm',
            sprintf('--config=%s', $configFile),
            sprintf('--root=%s', $path),
            sprintf('--threads=%d', $threads),
            '--output-format=json',
            '--no-cache',
        ], $rootDir, [
            'USAGE_FINDER_NAME'        => $classMethodReference->getName(),
            'USAGE_FINDER_CLASS_NAME'  => $classMethodReference->getClassName(),
            'USAGE_FINDER_METHOD_NAME' => $classMethodReference->getMethodName(),
        ]);
        $process->setTimeout(0);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            $processOutput = $this->getPsalmProcessOutput($process);

            if ($processOutput === null) {
                throw new PsalmError($e->getMessage());
            }
        } finally {
            $this->cleanupFindClassMethodUsagesPlugin($path);

            unlink($configFile);
        }

        return $this->buildClassMethodUsages($process);
    }

    /**
     * @param array<int, array<string, mixed>> $results
     *
     * @return array<int, array<string, mixed>>
     */
    private function filterClassMethodUsages(array $results) : array
    {
        return array_filter($results, static function (array $result) : bool {
            return $result['type'] === 'ClassMethodUsageFound';
        });
    }

    /**
     * @return array<int, ClassMethodUsage>
     */
    private function buildClassMethodUsages(Process $process) : array
    {
        $processOutput = $this->getPsalmProcessOutput($process);

        if ($processOutput === null) {
            throw new RuntimeException(sprintf('Unknown error. Psalm returned invalid JSON, Returned the following: %s', $process->getOutput()));
        }

        return array_map(static function (array $result) : ClassMethodUsage {
            return new ClassMethodUsage(
                $result['file_name'],
                $result['line_from'],
                $result['snippet'],
                $result['selected_text']
            );
        }, $this->filterClassMethodUsages($processOutput));
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    private function getPsalmProcessOutput(Process $process) : ?array
    {
        $rawProcessOutput = trim($process->getOutput());

        if ($rawProcessOutput === '') {
            return [];
        }

        $processOutput = json_decode($rawProcessOutput, true);

        if (! is_array($processOutput)) {
            return null;
        }

        if (! isset($processOutput[0]['message'])) {
            return null;
        }

        return $processOutput;
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
