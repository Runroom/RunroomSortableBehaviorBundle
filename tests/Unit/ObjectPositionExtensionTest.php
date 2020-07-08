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
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Runroom\SortableBehaviorBundle\Service\PositionHandlerInterface;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\ChildSortableEntity;
use Runroom\SortableBehaviorBundle\Twig\ObjectPositionExtension;

class ObjectPositionExtensionTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<PositionHandlerInterface> */
    private $positionHandler;

    /** @var ObjectPositionExtension */
    private $extension;

    protected function setUp(): void
    {
        $this->positionHandler = $this->prophesize(PositionHandlerInterface::class);

        $this->extension = new ObjectPositionExtension($this->positionHandler->reveal());
    }

    /** @test */
    public function itGetsCurrentPosition(): void
    {
        $entity = new ChildSortableEntity();

        $this->positionHandler->getCurrentPosition($entity)->willReturn(3);

        $result = $this->extension->currentPosition($entity);

        self::assertSame(3, $result);
    }

    /** @test */
    public function itGetsLastPosition(): void
    {
        $entity = new ChildSortableEntity();

        $this->positionHandler->getLastPosition($entity)->willReturn(10);

        $result = $this->extension->lastPosition($entity);

        self::assertSame(10, $result);
    }

    /** @test */
    public function itDefinesTwoFunctions(): void
    {
        $filters = $this->extension->getFunctions();

        self::assertCount(2, $filters);
    }
}
