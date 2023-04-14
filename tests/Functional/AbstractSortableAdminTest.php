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
use Zenstruck\Foundry\AnonymousFactory;
use Zenstruck\Foundry\LazyValue;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

use function Zenstruck\Foundry\anonymous;

class AbstractSortableAdminTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testItUpdatesPosition(): void
    {
        $client = static::createClient();

        /**
         * @psalm-suppress DeprecatedClass
         *
         * @todo: simplify when dropping support for ZenstruckFoundryBundle < 1.10
         */
        if (class_exists(LazyValue::class)) {
            $factory = anonymous(SortableEntity::class);
        } else {
            /**
             * @psalm-suppress InvalidArgument
             */
            $factory = AnonymousFactory::new(SortableEntity::class);
        }

        $sortableEntities = $factory->many(4)->create();

        /** @var Proxy<SortableEntity> */
        $sortableEntity1 = $sortableEntities[0];
        /** @var Proxy<SortableEntity> */
        $sortableEntity2 = $sortableEntities[1];
        /** @var Proxy<SortableEntity> */
        $sortableEntity3 = $sortableEntities[2];
        /** @var Proxy<SortableEntity> */
        $sortableEntity4 = $sortableEntities[3];

        static::assertSame(0, $sortableEntity1->getPosition());
        static::assertSame(1, $sortableEntity2->getPosition());
        static::assertSame(2, $sortableEntity3->getPosition());
        static::assertSame(3, $sortableEntity4->getPosition());

        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity1->getId() . '/move/down');
        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity2->getId() . '/move/bottom');
        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity3->getId() . '/move/up');
        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity4->getId() . '/move/top');

        $sortableEntity1->refresh();
        $sortableEntity2->refresh();
        $sortableEntity3->refresh();
        $sortableEntity4->refresh();

        static::assertSame(2, $sortableEntity1->getPosition());
        static::assertSame(3, $sortableEntity2->getPosition());
        static::assertSame(1, $sortableEntity3->getPosition());
        static::assertSame(0, $sortableEntity4->getPosition());

        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity3->getId() . '/move/3');

        $sortableEntity1->refresh();
        $sortableEntity2->refresh();
        $sortableEntity3->refresh();
        $sortableEntity4->refresh();

        static::assertSame(1, $sortableEntity1->getPosition());
        static::assertSame(2, $sortableEntity2->getPosition());
        static::assertSame(3, $sortableEntity3->getPosition());
        static::assertSame(0, $sortableEntity4->getPosition());
    }
}
