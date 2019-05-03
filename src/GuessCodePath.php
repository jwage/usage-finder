<?php

declare(strict_types=1);

namespace UsageFinder;

use RuntimeException;
use function is_dir;
use function sprintf;

final class GuessCodePath
{
    public function __invoke(string $path) : string
    {
        $checks = [
            'lib',
            'src',
        ];

        foreach ($checks as $check) {
            $dir = $path . '/' . $check;

            if (is_dir($dir)) {
                return $check;
            }
        }

        throw new RuntimeException(sprintf('Could not guess code path for %s', $path));
    }
}
