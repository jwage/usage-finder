<?php

declare(strict_types=1);

namespace UsageFinder;

use InvalidArgumentException;
use function count;
use function explode;
use function sprintf;

final class ClassMethodReference
{
    /** @var string */
    private $name;

    /** @var string */
    private $className;

    /** @var string */
    private $methodName;

    public function __construct(string $name)
    {
        $this->name = $name;

        $e = explode('::', $this->name);

        if (count($e) < 2) {
            throw new InvalidArgumentException(
                sprintf('Invalid ClassMethodReference, %s given. Format must be ClassName::methodName.', $name)
            );
        }

        [$this->className, $this->methodName] = $e;

        if ($this->methodName === '') {
            throw new InvalidArgumentException(
                sprintf('You must specify a method name to find.', $name)
            );
        }
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getClassName() : string
    {
        return $this->className;
    }

    public function getMethodName() : string
    {
        return $this->methodName;
    }
}
