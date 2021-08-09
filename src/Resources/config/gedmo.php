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
use Runroom\SortableBehaviorBundle\Service\GedmoPositionHandler;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    $services = $containerConfigurator->services();

    $services->set(GedmoPositionHandler::class)
        ->arg('$entityManager', new ReferenceConfigurator('doctrine.orm.entity_manager'))
        ->arg('$listener', new ReferenceConfigurator('runroom.sortable_behavior.sortable_listener'))
        ->call('setPropertyAccessor', [new ReferenceConfigurator('property_accessor')]);

    $services->set('runroom.sortable_behavior.sortable_listener', SortableListener::class)
        ->call('setAnnotationReader', [new ReferenceConfigurator('annotation_reader')]);
};
