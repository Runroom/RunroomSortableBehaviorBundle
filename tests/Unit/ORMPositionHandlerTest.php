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
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Runroom\SortableBehaviorBundle\Service\ORMPositionHandler;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\ChildSortableEntity;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableGroup;

class ORMPositionHandlerTest extends TestCase
{
    /** @var Stub&EntityManagerInterface */
    private $entityManager;

    /** @var ORMPositionHandler */
    private $positionHandler;

    protected function setUp(): void
    {
        $this->entityManager = $this->createStub(EntityManagerInterface::class);

        $this->positionHandler = new ORMPositionHandler(
            $this->entityManager,
            ['entities' => [SortableEntity::class => 'position'], 'default' => 'place'],
            ['entities' => [SortableEntity::class => ['group', 'sortableGroup']]]
        );
    }

    /** @test */
    public function itGetsLastPosition(): void
    {
        $entity = new ChildSortableEntity();
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);

        $entity->setGroup(2);
        $entity->setSortableGroup(new SortableGroup());

        $queryBuilder->method('select')->with('MAX(t.position) as last_position')->willReturn($queryBuilder);
        $queryBuilder->method('from')->with(SortableEntity::class, 't')->willReturn($queryBuilder);
        $queryBuilder->method('andWhere')->willReturn($queryBuilder);
        $queryBuilder->method('setParameter')->willReturn($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);
        $query->expects(self::once())->method('useResultCache')->with(false);
        $query->method('getSingleScalarResult')->willReturn(2);
        $this->entityManager->method('createQueryBuilder')->willReturn($queryBuilder);

        $lastPosition = $this->positionHandler->getLastPosition($entity);

        self::assertSame(2, $lastPosition);
    }

    /** @test */
    public function itGetsPositionFieldByEntity(): void
    {
        $field = $this->positionHandler->getPositionFieldByEntity(new \stdClass());

        self::assertSame('place', $field);
    }
}
