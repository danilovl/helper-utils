<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Reflection;

use Danilovl\HelperUtils\Exception\HelperException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

final class ReflectionHelper
{
    /** @var array<class-string, ReflectionClass<object>> */
    private static array $classCache = [];

    /** @var array<string, ReflectionProperty> */
    private static array $propertyCache = [];

    /**
     * @param object|class-string $classOrObject
     */
    public static function getShortName(object|string $classOrObject): string
    {
        return self::getReflection($classOrObject)->getShortName();
    }

    /**
     * @param object|class-string $classOrObject
     */
    public static function getNamespace(object|string $classOrObject): string
    {
        return self::getReflection($classOrObject)->getNamespaceName();
    }

    public static function getPropertyValue(object $object, string $property): mixed
    {
        $prop = self::getProperty($object, $property);

        return $prop->getValue($object);
    }

    public static function setPropertyValue(object $object, string $property, mixed $value): void
    {
        $prop = self::getProperty($object, $property);
        $prop->setValue($object, $value);
    }

    public static function hasProperty(object $object, string $property): bool
    {
        try {
            self::getProperty($object, $property);

            return true;
        } catch (HelperException) {
            return false;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function getAllProperties(object $object): array
    {
        $reflection = self::getReflection($object);
        $result = [];
        foreach ($reflection->getProperties() as $property) {
            $result[$property->getName()] = $property->getValue($object);
        }

        return $result;
    }

    /**
     * @param array<int, mixed> $args
     */
    public static function callPrivateMethod(object $object, string $method, array $args = []): mixed
    {
        $reflection = self::getReflection($object);

        try {
            $methodRef = $reflection->getMethod($method);
        } catch (ReflectionException $e) {
            throw new HelperException(sprintf('Method "%s" does not exist on %s.', $method, $reflection->getName()), 0, $e);
        }

        return $methodRef->invokeArgs($object, $args);
    }

    /**
     * @param object|class-string $classOrObject
     * @return list<class-string>
     */
    public static function getParentClasses(object|string $classOrObject): array
    {
        $parents = [];
        $reflection = self::getReflection($classOrObject)->getParentClass();
        while ($reflection instanceof ReflectionClass) {
            /** @var class-string $name */
            $name = $reflection->getName();
            $parents[] = $name;
            $reflection = $reflection->getParentClass();
        }

        return $parents;
    }

    /**
     * @param object|class-string $classOrObject
     * @return list<class-string>
     */
    public static function getInterfaces(object|string $classOrObject): array
    {
        /** @var list<class-string> $names */
        $names = self::getReflection($classOrObject)->getInterfaceNames();

        return $names;
    }

    /**
     * @param object|class-string $classOrObject
     * @return list<class-string>
     */
    public static function getTraits(object|string $classOrObject): array
    {
        /** @var list<class-string> $names */
        $names = self::getReflection($classOrObject)->getTraitNames();

        return $names;
    }

    /**
     * @param object|class-string $classOrObject
     * @return ReflectionClass<object>
     */
    private static function getReflection(object|string $classOrObject): ReflectionClass
    {
        $key = is_object($classOrObject) ? $classOrObject::class : $classOrObject;
        if (!isset(self::$classCache[$key])) {
            self::$classCache[$key] = new ReflectionClass($classOrObject);
        }

        return self::$classCache[$key];
    }

    private static function getProperty(object $object, string $property): ReflectionProperty
    {
        $key = $object::class . '::' . $property;
        if (isset(self::$propertyCache[$key])) {
            return self::$propertyCache[$key];
        }

        $reflection = self::getReflection($object);
        // Walk up the inheritance chain to find private/protected props
        $current = $reflection;
        while ($current !== false) {
            if ($current->hasProperty($property)) {
                $prop = $current->getProperty($property);

                return self::$propertyCache[$key] = $prop;
            }
            $current = $current->getParentClass();
        }

        throw new HelperException(sprintf('Property "%s" does not exist on %s.', $property, $reflection->getName()));
    }
}
