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

use Gedmo\Sortable\SortableListener;
use Runroom\SortableBehaviorBundle\Controller\SortableAdminController;
use Runroom\SortableBehaviorBundle\Service\GedmoPositionHandler;
use Runroom\SortableBehaviorBundle\Service\ORMPositionHandler;
use Runroom\SortableBehaviorBundle\Twig\ObjectPositionExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults();

    $services->set(SortableAdminController::class)
        ->public()
        ->arg('$accessor', ref('property_accessor'))
        ->arg('$positionHandler', ref('sortable_behavior.position'));

    $services->set(ORMPositionHandler::class)
        ->arg('$entityManager', ref('doctrine.orm.entity_manager'))
        ->arg('$positionField', '%sortable.behavior.position.field%')
        ->arg('$sortableGroups', '%sortable.behavior.sortable_groups%')
        ->call('setPropertyAccessor', [ref('property_accessor')]);

    $services->set(GedmoPositionHandler::class)
        ->arg('$entityManager', ref('doctrine.orm.entity_manager'))
        ->arg('$listener', ref('runroom.sortable_behavior.sortable_listener'))
        ->call('setPropertyAccessor', [ref('property_accessor')]);

    $services->set(ObjectPositionExtension::class)
        ->arg('$positionHandler', ref('sortable_behavior.position'))
        ->tag('twig.extension');

    $services->set('runroom.sortable_behavior.sortable_listener', SortableListener::class)
        ->call('setAnnotationReader', [ref('annotation_reader')]);
};
