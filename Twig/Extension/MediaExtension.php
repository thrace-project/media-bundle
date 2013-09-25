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
    
    public function generateDownloadUrl(FileInterface $file)
    {
        return $this->container->get('router')->generate('thrace_media_file_download', array(
            'filepath' => $file->getFilePath(), 'filename' => $file->getOriginalName()
        ), true);
    }
    
    /**
     * Converts bytes
     *
     * @param integer $bytes
     * @return string
     */
    public function fileSize($bytes)
    {
        $bytes = (int) $bytes;
        $suffix = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return $bytes ? round($bytes/pow(1024, ($i = floor(log($bytes, 1024)))), 2) . $suffix[$i] : '0 Bytes';
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            'thrace_image' => new \Twig_Function_Method($this, 'renderImage', array('is_safe' => array('html'))),
            'thrace_file_download_url' => new \Twig_Function_Method($this, 'generateDownloadUrl', array('is_safe' => array('html'))),
            'thrace_filesize' => new \Twig_Function_Method($this, 'fileSize')
        );
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'thrace_media';
    }
}
