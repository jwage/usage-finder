<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use UsageFinder\ClassMethodReference;
use UsageFinder\ClassMethodUsage;
use UsageFinder\FindClassMethodUsages;

final class FindClassMethodUsagesTest extends TestCase
{
    public function testInvokeUsagesFoundConcreteClass() : void
    {
        $classMethodReference = new ClassMethodReference('Doctrine\Common\Collections\ArrayCollection::slice');

        $expectedUsages = [
            new ClassMethodUsage('src/AppCode.php', 14, '            ->slice(0, 1);', 'slice'),
            new ClassMethodUsage('src/AppCode.php', 20, '            ->slice(0, 3);', 'slice'),
        ];

        $examplePath = __DIR__ . '/example';

        $usages = (new FindClassMethodUsages())->
            __invoke($examplePath, $classMethodReference, 2);

        self::assertCount(2, $usages);
        self::assertEquals($expectedUsages, $usages);
    }

    public function testInvokeUsagesFoundInterface() : void
    {
        $classMethodReference = new ClassMethodReference('Doctrine\Common\Collections\Collection::slice');

        $expectedUsages = [
            new ClassMethodUsage('src/AppCode.php', 14, '            ->slice(0, 1);', 'slice'),
            new ClassMethodUsage('src/AppCode.php', 17, '            ->slice(0, 2);', 'slice'),
            new ClassMethodUsage('src/AppCode.php', 20, '            ->slice(0, 3);', 'slice'),
        ];

        $examplePath = __DIR__ . '/example';

        $usages = (new FindClassMethodUsages())->
            __invoke($examplePath, $classMethodReference, 2);

        self::assertCount(3, $usages);
        self::assertEquals($expectedUsages, $usages);
    }

    public function testInvokeNoUsagesFound() : void
    {
        $classMethodReference = new ClassMethodReference('Class::doesNotExist');

        $examplePath = __DIR__ . '/example';

        $usages = (new FindClassMethodUsages())->
            __invoke($examplePath, $classMethodReference, 2);

        self::assertCount(0, $usages);
    }

    public function testInvokeOnInvalidCode() : void
    {
        $classMethodReference = new ClassMethodReference('Class::doesNotExist');

        $examplePath = __DIR__ . '/invalid-code-example';

        $usages = (new FindClassMethodUsages())->
            __invoke($examplePath, $classMethodReference, 2);

        self::assertCount(0, $usages);
    }

    public function testInvokeNoProblems() : void
    {
        $classMethodReference = new ClassMethodReference('Class::doesNotExist');

        $examplePath = __DIR__ . '/no-problems-example';

        $usages = (new FindClassMethodUsages())->
            __invoke($examplePath, $classMethodReference, 2);

        self::assertCount(0, $usages);
    }
}
