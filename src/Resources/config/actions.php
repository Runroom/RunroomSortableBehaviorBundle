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

use Psr\Container\ContainerInterface;
use Runroom\SortableBehaviorBundle\Action\MoveAction;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom.sortable_behavior.action.move', MoveAction::class)
        ->public()
        ->tag('container.service_subscriber')
        ->tag('controller.service_arguments')
        ->arg('$accessor', new ReferenceConfigurator('property_accessor'))
        ->arg('$translator', new ReferenceConfigurator('translator'))
        ->arg('$adminFetcher', new ReferenceConfigurator('sonata.admin.request.fetcher'))
        ->arg('$positionHandler', new ReferenceConfigurator('sortable_behavior.position'))
        ->call('setContainer', [new ReferenceConfigurator(ContainerInterface::class)]);
};
