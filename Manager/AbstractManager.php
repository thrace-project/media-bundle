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

use Knp\Bundle\GaufretteBundle\FilesystemMap;

use Gaufrette\Exception\FileNotFound;

/**
 * Base manager class
 */
class AbstractManager implements BaseManagerInterface
{
    /**
     * @var \Gaufrette\Filesystem
     */
    protected $temporaryFilesystem;
    
    /**
     * @var \Gaufrette\Filesystem
     */
    protected $mediaFilesystem;
    
    /**
     * Construct
     * 
     * @param FilesystemMap $filesystemMap
     * @param string $temporaryFilesystemKey
     * @param string $mediaFilesystemKey
     */
    public function __construct(FilesystemMap $filesystemMap, $temporaryFilesystemKey, $mediaFilesystemKey)
    {   
        $this->temporaryFilesystem = $filesystemMap->get($temporaryFilesystemKey);
        $this->mediaFilesystem = $filesystemMap->get($mediaFilesystemKey);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getExtension($filename)
    {
        return pathinfo($filename, \PATHINFO_EXTENSION);
    }
    
    /**
     * {@inheritDoc}
     */
    public function checksumTemporaryFileByName($name)
    {
        try {
            $hash = $this->temporaryFilesystem->checksum($name);
        } catch (FileNotFound $e){
            $hash = null;
        }
        
        return $hash;
    }
    
    /**
     * {@inheritDoc}
     */
    public function saveToTemporaryDirectory($name, $content)
    {
        $this->temporaryFilesystem->write($name, $content, true);
    }
    
    /**
     * {@inheritDoc}
     */
    public function clearCache($maxAge)
    {
        $delTime = (time() - (int) $maxAge);
        $num = 0;
        
        foreach ($this->temporaryFilesystem->keys() as $key){
            if (!$this->temporaryFilesystem->getAdapter()->isDirectory($key)){
                if ($delTime > $this->temporaryFilesystem->mtime($key)){
                    $this->temporaryFilesystem->delete($key);
                    $num++;
                }
            }
        }
        
        return $num;
    }
}