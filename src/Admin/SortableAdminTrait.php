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

use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

trait SortableAdminTrait
{
    /**
     * @todo: Add return typehint when dropping support for Sonata 3
     *
     * @return string
     */
    abstract public function getRouterIdParameter();

    /**
     * @param mixed[] $sortValues
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_by'] = 'position';
    }

    /**
     * @todo: Simplify this when dropping support for Sonata 3
     *
     * @param RouteCollection|RouteCollectionInterface $collection
     */
    protected function configureRoutes(object $collection): void
    {
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}', [
            '_controller' => 'runroom.sortable_behavior.action.move',
        ]);
    }
}
