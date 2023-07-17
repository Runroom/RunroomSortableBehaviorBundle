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

namespace Runroom\SortableBehaviorBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollectionInterface;

trait SortableAdminTrait
{
    abstract public function getRouterIdParameter(): string;

    /**
     * @param mixed[] $sortValues
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_by'] = 'position';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}', [
            '_controller' => 'runroom.sortable_behavior.action.move',
        ]);
    }
}
