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

use Psr\Container\ContainerInterface;
use Runroom\SortableBehaviorBundle\Action\MoveAction;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.sortable_behavior.action.move', MoveAction::class)
        ->public()
        ->tag('container.service_subscriber')
        ->tag('controller.service_arguments')
        ->arg('$accessor', service('property_accessor'))
        ->arg('$translator', service('translator'))
        ->arg('$adminFetcher', service('sonata.admin.request.fetcher'))
        ->arg('$positionHandler', service('sortable_behavior.position'))
        ->call('setContainer', [service(ContainerInterface::class)]);
};
