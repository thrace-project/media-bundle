<?php
namespace Thrace\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('thrace_media');

        $this->addPlUploadConfiguration($rootNode);

        return $treeBuilder;
    }

    /**
     * Plupload configurations
     * 
     * @param ArrayNodeDefinition $rootNode
     * @return void
     */
    private function addPlUploadConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('temporary_filesystem_key')->isRequired()->end()
                ->scalarNode('media_filesystem_key')->isRequired()->end()
                ->booleanNode('enable_version')->defaultFalse()->end()
                ->arrayNode('jwplayer')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('key')->isRequired()->end()
                        ->scalarNode('skin')->defaultValue(null)->end()
                        ->scalarNode('html5player')->isRequired()->end()
                        ->scalarNode('flashplayer')->isRequired()->end()
                        ->booleanNode('autostart')->defaultFalse()->end()
                        ->scalarNode('width')->defaultValue('600')->end()
                        ->scalarNode('height')->defaultValue('400')->end()
                        ->scalarNode('type')->defaultValue('flv')->end()
                    ->end()
                ->end()
                ->arrayNode('plupload')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('runtimes')->defaultValue('html5,flash')->end()
                        ->scalarNode('plupload_flash_path_swf')
                            ->defaultNull()
                            ->beforeNormalization()
                                ->always()
                                ->then(function($v) {
                                    return trim($v, '/');
                                })
                            ->end()
                        ->end()
                        ->scalarNode('max_upload_size')->defaultValue('4M')->end()
                        ->scalarNode('normalize_width')->defaultValue(1000)->end()
                        ->scalarNode('normalize_height')->defaultValue(1000)->end()
                     ->end()
                ->end()
            ->end()
        ;
    }
}
