<?php
declare(strict_types=1);

namespace Runroom\SortableBehaviorBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

trait SortableAdminTrait
{
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
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
    }

    abstract public function getRouterIdParameter(): string;
}
