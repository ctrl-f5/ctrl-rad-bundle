<?php

namespace Ctrl\RadBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ctrl_rad');

        $this->addTemplatesSection($rootNode);

        return $treeBuilder;
    }

    protected function addTemplatesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('base')->defaultValue('CtrlRadBundle::base.html.twig')->end()
                        ->scalarNode('app')->defaultValue('CtrlRadBundle::app.html.twig')->end()
                        ->scalarNode('app_frame')->defaultValue('CtrlRadBundle::_frame.html.twig')->end()
                        ->scalarNode('app_topbar')->defaultValue('CtrlRadBundle::_topbar.html.twig')->end()
                        ->scalarNode('app_topbar_extra')->defaultValue('CtrlRadBundle::_topbar_extra.html.twig')->end()
                        ->scalarNode('app_sidebar')->defaultValue('CtrlRadBundle::_sidebar.html.twig')->end()
                        ->scalarNode('index_table')->defaultValue('CtrlRadBundle:partial:_table.html.twig')->end()
                        ->scalarNode('filter_elements')->defaultValue('CtrlRadBundle:partial:_form_elements.html.twig')->end()
                        ->scalarNode('form_elements')->defaultValue('CtrlRadBundle:partial:_form_buttons.html.twig')->end()
                        ->scalarNode('form_buttons')->defaultValue('CtrlRadBundle:partial:_form_elements.html.twig')->end()
                    ->end()
                ->end()
            ->end();
    }
}
