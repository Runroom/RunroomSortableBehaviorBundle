<?php

declare(strict_types=1);

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\SortableBehaviorBundle\Tests\Unit;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Sortable\SortableListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Runroom\SortableBehaviorBundle\Service\GedmoPositionHandler;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\ChildSortableEntity;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;

final class GedmoPositionHandlerTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private ManagerRegistry&Stub $registry;
    private SortableListener&MockObject $listener;
    private GedmoPositionHandler $positionHandler;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry = $this->createStub(ManagerRegistry::class);
        $this->listener = $this->createMock(SortableListener::class);

        $this->registry->method('getManagerForClass')->willReturn($this->entityManager);

        $this->positionHandler = new GedmoPositionHandler(
            $this->registry,
            $this->listener
        );
    }

    public function testItGetsLastPosition(): void
    {
        $entity = new ChildSortableEntity();
        $meta = $this->createStub(ClassMetadata::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        $reflectionPropertyDate = $this->createMock(\ReflectionProperty::class);
        $reflectionPropertyObject = $this->createMock(\ReflectionProperty::class);
        $reflectionPropertyEmpty = $this->createStub(\ReflectionProperty::class);

        $meta->method('getName')->willReturn('SortableEntity');
        $meta->method('getReflectionProperty')->willReturnMap([
            ['date', $reflectionPropertyDate],
            ['object', $reflectionPropertyObject],
            ['empty', $reflectionPropertyEmpty],
        ]);
        $reflectionPropertyDate->method('getValue')->with($entity)->willReturn(new \DateTime());
        $reflectionPropertyObject->method('getValue')->with($entity)->willReturn(new \stdClass());
        $queryBuilder->method('select')->with('MAX(n.position)')->willReturn($queryBuilder);
        $queryBuilder->method('from')->with(SortableEntity::class, 'n')->willReturn($queryBuilder);
        $queryBuilder->method('andWhere')->willReturn($queryBuilder);
        $queryBuilder->method('setParameter')->willReturn($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->expects(static::once())->method('disableResultCache');
        $query->method('getSingleScalarResult')->willReturn(2);
        $this->entityManager->method('getClassMetadata')->with(ChildSortableEntity::class)->willReturn($meta);
        $this->entityManager->method('createQueryBuilder')->willReturn($queryBuilder);
        $this->listener->method('getConfiguration')->with($this->entityManager, 'SortableEntity')->willReturn([
            'useObjectClass' => SortableEntity::class,
            'position' => 'position',
            'groups' => ['date', 'object', 'empty'],
        ]);

        $lastPosition = $this->positionHandler->getLastPosition($entity);

        static::assertSame(2, $lastPosition);
    }

    public function testItGetsPositionFieldByEntity(): void
    {
        $meta = $this->createStub(ClassMetadata::class);

        $meta->method('getName')->willReturn('SortableEntity');
        $this->entityManager->method('getClassMetadata')->with(SortableEntity::class)->willReturn($meta);
        $this->listener->method('getConfiguration')->with($this->entityManager, 'SortableEntity')->willReturn([
            'useObjectClass' => SortableEntity::class,
            'position' => 'position',
        ]);

        $positionField = $this->positionHandler->getPositionFieldByEntity(new SortableEntity());

        static::assertSame('position', $positionField);
    }
}
