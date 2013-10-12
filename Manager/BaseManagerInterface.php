<?php
/*
 * This file is part of ThraceMediaBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\MediaBundle\Manager;

/**
 * Interface that implements BaseManager
 */
interface BaseManagerInterface
{
    /**
     * Gets file extension
     * 
     * @param string $filename
     * @return file extension
     */
    public function getExtension($filename);
    
    /**
     * Gets hash of temporary file
     * 
     * @param string $name
     * @return null | string
     */
    public function checksumTemporaryFileByName($name);
    
    /**
     * Saves file to temporary directory
     * 
     * @param string $name
     * @param string $content
     * @return void
     */
    public function saveToTemporaryDirectory($name, $content);
    
    /**
     * Deletes unused files from temporary directory 
     * 
     * @param integer $maxAge
     * @return integer - number if files removed
     */
    public function clearCache($maxAge);
}