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

use Runroom\SortableBehaviorBundle\Service\ORMPositionHandler;
use Runroom\SortableBehaviorBundle\Twig\ObjectPositionExtension;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.sortable_behavior.service.orm_position', ORMPositionHandler::class)
        ->arg('$registry', service('doctrine'))
        ->arg('$positionField', param('sortable.behavior.position.field'))
        ->arg('$sortableGroups', param('sortable.behavior.sortable_groups'))
        ->call('setPropertyAccessor', [service('property_accessor')]);

    $services->set('runroom.sortable_behavior.twig.object_position', ObjectPositionExtension::class)
        ->arg('$positionHandler', service('sortable_behavior.position'))
        ->tag('twig.extension');
};
