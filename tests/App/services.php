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
use Runroom\SortableBehaviorBundle\Tests\App\Admin\SortableEntityAdmin;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $sortableEntityAdmin = $services->set(SortableEntityAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => SortableEntity::class,
            'manager_type' => 'orm',
            'label' => 'Sortable Entity',
        ]);

    /**
     * @todo: Simplify this when dropping support for SonataAdminBundle 3
     */
    if (!is_a(CRUDController::class, AbstractController::class, true)) {
        $sortableEntityAdmin->args([null, SortableEntity::class, null]);
    }

    $services->set(SortableListener::class)
        ->tag('doctrine.event_subscriber')
        ->call('setAnnotationReader', [new ReferenceConfigurator('annotation_reader')]);
};
