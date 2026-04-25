<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Object;

use ReflectionClass;

final class ObjectHelper
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(object $object, bool $deep = false): array
    {
        $reflection = new ReflectionClass($object);
        $result = [];
        foreach ($reflection->getProperties() as $property) {
            if (!$property->isInitialized($object)) {
                $result[$property->getName()] = null;

                continue;
            }
            $value = $property->getValue($object);
            if ($deep && is_object($value)) {
                $result[$property->getName()] = self::toArray($value, true);
            } elseif ($deep && is_array($value)) {
                $result[$property->getName()] = self::arrayDeep($value);
            } else {
                $result[$property->getName()] = $value;
            }
        }

        return $result;
    }

    /**
     * @template T of object
     * @param class-string<T>|T $classOrInstance
     * @param array<string, mixed> $data
     * @return T
     */
    public static function hydrate(string|object $classOrInstance, array $data): object
    {
        $reflection = new ReflectionClass($classOrInstance);

        $instance = is_string($classOrInstance) ? $reflection->newInstanceWithoutConstructor() : $classOrInstance;
        foreach ($data as $key => $value) {
            $current = $reflection;
            while ($current !== false) {
                if ($current->hasProperty($key)) {
                    $prop = $current->getProperty($key);
                    $prop->setValue($instance, $value);

                    break;
                }
                $current = $current->getParentClass();
            }
        }

        /** @var T $instance */
        return $instance;
    }

    public static function equals(object $a, object $b): bool
    {
        if ($a::class !== $b::class) {
            return false;
        }

        return self::toArray($a) == self::toArray($b);
    }

    public static function deepEquals(object $a, object $b): bool
    {
        if ($a::class !== $b::class) {
            return false;
        }

        return self::toArray($a, true) == self::toArray($b, true);
    }

    /**
     * @return array<string, mixed>
     */
    public static function publicProperties(object $object): array
    {
        $result = [];
        foreach (get_object_vars($object) as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param array<int|string, mixed> $array
     * @return array<int|string, mixed>
     */
    private static function arrayDeep(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $result[$key] = self::toArray($value, true);
            } elseif (is_array($value)) {
                $result[$key] = self::arrayDeep($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
