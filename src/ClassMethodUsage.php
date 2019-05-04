<?php

declare(strict_types=1);

namespace UsageFinder;

use function sprintf;
use function str_replace;

final class ClassMethodUsage
{
    /** @var string */
    private $file;

    /** @var int */
    private $line;

    /** @var string */
    private $snippet;

    /** @var string */
    private $selectedText;

    public function __construct(
        string $file,
        int $line,
        string $snippet,
        string $selectedText
    ) {
        $this->file         = $file;
        $this->line         = $line;
        $this->snippet      = $snippet;
        $this->selectedText = $selectedText;
    }

    public function getFile() : string
    {
        return $this->file;
    }

    public function getLine() : int
    {
        return $this->line;
    }

    public function getConsoleSnippet() : string
    {
        return str_replace($this->selectedText, sprintf('<info>%s</info>', $this->selectedText), $this->snippet);
    }
}
