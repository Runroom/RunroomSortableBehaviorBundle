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
use Runroom\SortableBehaviorBundle\Controller\SortableAdminController;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    $services = $containerConfigurator->services();

    $sortableAdminController = $services->set(SortableAdminController::class)
        ->public()
        ->arg('$accessor', new ReferenceConfigurator('property_accessor'))
        ->arg('$positionHandler', new ReferenceConfigurator('sortable_behavior.position'));

    /* @todo: Simplify this when dropping support for SonataAdminBundle 3 */
    if (is_a(CRUDController::class, AbstractController::class, true)) {
        $sortableAdminController
            ->call('setContainer', [new ReferenceConfigurator(ContainerInterface::class)])
            ->tag('container.service_subscriber')
            ->tag('controller.service_arguments');
    }
};
