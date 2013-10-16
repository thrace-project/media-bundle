<?php
/*
 * This file is part of ThraceMediaBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\MediaBundle\Twig\Extension;

use Thrace\MediaBundle\Model\MediaInterface;

use Thrace\MediaBundle\Model\FileInterface;

use Thrace\MediaBundle\Model\ImageInterface;

use Symfony\Component\DependencyInjection\Container;

/**
 * Twig extension
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class MediaExtension extends \Twig_Extension
{

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * Construct
     * 
     * @param Container $container
     */
    public function __construct(Container $container)
    {
       $this->container = $container;
    }

    /**
     * @param ImageInterface $image
     * 
     * @return string
     */
    public function renderImage(ImageInterface $image, $filter, array $options = array())
    {
        return
            $this->container->get('templating')
                ->render('ThraceMediaBundle:Media:image.html.twig',
                   array('image' => $image, 'filter' => $filter, 'options' => $options)
                )
            ;
    }
    
    /**
     * Return download url
     * 
     * @param FileInterface $file
     * @return string
     */
    public function generateDownloadUrl(FileInterface $file)
    {
        return $this->container->get('router')->generate('thrace_media_file_download', array(
            'filepath' => $file->getFilePath(), 'filename' => $file->getOriginalName()
        ), true);
    }
    
    /**
     * Renders jwplayer
     * 
     * @param MediaInterface $media
     * @param array $options
     * 
     * @return string
     */
    public function renderMedia(MediaInterface $media, array $options = array())
    {
        $defaultOptions = $this->container->getParameter('thrace_media.jwplayer.options');
        $configs = array(
            'key' => $defaultOptions['key'],
            'html5player' => $defaultOptions['html5player'],
            'flashplayer' => $defaultOptions['flashplayer'],
            'type' => $media->getType(),
            'id' => uniqid('thrace_media', true),
            'file' => $this->container->get('router')->generate('thrace_media_render', array(
                'name' => $media->getMediaPath(),
                'hash' => $media->getHash()
            ), true)
        );
        
        $configs = array_replace_recursive($configs, $options);
        
        return $this->container->get('templating')
            ->render('ThraceMediaBundle:Media:media.html.twig',
                array('media' => $media, 'configs' => $configs)
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            'thrace_image' => new \Twig_Function_Method($this, 'renderImage', array('is_safe' => array('html'))),
            'thrace_media' => new \Twig_Function_Method($this, 'renderMedia', array('is_safe' => array('html'))),
            'thrace_file_download_url' => new \Twig_Function_Method($this, 'generateDownloadUrl', array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'thrace_media';
    }
}
