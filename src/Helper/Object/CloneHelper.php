<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Object;

use Danilovl\HelperUtils\Exception\HelperException;
use Throwable;

final class CloneHelper
{
    /**
     * @template T of object
     * @param T $object
     * @return T
     */
    public static function shallowClone(object $object): object
    {
        return clone $object;
    }

    /**
     * @template T of object
     * @param T $object
     * @return T
     */
    public static function deepClone(object $object): object
    {
        try {
            $serialized = serialize($object);
        } catch (Throwable $e) {
            throw new HelperException('Cannot deep-clone object: ' . $e->getMessage(), 0, $e);
        }

        /** @var T $clone */
        $clone = unserialize($serialized);

        return $clone;
    }

    /**
     * @template T of object
     * @param iterable<T> $objects
     * @return list<T>
     */
    public static function shallowCloneAll(iterable $objects): array
    {
        $result = [];
        foreach ($objects as $object) {
            $result[] = clone $object;
        }

        return $result;
    }

    /**
     * @template T of object
     * @param iterable<T> $objects
     * @return list<T>
     */
    public static function deepCloneAll(iterable $objects): array
    {
        $result = [];
        foreach ($objects as $object) {
            $result[] = self::deepClone($object);
        }

        return $result;
    }

    /**
     * @template T of object
     * @param T $object
     * @param callable(T): void $modifier
     * @return T
     */
    public static function cloneWith(object $object, callable $modifier): object
    {
        $clone = clone $object;
        $modifier($clone);

        return $clone;
    }
}
