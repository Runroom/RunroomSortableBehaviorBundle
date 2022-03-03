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

namespace Runroom\SortableBehaviorBundle\Tests\Integration;

use Runroom\SortableBehaviorBundle\Tests\App\Admin\SortableEntityAdmin;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;
use Runroom\Testing\TestCase\SonataAdminTestCase;

/**
 * @extends SonataAdminTestCase<SortableEntity>
 */
class AbstractSortableAdminTest extends SonataAdminTestCase
{
    /**
     * @test
     */
    public function itDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesContainRoute('move');
    }

    /**
     * @test
     */
    public function itDoesDefineDefaultFilterParameters(): void
    {
        $this->assertAdminFilterParametersContainsFilter('_sort_by', 'position');
    }

    protected function getAdminClass(): string
    {
        return SortableEntityAdmin::class;
    }
}
