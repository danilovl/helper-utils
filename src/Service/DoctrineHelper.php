<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Service;

use Danilovl\HelperUtils\Exception\HelperException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;

final readonly class DoctrineHelper
{
    public function __construct(private ManagerRegistry $registry) {}

    /**
     * @param class-string $class
     */
    public function getEntityManager(string $class): EntityManagerInterface
    {
        $manager = $this->registry->getManagerForClass($class);
        if (!$manager instanceof EntityManagerInterface) {
            throw new HelperException(sprintf('No EntityManager found for class "%s".', $class));
        }

        return $manager;
    }

    /**
     * @param class-string $class
     * @return ClassMetadata<object>
     */
    public function getMetadata(string $class): ClassMetadata
    {
        /** @var ClassMetadata<object> $metadata */
        $metadata = $this->getEntityManager($class)->getClassMetadata($class);

        return $metadata;
    }

    /**
     * @param class-string $class
     */
    public function getTableName(string $class): string
    {
        return $this->getMetadata($class)->getTableName();
    }

    /**
     * @param class-string $class
     */
    public function getIdField(string $class): string
    {
        $fields = $this->getMetadata($class)->getIdentifierFieldNames();
        if ($fields === []) {
            throw new HelperException(sprintf('Class "%s" has no identifier field.', $class));
        }

        return $fields[0];
    }

    public function refresh(object $entity): void
    {
        $manager = $this->registry->getManagerForClass($entity::class);
        if (!$manager instanceof EntityManagerInterface) {
            throw new HelperException(sprintf('No EntityManager found for class "%s".', $entity::class));
        }
        $manager->refresh($entity);
    }

    public function detach(object $entity): void
    {
        $manager = $this->registry->getManagerForClass($entity::class);
        if (!$manager instanceof EntityManagerInterface) {
            throw new HelperException(sprintf('No EntityManager found for class "%s".', $entity::class));
        }
        $manager->detach($entity);
    }

    public function clearAll(): void
    {
        foreach ($this->registry->getManagers() as $manager) {
            $manager->clear();
        }
    }
}
