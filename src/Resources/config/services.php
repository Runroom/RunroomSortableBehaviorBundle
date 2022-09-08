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

use Runroom\SortableBehaviorBundle\Service\ORMPositionHandler;
use Runroom\SortableBehaviorBundle\Twig\ObjectPositionExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom.sortable_behavior.service.orm_position', ORMPositionHandler::class)
        ->arg('$entityManager', new ReferenceConfigurator('doctrine.orm.entity_manager'))
        ->arg('$positionField', '%sortable.behavior.position.field%')
        ->arg('$sortableGroups', '%sortable.behavior.sortable_groups%')
        ->call('setPropertyAccessor', [new ReferenceConfigurator('property_accessor')]);

    $services->set('runroom.sortable_behavior.twig.object_position', ObjectPositionExtension::class)
        ->arg('$positionHandler', new ReferenceConfigurator('sortable_behavior.position'))
        ->tag('twig.extension');
};
