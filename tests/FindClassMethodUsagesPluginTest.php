<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use Psalm\Codebase;
use Psalm\Context;
use Psalm\StatementsSource;
use function explode;
use function putenv;

final class FindClassMethodUsagesPluginTest extends TestCase
{
    public function testAfterMethodCallAnalysisMatches() : void
    {
        $codebase = $this->createMock(Codebase::class);

        $usages = $this->findUsages('Class::method', $codebase);

        self::assertSame(['Class::method'], $usages);
    }

    public function testAfterMethodCallAnalysisDoesNotMatch() : void
    {
        $codebase = $this->createMock(Codebase::class);

        $usages = $this->findUsages('Class::doesNotExist', $codebase);

        self::assertEmpty($usages);
    }

    public function testAfterMethodCallAnalysisImplementsInterface() : void
    {
        $codebase = $this->createMock(Codebase::class);

        $codebase->expects(self::once())
            ->method('classImplements')
            ->with('Class', 'AnotherClass')
            ->willReturn(true);

        $usages = $this->findUsages('AnotherClass::method', $codebase);

        self::assertSame(['Class::method'], $usages);
    }

    public function testAfterMethodCallAnalysisDoesNotImplementInterface() : void
    {
        $codebase = $this->createMock(Codebase::class);

        $codebase->expects(self::once())
            ->method('classImplements')
            ->with('Class', 'AnotherClass')
            ->willReturn(false);

        $usages = $this->findUsages('AnotherClass::method', $codebase);

        self::assertEmpty($usages);
    }

    /**
     * @return array<int, string>
     */
    private function findUsages(string $method, Codebase $codebase) : array
    {
        $methodCall       = $this->createMock(MethodCall::class);
        $methodCall->name = $this->createMock(Identifier::class);
        $context          = $this->createMock(Context::class);
        $statementsSource = $this->createMock(StatementsSource::class);

        $fileReplacements = [];

        [$className, $methodName] = explode('::', $method);

        putenv('USAGE_FINDER_NAME=' . $method);
        putenv('USAGE_FINDER_CLASS_NAME=' . $className);
        putenv('USAGE_FINDER_METHOD_NAME=' . $methodName);

        FindClassMethodUsagesPluginStub::afterMethodCallAnalysis(
            $methodCall,
            'Class::method',
            'Class::method',
            'Class::method',
            $context,
            $statementsSource,
            $codebase,
            $fileReplacements
        );

        $usages = FindClassMethodUsagesPluginStub::$usages;

        FindClassMethodUsagesPluginStub::$usages = [];

        return $usages;
    }
}
