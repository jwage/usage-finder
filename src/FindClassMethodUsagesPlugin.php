<?php

declare(strict_types=1);

namespace UsageFinder;

use InvalidArgumentException;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Context;
use Psalm\FileManipulation;
use Psalm\IssueBuffer;
use Psalm\Plugin\Hook\AfterMethodCallAnalysisInterface;
use Psalm\StatementsSource;
use Psalm\Type\Union;
use UsageFinder\Issue\ClassMethodUsageFound;
use function explode;
use function getenv;
use function sprintf;
use function strtolower;

class FindClassMethodUsagesPlugin implements AfterMethodCallAnalysisInterface
{
    /**
     * @param  MethodCall|StaticCall $expr
     * @param  FileManipulation[]    $file_replacements
     */
    public static function afterMethodCallAnalysis(
        Expr $expr,
        string $method_id,
        string $appearing_method_id,
        string $declaring_method_id,
        Context $context,
        StatementsSource $statements_source,
        Codebase $codebase,
        array &$file_replacements = [],
        ?Union &$return_type_candidate = null
    ) : void {
        if (! $expr instanceof MethodCall) {
            return;
        }

        if (! self::isMethodWeWant($declaring_method_id, $codebase)) {
            return;
        }

        static::bufferClassMethodUsage($expr, $statements_source, $declaring_method_id);
    }

    protected static function bufferClassMethodUsage(
        MethodCall $methodCall,
        StatementsSource $statements_source,
        string $declaring_method_id
    ) : void {
        IssueBuffer::accepts(
            new ClassMethodUsageFound(
                sprintf('Found reference to %s', $declaring_method_id),
                new CodeLocation($statements_source, $methodCall->name)
            ),
            $statements_source->getSuppressedIssues()
        );
    }

    private static function isMethodWeWant(string $declaring_method_id, Codebase $codebase) : bool
    {
        [$className, $methodName] = explode('::', $declaring_method_id);

        if (strtolower($declaring_method_id) === strtolower(self::getFindName())) {
            return true;
        }

        return strtolower($methodName) === strtolower(self::getFindMethodName())
            && $codebase->classImplements($className, self::getFindClassName());
    }

    private static function getFindName() : string
    {
        return self::getEnv('USAGE_FINDER_NAME');
    }

    private static function getFindClassName() : string
    {
        return self::getEnv('USAGE_FINDER_CLASS_NAME');
    }

    private static function getFindMethodName() : string
    {
        return self::getEnv('USAGE_FINDER_METHOD_NAME');
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function getEnv(string $name) : string
    {
        $value = getenv($name);

        if ($value === false) {
            throw new InvalidArgumentException(sprintf('Missing env variable %s.', $name));
        }

        return $value;
    }
}
