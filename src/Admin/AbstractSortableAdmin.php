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

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

abstract class AbstractSortableAdmin extends AbstractAdmin
{
    /** @param mixed[] $sortValues */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_by'] = 'position';
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
    }
}
