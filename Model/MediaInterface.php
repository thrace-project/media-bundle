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
interface MediaInterface extends FileInterface
{
    const TYPE_FLV = 'flv';
    
    const TYPE_MP3 = 'mp3';
    
    public function getType();
    
    public function getMediaPath();
}