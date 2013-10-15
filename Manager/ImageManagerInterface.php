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

use Thrace\MediaBundle\Model\ImageInterface;

/**
 * Interface that implements ImageManager
 */
interface ImageManagerInterface extends BaseManagerInterface
{
    
    /**
     * Gets temporary image path by name
     * 
     * @param string $name
     * @return string
     */
    public function getTemporaryOriginalImagePath($name);
    
    /**
     * Gets permanent original image path by object
     * 
     * @param ImageInterface $object
     * @return string
     */
    public function getPermanentOriginalImagePath(ImageInterface $object);
    
    /**
     * Gets temporary image by object
     * 
     * @param ImageInterface $object
     * @return \Gaufrette\File
     * @throws \Gaufrette\Exception\FileNotFound
     */
    public function getTemporaryImage(ImageInterface $object);
    
    /**
     * Gets temporary original image by name
     *
     * @param string $name
     * @return string
     * @throws \Gaufrette\Exception\FileNotFound
     * @throws \RuntimeException
     */
    public function getTemporaryOriginalImageBlobByName($name);
    
    /**
     * Gets temporary image blob by name
     * 
     * @param string $name
     * @return string
     * @throws \Gaufrette\Exception\FileNotFound
     * @throws \RuntimeException
     */
    public function getTemporaryImageBlobByName($name);
    
    /**
     * Loads temporary image by name
     * 
     * @param string $name
     * @return \Imagine\Image\ImagineInterface
     * @throws  \RuntimeException
     */
    public function loadTemporaryImageByName($name);
    
    /**
     * Loads permanent image by name
     *
     * @param string $name
     * @return \Imagine\Image\ImagineInterface
     * @throws  \RuntimeException
     */
    public function loadPermanentImageByName($name);
    
    /**
     * Copies temporary image to orginal directory
     * 
     * @param string $name
     * @return void
     * @throws \RuntimeException
     */
    public function makeImageCopyToOriginalDirectory($name);
    
    /**
     * Normalize image to given width or height
     * 
     * @param string $name
     * @param string $content
     * @param integer $width
     * @param integer $height
     * @return string
     * @throws \RuntimeException
     */
    public function normalizeImage ($name, $content, $width, $height);
    
    /**
     * Copies images from permanent to temporary filesystem
     * 
     * @param ImageInterface $object
     * @return void
     * @throws \RuntimeException
     */
    public function copyImagesToTemporaryDirectory(ImageInterface $object);
    
    /**
     * Copies images from temporary to permanent filesystem
     * 
     * @param ImageInterface $object
     * @return void
     * @throws \RuntimeException
     */
    public function copyImagesToPermanentDirectory(ImageInterface $object);
    
    /**
     * Removes images from temporary filesystem
     * 
     * @param ImageInterface $object
     * @return void
     * @throws \RuntimeException
     */
    public function removeImagesFromTemporaryDirectory(ImageInterface $object);
    
    /**
     * Removes images from permanent filesystem
     *
     * @param ImageInterface $object
     * @return void
     * @throws \RuntimeException
     */
    public function removeImagesFromPermanentDirectory(ImageInterface $object);
    
    /**
     * Removes images from temporary and permanent filesystems
     * 
     * @param ImageInterface $object
     * @return void
     * @throws \RuntimeException
     */
    public function removeAllImages(ImageInterface $object);
    
    /**
     * Crops image by name and saves it to temporary filesystem
     * 
     * @param string $name
     * @param array $options
     * @return boolean
     * @throws \RuntimeException
     */
    public function crop($name, array $options);
    
    /**
     * Rotates image by name and saves it to temporary filesystem
     * 
     * @param string $name
     * @return boolean
     * @throws \RuntimeException
     */
    public function rotate($name);
    
    /**
     * Rotates image by name to original one and saves it to temporary filesystem
     *
     * @param string $name
     * @return boolean
     * @throws \RuntimeException
     */
    public function reset($name);
}