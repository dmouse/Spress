<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Definition;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Definition for config.toml
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ConfigDefinition implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(0);

        $rootNode->children()
            ->scalarNode('source')
            ->end()
            ->scalarNode('destination')
            ->end()
            ->ArrayNode('include')
            ->end()
            ->ArrayNode('exclude')
            ->end()
            ->scalarNode('template')
            ->end()
            ->ArrayNode('markdown_ext')
                ->prototype('scalar')
            ->end()
            ->scalarNode('permalink')
            ->end()
            ->integerNode('paginate')
                ->min(0)
            ->end()
            ->scalarNode('paginate_path')
            ->end()
            ->integerNode('limit_posts')
                ->min(0)
            ->end()
            ->booleanNode('safe')
            ->end()
            ->scalarNode('host')
            ->end()
            ->integerNode('port')
                ->min(0)->max(6534)
            ->end()
            ->scalarNode('baseurl')
            ->end()
            ->scalarNode('url')
            ->end()
        ->end();

        return $treeBuilder;
    }
}