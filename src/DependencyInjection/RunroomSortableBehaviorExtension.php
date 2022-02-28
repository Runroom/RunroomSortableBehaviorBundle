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

namespace Runroom\SortableBehaviorBundle\DependencyInjection;

use Gedmo\Sortable\SortableListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class RunroomSortableBehaviorExtension extends Extension
{
    /**
     * @psalm-suppress UndefinedInterfaceMethod $bundles is an array
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $bundles = $container->getParameter('kernel.bundles');

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.php');
        }

        if (class_exists(SortableListener::class)) {
            $loader->load('gedmo.php');
        }

        $container->setParameter('sortable.behavior.position.field', $config['position_field']);
        $container->setParameter('sortable.behavior.sortable_groups', $config['sortable_groups']);

        $container->setAlias('sortable_behavior.position', new Alias($config['position_handler']));
        $container->getAlias('sortable_behavior.position')->setPublic(true);
    }
}
