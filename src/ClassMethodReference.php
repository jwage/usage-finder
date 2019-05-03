<?php

declare(strict_types=1);

namespace UsageFinder;

use InvalidArgumentException;
use function count;
use function explode;
use function sprintf;
use function trim;

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
        if ($name === '') {
            throw new InvalidArgumentException(
                'Invalid ClassMethodReference, empty string given. Format must be ClassName::methodName.'
            );
        }

        $this->name = $name;

        $e = explode('::', $this->name);

        if (count($e) < 2) {
            throw new InvalidArgumentException(
                sprintf('Invalid ClassMethodReference, "%s" given. Format must be ClassName::methodName.', $name)
            );
        }

        [$this->className, $this->methodName] = $e;

        $this->methodName = trim($this->methodName);

        if ($this->methodName === '') {
            throw new InvalidArgumentException(
                sprintf('You must specify a method name to find. "%s" given.', $name)
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
