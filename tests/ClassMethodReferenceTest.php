<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use InvalidArgumentException;
use UsageFinder\ClassMethodReference;

final class ClassMethodReferenceTest extends TestCase
{
    public function testGetName() : void
    {
        self::assertSame(
            'Doctrine\Common\Collections\Collection::slice',
            (new ClassMethodReference('Doctrine\Common\Collections\Collection::slice'))->getName()
        );
    }

    public function testGetClassName() : void
    {
        self::assertSame(
            'Doctrine\Common\Collections\Collection',
            (new ClassMethodReference('Doctrine\Common\Collections\Collection::slice'))->getClassName()
        );
    }

    public function testGetMethodName() : void
    {
        self::assertSame(
            'slice',
            (new ClassMethodReference('Doctrine\Common\Collections\Collection::slice'))->getMethodName()
        );
    }

    /**
     * @dataProvider provideForTestInvalidClassMethodReference
     */
    public function testInvalidClassMethodReference(string $input, string $message) : void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage($message);

        new ClassMethodReference($input);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function provideForTestInvalidClassMethodReference() : array
    {
        return [
            [
                '',
                'Invalid ClassMethodReference, empty string given. Format must be ClassName::methodName.',
            ],
            [
                'Doctrine\Common\Collections\Collection::',
                'You must specify a method name to find. "Doctrine\Common\Collections\Collection::" given.',
            ],
            [
                'Doctrine\Common\Collections\Collection:: ',
                'You must specify a method name to find. "Doctrine\Common\Collections\Collection:: " given.',
            ],
            [
                'Doctrine\Common\Collections\Collection',
                'Invalid ClassMethodReference, "Doctrine\Common\Collections\Collection" given. Format must be ClassName::methodName.',
            ],
        ];
    }
}
