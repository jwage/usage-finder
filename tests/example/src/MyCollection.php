<?php

declare(strict_types=1);

namespace Test;

use ArrayIterator;
use Closure;
use Doctrine\Common\Collections\Collection;
use Traversable;
use const ARRAY_FILTER_USE_BOTH;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_search;
use function array_slice;
use function array_values;
use function count;
use function current;
use function end;
use function in_array;
use function key;
use function next;
use function reset;
use function spl_object_hash;

final class MyCollection implements Collection
{
    /**
     * An array containing the entries of this collection.
     *
     * @psalm-var array<TKey,T>
     * @var array<mixed, mixed>
     */
    private $elements;

    /**
     * Initializes a new ArrayCollection.
     *
     * @param array<mixed, mixed> $elements
     *
     * @psalm-param array<TKey,T> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray() : array
    {
        return $this->elements;
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        return reset($this->elements);
    }

    /**
     * Creates a new instance from the specified elements.
     *
     * This method is provided for derived classes to specify how a new
     * instance should be created when constructor semantics have changed.
     *
     * @param array<mixed, mixed> $elements Elements.
     *
     * @return static
     *
     * @psalm-param array<TKey,T> $elements
     * @psalm-return ArrayCollection<TKey,T>
     */
    protected function createFrom(array $elements)
    {
        return new static($elements);
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        return end($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        if (! isset($this->elements[$key]) && ! array_key_exists($key, $this->elements)) {
            return null;
        }

        $removed = $this->elements[$key];
        unset($this->elements[$key]);

        return $removed;
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement($element) : bool
    {
        $key = array_search($element, $this->elements, true);

        if ($key === false) {
            return false;
        }

        unset($this->elements[$key]);

        return true;
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     *
     * @param int|string $offset
     */
    public function offsetExists($offset) : bool
    {
        return $this->containsKey($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * @param int|string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * @param int|string|null $offset
     * @param mixed           $value
     */
    public function offsetSet($offset, $value) : void
    {
        if ($offset === null) {
            $this->add($value);

            return;
        }

        $this->set($offset, $value);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * @param int|string $offset
     */
    public function offsetUnset($offset) : void
    {
        $this->remove($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key) : bool
    {
        return isset($this->elements[$key]) || array_key_exists($key, $this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function contains($element) : bool
    {
        return in_array($element, $this->elements, true);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(Closure $p) : bool
    {
        foreach ($this->elements as $key => $element) {
            if ($p($key, $element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        return array_search($element, $this->elements, true);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return $this->elements[$key] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys() : array
    {
        return array_keys($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues() : array
    {
        return array_values($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function count() : int
    {
        return count($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value) : void
    {
        $this->elements[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function add($element) : bool
    {
        $this->elements[] = $element;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty() : bool
    {
        return empty($this->elements);
    }

    /**
     * Required by interface IteratorAggregate.
     *
     * {@inheritDoc}
     *
     * @psalm-return Traversable<TKey, T>
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function map(Closure $func) : Collection
    {
        return $this->createFrom(array_map($func, $this->elements));
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     *
     * @psalm-return ArrayCollection<TKey,T>
     */
    public function filter(Closure $p) : Collection
    {
        return $this->createFrom(array_filter($this->elements, $p, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * {@inheritDoc}
     */
    public function forAll(Closure $p) : bool
    {
        foreach ($this->elements as $key => $element) {
            if (! $p($key, $element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function partition(Closure $p) : array
    {
        $matches = $noMatches = [];

        foreach ($this->elements as $key => $element) {
            if ($p($key, $element)) {
                $matches[$key] = $element;
            } else {
                $noMatches[$key] = $element;
            }
        }

        return [$this->createFrom($matches), $this->createFrom($noMatches)];
    }

    /**
     * Returns a string representation of this object.
     */
    public function __toString() : string
    {
        return self::class . '@' . spl_object_hash($this);
    }

    /**
     * {@inheritDoc}
     */
    public function clear() : void
    {
        $this->elements = [];
    }

    /**
     * {@inheritDoc}
     */
    public function slice(int $offset, ?int $length = null) : array
    {
        return array_slice($this->elements, $offset, $length, true);
    }
}
