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

use Thrace\MediaBundle\Model\FileInterface;

/**
 * Interface that implements FileManager
 */
interface FileManagerInterface extends BaseManagerInterface
{
    
    /**
     * Gets temporary file blob by name
     * 
     * @param string $name
     * @return string
     * @throws \Gaufrette\Exception\FileNotFound
     * @throws \RuntimeException
     */
    public function getTemporaryFileBlobByName($name);
    
    /**
     * Gets permanent file blob by name
     *
     * @param string $name
     * @return string
     * @throws \Gaufrette\Exception\FileNotFound
     * @throws \RuntimeException
     */
    public function getPermanentFileBlobByName($name);
    
    /**
     * Gets Gaufrette file
     * 
     * @param FileInterface $object
     * @return \Gaufrette\File
     * @throws \Gaufrette\Exception\FileNotFound
     */
    public function getTemporaryFile(FileInterface $object);
    
    /**
     * Copies file to temporary filesystem
     * 
     * @param FileInterface $object
     */
    public function copyFileToTemporaryDirectory(FileInterface $object);
    
    /**
     * Copies file to permanent filesystem
     * 
     * @param FileInterface $object
     */
    public function copyFileToPermanentDirectory(FileInterface $object);
    
    /**
     * Removes file form temporary filesystem
     * 
     * @param FileInterface $object
     */
    public function removeFileFromTemporaryDirectory(FileInterface $object);
    
    /**
     * Removes file from permenent filesystem
     * 
     * @param FileInterface $object
     */
    public function removeFileFromPermanentDirectory(FileInterface $object);
    
    /**
     * Removes file from temporary and  permanent filesystems
     * 
     * @param FileInterface $object
     */
    public function removeAllFiles(FileInterface $object);
}