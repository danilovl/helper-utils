<?php declare(strict_types=1);

namespace Danilovl\HelperUtils\Tests\Unit\Service;

use Danilovl\HelperUtils\Exception\HelperException;
use Danilovl\HelperUtils\Service\DoctrineHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

#[AllowMockObjectsWithoutExpectations]
final class DoctrineHelperTest extends TestCase
{
    /** @var MockObject&ManagerRegistry */
    private ManagerRegistry $registry;

    /** @var MockObject&EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var MockObject&ClassMetadata<object> */
    private ClassMetadata $classMetadata;

    private DoctrineHelper $helper;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->classMetadata = $this->createMock(ClassMetadata::class);

        $this->helper = new DoctrineHelper($this->registry);
    }

    public function testGetEntityManager(): void
    {
        $class = stdClass::class;
        $this->registry->expects(self::once())->method('getManagerForClass')->with($class)->willReturn($this->entityManager);
        self::assertSame($this->entityManager, $this->helper->getEntityManager($class));
    }

    public function testGetEntityManagerThrows(): void
    {
        $class = stdClass::class;
        $this->registry->expects(self::once())->method('getManagerForClass')->with($class)->willReturn(null);
        $this->expectException(HelperException::class);
        $this->helper->getEntityManager($class);
    }

    public function testGetMetadata(): void
    {
        $class = stdClass::class;
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry->expects(self::once())->method('getManagerForClass')->with($class)->willReturn($entityManager);
        $entityManager->expects(self::once())->method('getClassMetadata')->with($class)->willReturn($this->classMetadata);

        self::assertSame($this->classMetadata, $this->helper->getMetadata($class));
    }

    public function testGetTableName(): void
    {
        $class = stdClass::class;
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry->expects(self::once())->method('getManagerForClass')->with($class)->willReturn($entityManager);
        $entityManager->expects(self::once())->method('getClassMetadata')->with($class)->willReturn($this->classMetadata);
        $this->classMetadata->method('getTableName')->willReturn('some_table');

        self::assertSame('some_table', $this->helper->getTableName($class));
    }

    public function testGetIdField(): void
    {
        $class = stdClass::class;
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry->expects(self::once())->method('getManagerForClass')->with($class)->willReturn($entityManager);
        $entityManager->expects(self::once())->method('getClassMetadata')->with($class)->willReturn($this->classMetadata);
        $this->classMetadata->method('getIdentifierFieldNames')->willReturn(['id']);

        self::assertSame('id', $this->helper->getIdField($class));
    }

    public function testGetIdFieldThrowsWhenNoFields(): void
    {
        $class = stdClass::class;
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry->expects(self::once())->method('getManagerForClass')->with($class)->willReturn($entityManager);
        $entityManager->expects(self::once())->method('getClassMetadata')->with($class)->willReturn($this->classMetadata);
        $this->classMetadata->method('getIdentifierFieldNames')->willReturn([]);

        $this->expectException(HelperException::class);
        $this->helper->getIdField($class);
    }

    public function testRefresh(): void
    {
        $entity = new stdClass;
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry->expects(self::once())->method('getManagerForClass')->with($entity::class)->willReturn($entityManager);
        $entityManager->expects(self::once())->method('refresh')->with($entity);

        $this->helper->refresh($entity);
    }

    public function testDetach(): void
    {
        $entity = new stdClass;
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry->expects(self::once())->method('getManagerForClass')->with($entity::class)->willReturn($entityManager);
        $entityManager->expects(self::once())->method('detach')->with($entity);

        $this->helper->detach($entity);
    }

    public function testClearAll(): void
    {
        $manager1 = $this->createMock(EntityManagerInterface::class);
        $manager2 = $this->createMock(EntityManagerInterface::class);

        $this->registry->expects(self::once())->method('getManagers')->willReturn([$manager1, $manager2]);

        $manager1->expects(self::once())->method('clear');
        $manager2->expects(self::once())->method('clear');

        $this->helper->clearAll();
    }
}
