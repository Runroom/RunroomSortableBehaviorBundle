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

namespace Runroom\SortableBehaviorBundle\Tests\Functional;

use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

use function Zenstruck\Foundry\Persistence\persistent_factory;
use function Zenstruck\Foundry\Persistence\refresh;

final class AbstractSortableAdminTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testItMovesItemUpAndDown(): void
    {
        $client = static::createClient();

        $factory = persistent_factory(SortableEntity::class);
        $sortableEntities = $factory->many(4)->create();

        $sortableEntity1 = $sortableEntities[0];
        $sortableEntity2 = $sortableEntities[1];
        $sortableEntity3 = $sortableEntities[2];
        $sortableEntity4 = $sortableEntities[3];

        static::assertSame(0, $sortableEntity1->getPosition());
        static::assertSame(1, $sortableEntity2->getPosition());
        static::assertSame(2, $sortableEntity3->getPosition());
        static::assertSame(3, $sortableEntity4->getPosition());

        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity1->getId() . '/move/down');
        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity2->getId() . '/move/bottom');
        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity3->getId() . '/move/up');
        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity4->getId() . '/move/top');

        refresh($sortableEntity1);
        refresh($sortableEntity2);
        refresh($sortableEntity3);
        refresh($sortableEntity4);

        static::assertSame(2, $sortableEntity1->getPosition());
        static::assertSame(3, $sortableEntity2->getPosition());
        static::assertSame(1, $sortableEntity3->getPosition());
        static::assertSame(0, $sortableEntity4->getPosition());
    }

    public function testItMovesItemsToSpecificPositions(): void
    {
        $client = static::createClient();

        $factory = persistent_factory(SortableEntity::class);
        $sortableEntities = $factory->many(4)->create();

        $sortableEntity1 = $sortableEntities[0];
        $sortableEntity2 = $sortableEntities[1];
        $sortableEntity3 = $sortableEntities[2];
        $sortableEntity4 = $sortableEntities[3];

        static::assertSame(0, $sortableEntity1->getPosition());
        static::assertSame(1, $sortableEntity2->getPosition());
        static::assertSame(2, $sortableEntity3->getPosition());
        static::assertSame(3, $sortableEntity4->getPosition());

        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity1->getId() . '/move/3');

        refresh($sortableEntity1);
        refresh($sortableEntity2);
        refresh($sortableEntity3);
        refresh($sortableEntity4);

        static::assertSame(3, $sortableEntity1->getPosition());
        static::assertSame(0, $sortableEntity2->getPosition());
        static::assertSame(1, $sortableEntity3->getPosition());
        static::assertSame(2, $sortableEntity4->getPosition());
    }
}
