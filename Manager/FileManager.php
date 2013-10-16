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

use Gaufrette\Exception\FileNotFound;

use Gaufrette\Filesystem;

use Thrace\MediaBundle\Model\FileInterface;

use Gaufrette\Adapter;

/**
 * Filemanager that handles file
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class FileManager extends AbstractManager implements FileManagerInterface 
{   

    /**
     * {@inheritDoc}
     */
    public function getTemporaryFile(FileInterface $object)
    {
        return $this->temporaryFilesystem->get($object->getName());
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTemporaryFileBlobByName($name)
    {
        return $this->temporaryFilesystem->read($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getPermanentFileBlobByName($name)
    {
        return $this->mediaFilesystem->read($name);
    }
    
    /**
     * {@inheritDoc}
     */
    public function copyFileToTemporaryDirectory(FileInterface $object)
    {
        $tempHash = $this->checksumTemporaryFileByName($object->getName());
        
        if ($tempHash){
            return;
        }
        
        $content = $this->mediaFilesystem->read($object->getFilePath());
        $this->temporaryFilesystem->write($object->getName(), $content);
    }
    
    /**
     * {@inheritDoc}
     */
    public function copyFileToPermanentDirectory(FileInterface $object)
    {
        $tempFile = $this->temporaryFilesystem->read($object->getName());
        return $this->mediaFilesystem->write($object->getFilePath(), $tempFile, true);
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeFileFromTemporaryDirectory(FileInterface $object)
    {
        try {
            $this->temporaryFilesystem->delete($object->getName());
        } catch (FileNotFound $e){} 
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeFileFromPermanentDirectory(FileInterface $object)
    {
        try {
            $this->mediaFilesystem->delete($object->getFilePath());
        } catch (FileNotFound $e){} 
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeAllFiles(FileInterface $object)
    {
        $this->removeFileFromTemporaryDirectory($object);
        $this->removeFileFromPermanentDirectory($object);
    }
}