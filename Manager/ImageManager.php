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

use Imagine\Exception\InvalidArgumentException;

use Imagine\Exception\OutOfBoundsException;

use Imagine\Image\ImagineInterface;

use Thrace\MediaBundle\Model\ImageInterface;

use Imagine\Image\Box;

use Imagine\Image\Point;

use Gaufrette\Exception\FileNotFound;

use Gaufrette\Filesystem;

use Gaufrette\Adapter;

/**
 * Imagemanager that handles image
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class ImageManager extends AbstractManager implements ImageManagerInterface
{       
    /**
     * @var \Imagine\Image\ImagineInterface
     */
    protected $imagine;
    
    /**
     * @var string
     */
    protected $originalDirectory = 'original';
    
    /**
     * Sets Imagine service
     * 
     * @param ImagineInterface $imagine
     * @return \Thrace\MediaBundle\Manager\ImageManager
     */
    public function setImagine(ImagineInterface $imagine)
    {   
        $this->imagine = $imagine;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTemporaryOriginalImagePath($name)
    {
        return $this->originalDirectory . DIRECTORY_SEPARATOR . $name;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPermanentOriginalImagePath(ImageInterface $object)
    {
        return $object->getUploadDir() . DIRECTORY_SEPARATOR . $this->originalDirectory . DIRECTORY_SEPARATOR . $object->getName();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTemporaryImage(ImageInterface $object)
    {
        return $this->temporaryFilesystem->get($object->getName());
    }

    /**
     * {@inheritDoc}
     */
    public function getTemporaryImageBlobByName($name)
    {
        return $this->temporaryFilesystem->read($name);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTemporaryOriginalImageBlobByName($name)
    {
        return $this->temporaryFilesystem->read($this->originalDirectory . DIRECTORY_SEPARATOR . $name);
    }
    
    /**
     * {@inheritDoc}
     */
    public function loadTemporaryImageByName($name)
    {
        return $this->imagine->load($this->temporaryFilesystem->read($name));        
    }
    
    /**
     * {@inheritDoc}
     */
    public function loadPermanentImageByName($name)
    {   
        return $this->imagine->load($this->mediaFilesystem->read($name));        
    }
        
    /**
     * {@inheritDoc}
     */
    public function makeImageCopyToOriginalDirectory($name)
    {
        $content = $this->getTemporaryImageBlobByName($name);
        $this->temporaryFilesystem->write($this->getTemporaryOriginalImagePath($name), $content, true);
    }
    
    /**
     * {@inheritDoc}
     */
    public function normalizeImage ($name, $content, $width, $height)
    {
        $extension = $this->getExtension($name);
        $image = $this->imagine->load($content);
        $size = $image->getSize();
        $box = new \Imagine\Image\Box($size->getWidth(), $size->getHeight());
    
        if ($size->getWidth() >= $size->getHeight() && $size->getWidth() > $width) {
            return $image->resize($box->widen($width))->get($extension);
        } elseif ($size->getWidth() < $size->getHeight() && $size->getHeight() > $height) {
            return $image->resize($box->heighten($height))->get($extension);
        }
    
        return $image->get($extension);
    }
    
    /**
     * {@inheritDoc}
     */
    public function copyImagesToTemporaryDirectory(ImageInterface $object)
    {
        $tempHash = $this->checksumTemporaryFileByName($object->getName());
        
        if ($tempHash){
            return;
        }
        
        $image = $this->mediaFilesystem->read($object->getImagePath());
        $originalImage = $this->mediaFilesystem->read($this->getPermanentOriginalImagePath($object));
        
        $this->temporaryFilesystem->write($object->getName(), $image, true);
        $this->temporaryFilesystem->write($this->getTemporaryOriginalImagePath($object->getName()), $originalImage, true);
              
    }
    
    /**
     * {@inheritDoc}
     */
    public function copyImagesToPermanentDirectory(ImageInterface $object)
    {
        $image = $this->temporaryFilesystem->read($object->getName());
        $originalImage = $this->temporaryFilesystem->read($this->getTemporaryOriginalImagePath($object->getName()));
        
        $this->mediaFilesystem->write($object->getImagePath(), $image, true);
        $this->mediaFilesystem->write(
            $this->getPermanentOriginalImagePath($object), 
            $originalImage, 
            true
        );
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeImagesFromTemporaryDirectory(ImageInterface $object)
    {
        try {
            $this->temporaryFilesystem->delete($object->getName());
            $this->temporaryFilesystem->delete($this->getTemporaryOriginalImagePath($object->getName()));
        } catch (FileNotFound $e){}        
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeImagesFromPermanentDirectory(ImageInterface $object)
    {   
        try {
            $this->mediaFilesystem->delete($object->getImagePath());
            $this->mediaFilesystem->delete($this->getPermanentOriginalImagePath($object));
        } catch (FileNotFound $e){}        
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeAllImages(ImageInterface $object)
    {
        $this->removeImagesFromTemporaryDirectory($object);
        $this->removeImagesFromPermanentDirectory($object);
    }
    
    /**
     * {@inheritDoc}
     */
    public function crop($name, array $options)
    {
        $extension = $this->getExtension($name);
        $image = $this->loadTemporaryImageByName($name);
        
        try{
            $content = $image->crop(
                new Point($options['x'], $options['y']),
                new Box($options['w'], $options['h'])
            )->get($extension);
        } catch (OutOfBoundsException $e){
            return false;
        } 
 
        $this->saveToTemporaryDirectory($name, $content);
        
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function rotate($name)
    {
        $extension = $this->getExtension($name);
        $image = $this->loadTemporaryImageByName($name);
        $content = $image->rotate(90)->get($extension);
        $this->saveToTemporaryDirectory($name, $content);
        
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function reset($name)
    {
        $content = $this->getTemporaryOriginalImageBlobByName($name);
        $this->saveToTemporaryDirectory($name, $content);
        
        return true;
    }
}