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

namespace Runroom\SortableBehaviorBundle\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Runroom\SortableBehaviorBundle\RunroomSortableBehaviorBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RouteConfigurator;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new DoctrineBundle(),
            new FrameworkBundle(),
            new KnpMenuBundle(),
            new SecurityBundle(),
            new SonataAdminBundle(),
            new SonataDoctrineORMAdminBundle(),
            new TwigBundle(),

            new RunroomSortableBehaviorBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return $this->getBaseDir() . '/cache';
    }

    public function getLogDir(): string
    {
        return $this->getBaseDir() . '/log';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir() . '/services.yaml');

        $container->loadFromExtension('framework', [
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
            'form' => ['enabled' => true],
            'property_access' => ['enabled' => true],
        ]);

        $container->loadFromExtension('security', [
            'firewalls' => ['main' => ['anonymous' => true]],
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite:///%kernel.cache_dir%/app.db', 'logging' => false],
            'orm' => [
                'auto_mapping' => true,
                'mappings' => [
                    'sortable_behavior' => [
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/Entity',
                        'prefix' => 'Runroom\SortableBehaviorBundle\Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                ],
            ],
        ]);

        $container->loadFromExtension('twig', [
            'exception_controller' => null,
            'strict_variables' => '%kernel.debug%',
        ]);
    }

    /** @param RouteCollectionBuilder|RouteConfigurator $routes */
    protected function configureRoutes($routes): void
    {
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-sortable-behavior-bundle/var';
    }
}
