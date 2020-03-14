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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('runroom_sortable_behavior');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()

            ->scalarNode('position_handler')
                ->defaultValue('sortable_behavior.position.gedmo')
            ->end()

            ->arrayNode('position_field')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('default')
                        ->defaultValue('position')
                    ->end()
                    ->arrayNode('entities')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()

            ->arrayNode('sortable_groups')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('entities')
                        ->useAttributeAsKey('name')
                        ->prototype('variable')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
