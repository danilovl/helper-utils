<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Helper\Array;

use Closure;
use Doctrine\Common\Collections\Collection;

final class CollectionHelper
{
    /**
     * Synchronizes a Doctrine collection with desired items.
     * Removes items from $current that aren't in $desired (according to $matcher),
     * adds items from $desired that aren't already in $current.
     *
     * @template TKey of array-key
     * @template T of object
     * @param Collection<TKey, T> $current
     * @param iterable<T> $desired
     * @param callable(T, T): bool $matcher
     */
    public static function syncOneToMany(Collection $current, iterable $desired, callable $matcher): void
    {
        $desiredArray = is_array($desired) ? $desired : iterator_to_array($desired, false);

        foreach ($current as $item) {
            $found = false;
            foreach ($desiredArray as $desiredItem) {
                if ($matcher($item, $desiredItem)) {
                    $found = true;

                    break;
                }
            }
            if (!$found) {
                $current->removeElement($item);
            }
        }

        foreach ($desiredArray as $desiredItem) {
            $found = false;
            foreach ($current as $item) {
                if ($matcher($item, $desiredItem)) {
                    $found = true;

                    break;
                }
            }
            if (!$found) {
                $current->add($desiredItem);
            }
        }
    }

    /**
     * @template TKey of array-key
     * @template T
     * @param Collection<TKey, T> $a
     * @param Collection<TKey, T> $b
     * @return list<T>
     */
    public static function diff(Collection $a, Collection $b): array
    {
        $result = [];
        foreach ($a as $item) {
            if (!$b->contains($item)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @template TKey of array-key
     * @template T
     * @param Collection<TKey, T> $c
     * @param Closure(T): bool $predicate
     * @return array{0: list<T>, 1: list<T>}
     */
    public static function partition(Collection $c, Closure $predicate): array
    {
        $matches = [];
        $rest = [];
        foreach ($c as $item) {
            if ($predicate($item)) {
                $matches[] = $item;
            } else {
                $rest[] = $item;
            }
        }

        return [$matches, $rest];
    }
}
