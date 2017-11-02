<?php

namespace Keboola\Processor\AddFilenameColumn;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigDefinition implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("parameters");

        $rootNode
            ->children()
                ->scalarNode("column_name")
                    ->defaultValue("filename")
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
