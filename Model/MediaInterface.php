<?php
/*
 * This file is part of ThraceMediaBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\MediaBundle\Model;

/**
 * Interface that implements media
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
interface MediaInterface extends BaseFileInterface
{
   
    const TYPE_FLV = 'flv';
    
    const TYPE_MP3 = 'mp3';
    
    /**
     * Gets media type (needed by jwplayer)
     */
    public function getType();
    
    /**
     * Gets relative media path
     */
    public function getMediaPath();
}