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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Runroom\SortableBehaviorBundle\DependencyInjection\Configuration;
use Runroom\SortableBehaviorBundle\DependencyInjection\RunroomSortableBehaviorExtension;
use Runroom\SortableBehaviorBundle\Service\GedmoPositionHandler;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * @test
     */
    public function itExposesConfiguration(): void
    {
        $this->assertProcessedConfigurationEquals([
            'position_handler' => GedmoPositionHandler::class,
            'position_field' => [
                'default' => 'position',
                'entities' => [
                    SortableEntity::class => 'customPositionField',
                ],
            ],
            'sortable_groups' => ['entities' => [
                SortableEntity::class => ['sortableGroup'],
            ]],
        ], [
            __DIR__ . '/../Fixtures/configuration.yaml',
        ]);
    }

    /**
     * @test
     */
    public function itFailsOnInvalidConfiguration(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->assertProcessedConfigurationEquals([], [
            __DIR__ . '/../Fixtures/configuration_invalid.yaml',
        ]);
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new RunroomSortableBehaviorExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
