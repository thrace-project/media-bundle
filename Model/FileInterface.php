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
 * Interface that implements file
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
interface FileInterface
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
     * Sets File size
     *
     * @param integer $size
     */
    public function setSize($size);

    /**
     * Gets File size
     *
     * @return string
     */
    public function getSize();

    /**
     * Sets file title
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * Gets file title
     *
     * @return string | null
     */
    public function getTitle();

    /**
     * Sets file caption
     *
     * @param string $caption
     */
    public function setCaption($caption);

    /**
     * Gets file caption
     *
     * @return string | null
     */
    public function getCaption();

    /**
     * Sets file description
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Gets file description
     *
     * @return string | null
     */
    public function getDescription();

    /**
     * Sets file md5 hash
     * Checks if file is changed
     *
     * @param string $hash
     */
    public function setHash($hash);

    /**
     * Gets file md5 hash
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
     * Gets file upload directory
     * It suggests it is located in "web" directory
     *
     * @return string
     */
    public function getUploadDir();

    /**
     * Gets path to file
     *
     * @return string
     */
    public function getFilePath();

    /**
     * Enabled file
     *
     * @param boolean $bool
     */
    public function setEnabled($bool);

    /**
     * Check if file is enabled
     *
     * @return boolean
     */
    public function isEnabled();
    
    /**
     * Schedules file for deletion
     * 
     * @param boolean $bool
     */
    public function setScheduledForDeletion($bool);
    
    /**
     * Checks if file is scheduled for deletion
     * 
     * @return bool
     * @return void
     */
    public function isScheduledForDeletion();


}