<?php

declare(strict_types=1);

namespace UsageFinder;

use function is_dir;

final class GuessCodePath
{
    public function __invoke(string $path) : ?string
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

        return null;
    }
}
