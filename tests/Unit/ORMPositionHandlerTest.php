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
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Runroom\SortableBehaviorBundle\Service\ORMPositionHandler;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\ChildSortableEntity;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableGroup;

class ORMPositionHandlerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<EntityManagerInterface> */
    private $entityManager;

    /** @var ORMPositionHandler */
    private $positionHandler;

    protected function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);

        $this->positionHandler = new ORMPositionHandler(
            $this->entityManager->reveal(),
            ['entities' => [SortableEntity::class => 'position'], 'default' => 'place'],
            ['entities' => [SortableEntity::class => ['group', 'sortableGroup']]]
        );
    }

    /** @test */
    public function itGetsLastPosition(): void
    {
        $entity = new ChildSortableEntity();
        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $query = $this->prophesize(AbstractQuery::class);

        $entity->setGroup(2);
        $entity->setSortableGroup(new SortableGroup());

        $queryBuilder->select('MAX(t.position) as last_position')->willReturn($queryBuilder->reveal());
        $queryBuilder->from(SortableEntity::class, 't')->willReturn($queryBuilder->reveal());
        $queryBuilder->andWhere(Argument::any())->willReturn($queryBuilder->reveal());
        $queryBuilder->setParameter(Argument::any(), Argument::any())->willReturn($queryBuilder->reveal());
        $queryBuilder->getQuery()->willReturn($query->reveal());
        $query->useResultCache(false)->shouldBeCalled();
        $query->getSingleScalarResult()->willReturn(2);
        $this->entityManager->createQueryBuilder()->willReturn($queryBuilder->reveal());

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
