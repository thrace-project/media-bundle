<?php

namespace Thrace\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ThraceMediaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('thrace_media.temporary_filesystem_key', $config['temporary_filesystem_key']);
        $container->setParameter('thrace_media.media_filesystem_key', $config['media_filesystem_key']);
        $container->setParameter('thrace_media.enable_version', $config['enable_version']);
        $container->setParameter('thrace_media.jwplayer.options', $config['jwplayer']);  
        $container->setParameter('thrace_media.plupload.options', $config['plupload']);  
    }
    

}
