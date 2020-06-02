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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\SortableBehaviorBundle\Controller\SortableAdminController;
use Runroom\SortableBehaviorBundle\DependencyInjection\RunroomSortableBehaviorExtension;
use Runroom\SortableBehaviorBundle\Service\GedmoPositionHandler;
use Runroom\SortableBehaviorBundle\Service\ORMPositionHandler;
use Runroom\SortableBehaviorBundle\Twig\ObjectPositionExtension;

class RunroomSortableBehaviorExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->load();
    }

    /** @test */
    public function itHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService(SortableAdminController::class);
        $this->assertContainerBuilderHasService(ObjectPositionExtension::class);
        $this->assertContainerBuilderHasService(ORMPositionHandler::class);
        $this->assertContainerBuilderHasService(GedmoPositionHandler::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomSortableBehaviorExtension()];
    }
}
