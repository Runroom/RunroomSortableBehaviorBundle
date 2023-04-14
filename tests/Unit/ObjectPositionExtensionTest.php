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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\SortableBehaviorBundle\Service\PositionHandlerInterface;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\ChildSortableEntity;
use Runroom\SortableBehaviorBundle\Twig\ObjectPositionExtension;

class ObjectPositionExtensionTest extends TestCase
{
    /**
     * @var MockObject&PositionHandlerInterface
     */
    private $positionHandler;

    private ObjectPositionExtension $extension;

    protected function setUp(): void
    {
        $this->positionHandler = $this->createMock(PositionHandlerInterface::class);

        $this->extension = new ObjectPositionExtension($this->positionHandler);
    }

    public function testItGetsCurrentPosition(): void
    {
        $entity = new ChildSortableEntity();

        $this->positionHandler->method('getCurrentPosition')->with($entity)->willReturn(3);

        $result = $this->extension->currentPosition($entity);

        static::assertSame(3, $result);
    }

    public function testItGetsLastPosition(): void
    {
        $entity = new ChildSortableEntity();

        $this->positionHandler->method('getLastPosition')->with($entity)->willReturn(10);

        $result = $this->extension->lastPosition($entity);

        static::assertSame(10, $result);
    }

    public function testItDefinesTwoFunctions(): void
    {
        $filters = $this->extension->getFunctions();

        static::assertCount(2, $filters);
    }
}
