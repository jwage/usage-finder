<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use PHPUnit\Framework\TestCase;
use UsageFinder\ClassMethodReference;

final class ClassMethodReferenceTest extends TestCase
{
    public function testInvoke() : void
    {
        self::assertSame('Doctrine\Common\Collections\Collection::slice', (new ClassMethodReference('Doctrine\Common\Collections\Collection::slice'))->getName());
    }
}
