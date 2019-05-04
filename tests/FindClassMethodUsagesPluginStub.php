<?php

declare(strict_types=1);

namespace UsageFinder\Tests;

use PhpParser\Node\Expr\MethodCall;
use Psalm\StatementsSource;
use UsageFinder\FindClassMethodUsagesPlugin;

final class FindClassMethodUsagesPluginStub extends FindClassMethodUsagesPlugin
{
    /** @var array<int, string> */
    public static $usages = [];

    protected static function bufferClassMethodUsage(
        MethodCall $methodCall,
        StatementsSource $statements_source,
        string $declaring_method_id
    ) : void {
        self::$usages[] = $declaring_method_id;
    }
}
