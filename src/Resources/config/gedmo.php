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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Gedmo\Mapping\Driver\AttributeReader;
use Gedmo\Sortable\SortableListener;
use Runroom\SortableBehaviorBundle\Service\GedmoPositionHandler;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.sortable_behavior.service.gedmo_position', GedmoPositionHandler::class)
        ->arg('$registry', service('doctrine'))
        ->arg('$listener', service('runroom.sortable_behavior.sortable_listener'))
        ->call('setPropertyAccessor', [service('property_accessor')]);

    $services->set('runroom.sortable_behavior.attribute_reader', AttributeReader::class);

    $services->set('runroom.sortable_behavior.sortable_listener', SortableListener::class)
        ->call('setAnnotationReader', [service('runroom.sortable_behavior.attribute_reader')]);
};
