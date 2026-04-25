<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Reflection;

use Danilovl\HelperUtils\Exception\HelperException;
use Doctrine\ORM\Mapping\Table;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class AttributeHelper
{
    /**
     * Gets the Doctrine table name from #[ORM\Table] attribute, or default (lowercased short name).
     * Returns the default if doctrine/orm is not installed.
     *
     * @param object|class-string $entity
     */
    public static function getEntityTableName(object|string $entity): string
    {
        $reflection = self::getReflection($entity);

        if (class_exists(Table::class)) {
            $attributes = $reflection->getAttributes(Table::class);
            if ($attributes !== []) {
                $instance = $attributes[0]->newInstance();
                if (!empty($instance->name)) {
                    return $instance->name;
                }
            }
        }

        return mb_strtolower($reflection->getShortName());
    }

    /**
     * @template T of object
     * @param object|class-string $classOrObject
     * @param class-string<T> $attributeClass
     * @return T|null
     */
    public static function getClassAttribute(object|string $classOrObject, string $attributeClass): ?object
    {
        $attributes = self::getReflection($classOrObject)
            ->getAttributes($attributeClass, ReflectionAttribute::IS_INSTANCEOF);

        if ($attributes === []) {
            return null;
        }

        /** @var T $instance */
        $instance = $attributes[0]->newInstance();

        return $instance;
    }

    /**
     * @param object|class-string $classOrObject
     * @param class-string|null $attributeClass
     * @return list<object>
     */
    public static function getClassAttributes(object|string $classOrObject, ?string $attributeClass = null): array
    {
        $reflection = self::getReflection($classOrObject);
        $attributes = $attributeClass === null
            ? $reflection->getAttributes()
            : $reflection->getAttributes($attributeClass, ReflectionAttribute::IS_INSTANCEOF);

        return array_map(static fn (ReflectionAttribute $a): object => $a->newInstance(), $attributes);
    }

    /**
     * @template T of object
     * @param object|class-string $classOrObject
     * @param class-string<T> $attributeClass
     * @return T|null
     */
    public static function getMethodAttribute(object|string $classOrObject, string $method, string $attributeClass): ?object
    {
        try {
            $attributes = self::getReflection($classOrObject)
                ->getMethod($method)
                ->getAttributes($attributeClass, ReflectionAttribute::IS_INSTANCEOF);
        } catch (ReflectionException) {
            return null;
        }

        if ($attributes === []) {
            return null;
        }

        /** @var T $instance */
        $instance = $attributes[0]->newInstance();

        return $instance;
    }

    /**
     * @template T of object
     * @param object|class-string $classOrObject
     * @param class-string<T> $attributeClass
     * @return T|null
     */
    public static function getPropertyAttribute(object|string $classOrObject, string $property, string $attributeClass): ?object
    {
        try {
            $attributes = self::getReflection($classOrObject)->getProperty($property)
                ->getAttributes($attributeClass, ReflectionAttribute::IS_INSTANCEOF);
        } catch (ReflectionException) {
            return null;
        }

        if ($attributes === []) {
            return null;
        }

        /** @var T $instance */
        $instance = $attributes[0]->newInstance();

        return $instance;
    }

    /**
     * @param object|class-string $classOrObject
     * @param class-string $attributeClass
     */
    public static function hasAttribute(object|string $classOrObject, string $attributeClass): bool
    {
        return self::getReflection($classOrObject)
                ->getAttributes($attributeClass, ReflectionAttribute::IS_INSTANCEOF) !== [];
    }

    /**
     * Scans PHP files in a directory and returns classes with the given attribute.
     * This is a best-effort utility — it requires class names to match file names (PSR-4-style).
     *
     * @param class-string $attributeClass
     * @return list<class-string>
     */
    public static function findClassesWithAttribute(string $attributeClass, string $directory): array
    {
        if (!is_dir($directory)) {
            throw new HelperException(sprintf('Directory "%s" does not exist.', $directory));
        }

        $found = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file instanceof SplFileInfo || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            if ($contents === false) {
                continue;
            }

            $namespace = '';
            if (preg_match('~^namespace\s+([^;]+);~m', $contents, $m)) {
                $namespace = mb_trim($m[1]);
            }

            if (preg_match_all('~^(?:final\s+|abstract\s+)?class\s+(\w+)~m', $contents, $matches)) {
                foreach ($matches[1] as $className) {
                    $fqcn = mb_ltrim($namespace . '\\' . $className, '\\');
                    if (!class_exists($fqcn)) {
                        continue;
                    }
                    /** @var class-string $fqcn */
                    if (self::hasAttribute($fqcn, $attributeClass)) {
                        $found[] = $fqcn;
                    }
                }
            }
        }

        return $found;
    }

    /**
     * @param object|class-string $classOrObject
     * @return ReflectionClass<object>
     */
    private static function getReflection(object|string $classOrObject): ReflectionClass
    {
        return new ReflectionClass($classOrObject);
    }
}
