<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use PHPUnit\Framework\TestCase;
use UsageFinder\CreateTemporaryPsalmXmlFile;
use function file_exists;
use function file_get_contents;

final class CreateTemporaryPsalmXmlFileTest extends TestCase
{
    public function testInvoke() : void
    {
        $path = (new CreateTemporaryPsalmXmlFile())->__invoke(__DIR__ . '/example');

        self::assertTrue(file_exists($path));

        $xml = file_get_contents($path);

        self::assertStringContainsString('<directory name="src" />', $xml);
        self::assertStringContainsString('<directory name="vendor" />', $xml);
    }
}
