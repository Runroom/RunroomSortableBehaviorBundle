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

namespace Runroom\SortableBehaviorBundle\Tests\Integration;

use Gedmo\Mapping\Driver\AttributeReader;
use Gedmo\Sortable\SortableListener;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\SortableBehaviorBundle\Action\MoveAction;
use Runroom\SortableBehaviorBundle\DependencyInjection\RunroomSortableBehaviorExtension;
use Runroom\SortableBehaviorBundle\Service\GedmoPositionHandler;
use Runroom\SortableBehaviorBundle\Service\ORMPositionHandler;
use Runroom\SortableBehaviorBundle\Twig\ObjectPositionExtension;

final class RunroomSortableBehaviorExtensionTest extends AbstractExtensionTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', [
            'SonataAdminBundle' => true,
        ]);

        $this->load();
    }

    public function testItHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService('runroom.sortable_behavior.action.move', MoveAction::class);
        $this->assertContainerBuilderHasService('runroom.sortable_behavior.twig.object_position', ObjectPositionExtension::class);
        $this->assertContainerBuilderHasService('runroom.sortable_behavior.service.orm_position', ORMPositionHandler::class);
        $this->assertContainerBuilderHasService('runroom.sortable_behavior.service.gedmo_position', GedmoPositionHandler::class);
        $this->assertContainerBuilderHasService('runroom.sortable_behavior.attribute_reader', AttributeReader::class);
        $this->assertContainerBuilderHasService('runroom.sortable_behavior.sortable_listener', SortableListener::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomSortableBehaviorExtension()];
    }
}
