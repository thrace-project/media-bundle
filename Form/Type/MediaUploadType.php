<?php
/*
 * This file is part of ThraceMediaBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\MediaBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * This class creates jquery video/audio upload element
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class MediaUploadType extends FileUploadType
{
    /**
     * @var array
     */
    protected $jwplayerOptions;
    
    /**
     * Sets jwplayer options
     * 
     * @param array $jwplayerOptions
     */
    public function setJwplayerOptions(array $jwplayerOptions)
    {
        $this->jwplayerOptions = $jwplayerOptions;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'thrace_media_upload';
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDefaultConfigs()
    {
        $jwplayerOptions = $this->jwplayerOptions;
        $configs = parent::getDefaultConfigs();
        
        return array_merge($jwplayerOptions, $configs);
    }
}