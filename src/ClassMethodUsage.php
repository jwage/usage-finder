<?php

declare(strict_types=1);

namespace UsageFinder;

final class ClassMethodUsage
{
    /** @var string */
    private $file;

    /** @var int */
    private $line;

    public function __construct(string $file, int $line)
    {
        $this->file = $file;
        $this->line = $line;
    }

    public function getFile() : string
    {
        return $this->file;
    }

    public function getLine() : int
    {
        return $this->line;
    }
}
