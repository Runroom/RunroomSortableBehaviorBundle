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

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\MappingException;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Runroom\SortableBehaviorBundle\Service\ORMPositionHandler;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\ChildSortableEntity;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableGroup;

final class ORMPositionHandlerTest extends TestCase
{
    private EntityManagerInterface&Stub $entityManager;
    private ManagerRegistry&Stub $registry;
    private ORMPositionHandler $positionHandler;

    protected function setUp(): void
    {
        $this->entityManager = $this->createStub(EntityManagerInterface::class);
        $this->registry = $this->createStub(ManagerRegistry::class);

        $this->registry->method('getManagerForClass')->willReturn($this->entityManager);

        $this->positionHandler = new ORMPositionHandler(
            $this->registry,
            ['entities' => [SortableEntity::class => 'position'], 'default' => 'place'],
            ['entities' => [SortableEntity::class => ['group', 'sortableGroup']]]
        );
    }

    public function testItGetsLastPosition(): void
    {
        $entity = new ChildSortableEntity();
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);
        $classMetadata = new ClassMetadata($entity::class);

        $entity->setSimpleGroup(2);
        $entity->setSortableGroup(new SortableGroup());

        $queryBuilder->method('select')->with('MAX(t.position) as last_position')->willReturn($queryBuilder);
        $queryBuilder->method('from')->with(SortableEntity::class, 't')->willReturn($queryBuilder);
        $queryBuilder->method('andWhere')->willReturn($queryBuilder);
        $queryBuilder->method('setParameter')->willReturn($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->expects(static::once())->method('disableResultCache');
        $query->method('getSingleScalarResult')->willReturn(2);
        $this->entityManager->method('createQueryBuilder')->willReturn($queryBuilder);
        $this->entityManager->method('getClassMetadata')->willReturn($classMetadata);

        $lastPosition = $this->positionHandler->getLastPosition($entity);

        static::assertSame(2, $lastPosition);
    }

    public function testItGetsPositionFieldByEntity(): void
    {
        $this->entityManager->method('getClassMetadata')->willThrowException(new MappingException());

        $field = $this->positionHandler->getPositionFieldByEntity(new \stdClass());

        static::assertSame('place', $field);
    }
}
