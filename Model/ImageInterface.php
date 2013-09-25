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
interface ImageInterface 
{
    /**
     * Sets filename
     *
     * @param string $name
     */
    public function setName($name);
    
    /**
     * Returns filename
     *
     * @return string
    */
    public function getName();
    
    /**
     * Set original filename
     *
     * @param string $name
    */
    public function setOriginalName($name);
    
    /**
     * Get original filename
     *
     * @return string
    */
    public function getOriginalName();
    
    /**
     * Sets Image size
     *
     * @param integer $size
    */
    public function setSize($size);
    
    /**
     * Gets Image size
     *
     * @return string
    */
    public function getSize();
    
    /**
     * Sets image title
     *
     * @param string $title
    */
    public function setTitle($title);
    
    /**
     * Gets image title
     *
     * @return string | null
    */
    public function getTitle();
    
    /**
     * Sets image caption
     *
     * @param string $caption
    */
    public function setCaption($caption);
    
    /**
     * Gets image caption
     *
     * @return string | null
    */
    public function getCaption();
    
    /**
     * Sets image description
     *
     * @param string $description
    */
    public function setDescription($description);
    
    /**
     * Gets image description
     *
     * @return string | null
    */
    public function getDescription();
    
    /**
     * Sets image md5 hash
     * Checks if image is changed
     *
     * @param string $hash
    */
    public function setHash($hash);
    
    /**
     * Gets image md5 hash
     *
     * @return string
    */
    public function getHash();
    
    /**
     * Returns database version of the image
    */
    public function getVersion();
    
    /**
     * Sets current version of the image
     *
     * @param integer $currentVersion
    */
    public function setCurrentVersion($currentVersion);
    
    /**
     * Gets current version of the image
    */
    public function getCurrentVersion();
    
    /**
     * Gets image upload directory
     * It suggests it is located in "web" directory
     *
     * @return string
    */
    public function getUploadDir();
    
    /**
     * Gets path to image
     *
     * @return string
    */
    public function getImagePath();
    
    /**
     * Enabled image
     *
     * @param boolean $bool
    */
    public function setEnabled($bool);
    
    /**
     * Check if image is enabled
     *
     * @return boolean
    */
    public function isEnabled();
    
    /**
     * Schedules image for deletion
     *
     * @param boolean $bool
    */
    public function setScheduledForDeletion($bool);
    
    /**
     * Checks if image is scheduled for deletion
     *
     * @return bool
     * @return void
    */
    public function isScheduledForDeletion();
}