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
use Gedmo\Sortable\SortableListener;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Runroom\SortableBehaviorBundle\Services\GedmoPositionHandler;
use Runroom\SortableBehaviorBundle\Tests\Fixtures\ChildSortableEntity;
use Runroom\SortableBehaviorBundle\Tests\Fixtures\SortableEntity;

class GedmoPositionHandlerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<EntityManagerInterface> */
    private $entityManager;

    /** @var ObjectProphecy<SortableListener> */
    private $listener;

    /** @var GedmoPositionHandler */
    private $positionHandler;

    protected function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->listener = $this->prophesize(SortableListener::class);

        $this->positionHandler = new GedmoPositionHandler(
            $this->entityManager->reveal(),
            $this->listener->reveal()
        );
    }

    /** @test */
    public function itGetsLastPosition(): void
    {
        $entity = new ChildSortableEntity();
        $meta = $this->prophesize(ClassMetadata::class);
        $queryBuilder = $this->prophesize(QueryBuilder::class);
        $query = $this->prophesize(AbstractQuery::class);
        $reflectionPropertyDate = $this->prophesize(\ReflectionProperty::class);
        $reflectionPropertyObject = $this->prophesize(\ReflectionProperty::class);
        $reflectionPropertyEmpty = $this->prophesize(\ReflectionProperty::class);

        $meta->getName()->willReturn('SortableEntity');
        $meta->getReflectionProperty('date')->willReturn($reflectionPropertyDate->reveal());
        $meta->getReflectionProperty('object')->willReturn($reflectionPropertyObject->reveal());
        $meta->getReflectionProperty('empty')->willReturn($reflectionPropertyEmpty->reveal());
        $reflectionPropertyDate->getValue($entity)->willReturn(new \DateTime());
        $reflectionPropertyObject->getValue($entity)->willReturn(new \stdClass());
        $queryBuilder->select('MAX(n.position)')->willReturn($queryBuilder->reveal());
        $queryBuilder->from(SortableEntity::class, 'n')->willReturn($queryBuilder->reveal());
        $queryBuilder->andWhere(Argument::any())->willReturn($queryBuilder->reveal());
        $queryBuilder->setParameter(Argument::any(), Argument::any())->willReturn($queryBuilder->reveal());
        $queryBuilder->getQuery()->willReturn($query->reveal());
        $query->useResultCache(false)->shouldBeCalled();
        $query->getSingleScalarResult()->willReturn(2);
        $this->entityManager->getClassMetadata(ChildSortableEntity::class)->willReturn($meta->reveal());
        $this->entityManager->createQueryBuilder()->willReturn($queryBuilder->reveal());
        $this->listener->getConfiguration($this->entityManager->reveal(), 'SortableEntity')->willReturn([
            'useObjectClass' => SortableEntity::class,
            'position' => 'position',
            'groups' => ['date', 'object', 'empty'],
        ]);

        $lastPosition = $this->positionHandler->getLastPosition($entity);

        $this->assertSame(2, $lastPosition);
    }

    /** @test */
    public function itGetsPositionFieldByEntity(): void
    {
        $meta = $this->prophesize(ClassMetadata::class);

        $meta->getName()->willReturn('SortableEntity');
        $this->entityManager->getClassMetadata(SortableEntity::class)->willReturn($meta->reveal());
        $this->listener->getConfiguration($this->entityManager->reveal(), 'SortableEntity')->willReturn([
            'useObjectClass' => SortableEntity::class,
            'position' => 'position',
        ]);

        $positionField = $this->positionHandler->getPositionFieldByEntity(new SortableEntity());

        $this->assertSame('position', $positionField);
    }
}
