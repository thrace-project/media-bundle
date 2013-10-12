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
 * Interface that implements image
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
interface ImageInterface extends BaseFileInterface
{

    /**
     * Gets relative path to image
     *
     * @return string
    */
    public function getImagePath();
}