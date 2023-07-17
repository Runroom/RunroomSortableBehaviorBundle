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
use Runroom\SortableBehaviorBundle\Tests\App\Admin\SortableEntityAdmin;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SortableEntityAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => SortableEntity::class,
            'manager_type' => 'orm',
            'label' => 'Sortable Entity',
        ]);

    $services->set('attribute_reader', AttributeReader::class);

    $services->set(SortableListener::class)
        ->tag('doctrine.event_listener', ['event' => 'onFlush'])
        ->tag('doctrine.event_listener', ['event' => 'loadClassMetadata'])
        ->tag('doctrine.event_listener', ['event' => 'prePersist'])
        ->tag('doctrine.event_listener', ['event' => 'postPersist'])
        ->tag('doctrine.event_listener', ['event' => 'preUpdate'])
        ->tag('doctrine.event_listener', ['event' => 'postRemove'])
        ->tag('doctrine.event_listener', ['event' => 'postFlush'])
        ->call('setAnnotationReader', [service('attribute_reader')]);
};
