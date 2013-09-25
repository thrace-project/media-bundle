<?php
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

class ImageManager extends AbstractManager implements ImageManagerInterface
{       
    protected $imagine;
    
    protected $originalDirectory = 'original';
    
    public function setImagine(ImagineInterface $imagine)
    {   
        $this->imagine = $imagine;
        return $this;
    }
    
    public function getTemporaryOriginalImagePath($name)
    {
        return $this->originalDirectory . DIRECTORY_SEPARATOR . $name;
    }
    
    public function getPermenentOriginalImagePath(ImageInterface $object)
    {
        return $object->getUploadDir() . DIRECTORY_SEPARATOR . $this->originalDirectory . DIRECTORY_SEPARATOR . $object->getName();
    }
    
    public function getTemporaryImage(ImageInterface $object)
    {
        return $this->temporaryFilesystem->get($object->getName());
    }

    public function getTemporaryImageBlobByName($name)
    {
        return $this->temporaryFilesystem->read($name);
    }
    
    public function getTemporaryOriginalImageBlobByName($name)
    {
        return $this->temporaryFilesystem->read($this->originalDirectory . DIRECTORY_SEPARATOR . $name);
    }
    
    public function getPermenentImageByKey($key)
    {
        return $this->imagine->load($this->mediaFilesystem->read($key));        
    }
    
    public function getPermenentImageBlobByKey($key)
    {
        return $this->mediaFilesystem->read($key);
    }
        
    public function makeImageCopyToOriginalDirectory($name)
    {
        $content = $this->getTemporaryImageBlobByName($name);
        $this->temporaryFilesystem->write($this->getTemporaryOriginalImagePath($name), $content, true);
    }
    
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
    
    public function copyImagesToTemporaryDirectory(ImageInterface $object)
    {
        $tempHash = $this->checksumTemporaryFileByName($object->getName());
        
        if ($tempHash){
            return;
        }
        
        $image = $this->mediaFilesystem->read($object->getImagePath());
        $originalImage = $this->mediaFilesystem->read($this->getPermenentOriginalImagePath($object));
        
        $this->temporaryFilesystem->write($object->getName(), $image, true);
        $this->temporaryFilesystem->write($this->getTemporaryOriginalImagePath($object->getName()), $originalImage, true);
              
    }
    
    public function copyImagesToPermenentDirectory(ImageInterface $object)
    {
        $image = $this->temporaryFilesystem->read($object->getName());
        $originalImage = $this->temporaryFilesystem->read($this->getTemporaryOriginalImagePath($object->getName()));
        
        $this->mediaFilesystem->write($object->getImagePath(), $image, true);
        $this->mediaFilesystem->write(
            $this->getPermenentOriginalImagePath($object), 
            $originalImage, 
            true
        );
    }
    
    public function removeImagesFromTemporaryDirectory(ImageInterface $object)
    {
        try {
            $this->temporaryFilesystem->delete($object->getName());
            $this->temporaryFilesystem->delete($this->getTemporaryOriginalImagePath($object->getName()));
        } catch (FileNotFound $e){}        
    }
    
    public function removeImagesFromPermenentDirectory(ImageInterface $object)
    {   
        try {
            $this->mediaFilesystem->delete($object->getImagePath());
            $this->mediaFilesystem->delete($this->getPermenentOriginalImagePath($object));
        } catch (FileNotFound $e){}        
    }
    
    public function removeAllImages(ImageInterface $object)
    {
        $this->removeImagesFromTemporaryDirectory($object);
        $this->removeImagesFromPermenentDirectory($object);
    }
    
    public function crop($name, array $options)
    {
        $extension = $this->getExtension($name);
        $imageBlob = $this->getTemporaryImageBlobByName($name);
        $image = $this->imagine->load($imageBlob);
        
        try{
            $content = $image->crop(
                new Point($options['x'], $options['y']),
                new Box($options['w'], $options['h'])
            )->get($extension);
        } catch (OutOfBoundsException $e){
            return false;
        } catch (InvalidArgumentException $e){
            return false;
        }
 
        $this->saveToTemporaryDirectory($name, $content);
        
        return true;
    }
    
    public function rotate($name)
    {
        $extension = $this->getExtension($name);
        $imageBlob = $this->getTemporaryImageBlobByName($name);
        $image = $this->imagine->load($imageBlob);
        $imageBlob = $image->rotate(90)->get($extension);
        $this->saveToTemporaryDirectory($name, $imageBlob);
    }
    
    public function reset($name)
    {
        $content = $this->getTemporaryOriginalImageBlobByName($name);
        $this->saveToTemporaryDirectory($name, $content);
    }
}