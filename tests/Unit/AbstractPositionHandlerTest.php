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

use PHPUnit\Framework\TestCase;
use Runroom\SortableBehaviorBundle\Service\AbstractPositionHandler;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AbstractPositionHandlerTest extends TestCase
{
    private SortableEntity $entity;
    private TestPositionHandler $positionHandler;

    protected function setUp(): void
    {
        $this->entity = new SortableEntity();
        $this->entity->setPosition(3);

        $this->positionHandler = new TestPositionHandler();
        $this->positionHandler->setPropertyAccessor(PropertyAccess::createPropertyAccessor());
    }

    /**
     * @test
     */
    public function itGetsCurrentPosition(): void
    {
        $position = $this->positionHandler->getCurrentPosition($this->entity);

        static::assertSame(3, $position);
    }

    /**
     * @test
     */
    public function itUpdatesThePosition(): void
    {
        $lastPosition = $this->positionHandler->getLastPosition($this->entity);

        $this->entity->setPosition($this->positionHandler->getPosition($this->entity, 'bottom', $lastPosition));
        $this->entity->setPosition($this->positionHandler->getPosition($this->entity, 'top', $lastPosition));
        $this->entity->setPosition($this->positionHandler->getPosition($this->entity, 'down', $lastPosition));
        $this->entity->setPosition($this->positionHandler->getPosition($this->entity, 'up', $lastPosition));
        $this->entity->setPosition($this->positionHandler->getPosition($this->entity, 'random', $lastPosition));

        static::assertSame(0, $this->entity->getPosition());
    }
}

class TestPositionHandler extends AbstractPositionHandler
{
    public function getLastPosition(object $entity): int
    {
        return 10;
    }

    public function getPositionFieldByEntity($entity): string
    {
        return 'position';
    }
}
