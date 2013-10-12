<?php
namespace Thrace\MediaBundle\Tests\Manager;

use Imagine\Exception\OutOfBoundsException;

use Imagine\Image\Box;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

class ImageManagerTest extends BaseTestCase
{
    
    public function testGetTemporaryOriginalImagePath()
    {
        $result = $this->getImageManager()->getTemporaryOriginalImagePath('image.jpg');
        
        $this->assertSame('original/image.jpg', $result);
    }
    
    public function testGetPermanentOriginalImagePath()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        $objectMock
            ->expects($this->exactly(1))
            ->method('getUploadDir')
            ->will($this->returnValue('/product'))
        ;
        
        $objectMock
            ->expects($this->exactly(1))
            ->method('getName')
            ->will($this->returnValue('image.jpg'))
        ;
 
        
        $result = $this->getImageManager()->getPermanentOriginalImagePath($objectMock);
        
        $this->assertSame('/product/original/image.jpg', $result);
    }
    
    public function testGetTemporaryImage()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');

        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('get')
        ;
        
        $this->getImageManager($tempFileSystem)->getTemporaryImage($objectMock);
    }
    
    public function testGetTemporaryImageBlobByName()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');

        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('read')
            ->will($this->returnValue('binary'))
        ;
        
        $result = $this->getImageManager($tempFileSystem)->getTemporaryImageBlobByName('image.jpg');
        $this->assertSame('binary', $result);
    }
    
    public function testGetTemporaryOriginalImageBlobByName()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');

        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('read')
            ->with($this->stringContains('original/image.jpg'))
            ->will($this->returnValue('binary'))
        ;
        
        $result = $this->getImageManager($tempFileSystem)->getTemporaryOriginalImageBlobByName('image.jpg');
        $this->assertSame('binary', $result);
    }
    
    public function testLoadTemporaryImageByName()
    {
        $imagine = $this->getMock('Imagine\Image\ImagineInterface');
        $imagine
            ->expects($this->exactly(1))
            ->method('load')   
        ;
        
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('read')
            ->with($this->stringContains('image.jpg'))
            ->will($this->returnValue('binary'))
        ;
        
        $imageManager = $this->getImageManager($tempFileSystem);
        $imageManager->setImagine($imagine);
        
        $imageManager->loadTemporaryImageByName('image.jpg');
    }
    
    public function _testLoadPermanentImageByName()
    {
        $imagine = $this->getMock('Imagine\Image\ImagineInterface');
        $imagine
            ->expects($this->exactly(1))
            ->method('load')   
        ;
        
        $mediaFileSystem = $this->getMediaFilesystemMock();
        
        $mediaFileSystem
            ->expects($this->exactly(1))
            ->method('read')
            ->with($this->stringContains('image.jpg'))
            ->will($this->returnValue('binary'))
        ;
        
        $imageManager = $this->getImageManager(null, $mediaFileSystem);
        $imageManager->setImagine($imagine);
        
        $imageManager->loadPermenentImageByName('image.jpg');
    }
    
    public function testMakeImageCopyToOriginalDirectory()
    {
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('read')
            ->with($this->stringContains('image.jpg'))
            ->will($this->returnValue('binary'))
        ;
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('write')
        ;
        
        $imageManager = $this->getImageManager($tempFileSystem);
        $imageManager->makeImageCopyToOriginalDirectory('image.jpg');
    }
    
    public function testNormalizeImage()
    {
        $box = new Box(600, 500);

        $image = $this->getMock('Imagine\Image\ImageInterface');
        $image
            ->expects($this->exactly(1))
            ->method('getSize')
            ->will($this->returnValue($box))
        ;

        $image
            ->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValue('binary'))
        ;
        
        $imagine = $this->getMock('Imagine\Image\ImagineInterface');
        $imagine
            ->expects($this->exactly(1))
            ->method('load')
            ->will($this->returnValue($image))
        ;
        
        $imageManager = $this->getImageManager();
        $imageManager->setImagine($imagine);
        
        $result = $imageManager->normalizeImage('image.jpg', 'binary', 1000, 1000);
        $this->assertSame('binary', $result);
        
    }
    
    public function testNormalizeImageWithOverWidth()
    {
        $box = new Box(1001, 500);

        $image = $this->getMock('Imagine\Image\ImageInterface');
        $image
            ->expects($this->exactly(1))
            ->method('getSize')
            ->will($this->returnValue($box))
        ;
        
        $image
            ->expects($this->exactly(1))
            ->method('resize')
            ->will($this->returnSelf())
        ;
        
        $image
            ->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValue('normalized_binary'))
        ;
        
        $imagine = $this->getMock('Imagine\Image\ImagineInterface');
        $imagine
            ->expects($this->exactly(1))
            ->method('load')
            ->will($this->returnValue($image))
        ;
        
        $imageManager = $this->getImageManager();
        $imageManager->setImagine($imagine);
        
        $result = $imageManager->normalizeImage('image.jpg', 'binary', 1000, 1000);
        $this->assertSame('normalized_binary', $result);
        
    }
    
    public function testNormalizeImageWithOverHeight()
    {
        $box = new Box(500, 1001);

        $image = $this->getMock('Imagine\Image\ImageInterface');
        $image
            ->expects($this->exactly(1))
            ->method('getSize')
            ->will($this->returnValue($box))
        ;
        
        $image
            ->expects($this->exactly(1))
            ->method('resize')
            ->will($this->returnSelf())
        ;
        
        $image
            ->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValue('normalized_binary'))
        ;
        
        $imagine = $this->getMock('Imagine\Image\ImagineInterface');
        $imagine
            ->expects($this->exactly(1))
            ->method('load')
            ->will($this->returnValue($image))
        ;
        
        $imageManager = $this->getImageManager();
        $imageManager->setImagine($imagine);
        
        $result = $imageManager->normalizeImage('image.jpg', 'binary', 1000, 1000);
        $this->assertSame('normalized_binary', $result);
        
    }
    
    public function testCopyImagesToTemporaryDirectory()
    {
        $image = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        
        $tempFilesystem = $this->getTemporaryFilesystemMock();
        
        $tempFilesystem
            ->expects($this->exactly(1))
            ->method('checksum')
            ->will($this->returnValue(null))
        ;
        
        $tempFilesystem
            ->expects($this->exactly(2))
            ->method('write')
        ;
        
        $mediaFilesystem = $this->getPermanentFilesystemMock();
        
        $mediaFilesystem
            ->expects($this->exactly(2))
            ->method('read')
        ;
        
        $this->getImageManager($tempFilesystem, $mediaFilesystem)->copyImagesToTemporaryDirectory($image);
        
    }
    
    public function testCopyImagesToTemporaryDirectoryWithExistingImages()
    {
        $image = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        
        $tempFilesystem = $this->getTemporaryFilesystemMock();
        
        $tempFilesystem
            ->expects($this->exactly(1))
            ->method('checksum')
            ->will($this->returnValue('hash'))
        ;
        
        $tempFilesystem
            ->expects($this->exactly(0))
            ->method('write')
        ;
        
        $mediaFilesystem = $this->getPermanentFilesystemMock();
        
        $mediaFilesystem
            ->expects($this->exactly(0))
            ->method('read')
        ;
        
        $this->getImageManager($tempFilesystem, $mediaFilesystem)->copyImagesToTemporaryDirectory($image);
        
    }
    
    public function testCopyImagesToPermanentDirectory()
    {
        $image = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        
        $tempFilesystem = $this->getTemporaryFilesystemMock();
        
        $tempFilesystem
            ->expects($this->exactly(2))
            ->method('read')
        ;
        
        $mediaFilesystem = $this->getPermanentFilesystemMock();
        
        $mediaFilesystem
            ->expects($this->exactly(2))
            ->method('write')
        ;
        
        $this->getImageManager($tempFilesystem, $mediaFilesystem)->copyImagesToPermanentDirectory($image);        
    }
    
    public function testRemoveImagesFromTemporaryDirectory()
    {
        $image = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        
        $tempFilesystem = $this->getTemporaryFilesystemMock();
        
        $tempFilesystem
            ->expects($this->exactly(2))
            ->method('delete')
        ;
        
        $this->getImageManager($tempFilesystem)->removeImagesFromTemporaryDirectory($image);        
    }
    
    public function testRemoveImagesFromTemporaryDirectoryWithInvalidImage()
    {
        $image = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        
        $tempFilesystem = $this->getTemporaryFilesystemMock();
        
        $tempFilesystem
            ->expects($this->exactly(1))
            ->method('delete')
            ->will($this->throwException(new \Gaufrette\Exception\FileNotFound('image.jpg')))
        ;
        
        $this->getImageManager($tempFilesystem)->removeImagesFromTemporaryDirectory($image);        
    }
    
    public function testRemoveImagesFromPermanentDirectory()
    {
        $image = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        
        $mediaFilesystem = $this->getPermanentFilesystemMock();
        
        $mediaFilesystem
            ->expects($this->exactly(2))
            ->method('delete')
        ;
        
        $this->getImageManager(null, $mediaFilesystem)->removeImagesFromPermanentDirectory($image);        
    }
    
    public function testRemoveAllImages()
    {
        $image = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        
        $tempFilesystem = $this->getTemporaryFilesystemMock();
        
        $tempFilesystem
            ->expects($this->exactly(2))
            ->method('delete')
        ;
        
        $mediaFilesystem = $this->getPermanentFilesystemMock();
        
        $mediaFilesystem
            ->expects($this->exactly(2))
            ->method('delete')
        ;
        
        $this->getImageManager($tempFilesystem, $mediaFilesystem)->removeAllImages($image);        
    }
    
    public function testCrop()
    {
        $image = $this->getMock('Imagine\Image\ImageInterface');
        $image
            ->expects($this->exactly(1))
            ->method('crop')
            ->will($this->returnSelf())
        ;
        $image
            ->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValue('binary'))
        ;
        
        $tempFilesystem = $this->getTemporaryFilesystemMock();
        
        $imagine = $this->getMock('Imagine\Image\ImagineInterface');
        $imagine
            ->expects($this->exactly(1))
            ->method('load')
            ->will($this->returnValue($image))
        ;
        
        $imageManager = $this->getImageManager($tempFilesystem);
        $imageManager->setImagine($imagine);
        
        $result = $imageManager->crop('image.jpg', array('w' => 100, 'h' => 100, 'x' => 100, 'y' => 100));
        $this->assertTrue($result);
    }
    
    public function testCropWithInvalidOptions()
    {
        $image = $this->getMock('Imagine\Image\ImageInterface');
        $image
            ->expects($this->exactly(1))
            ->method('crop')
            ->will($this->throwException(new OutOfBoundsException()))
        ;
        
        $tempFilesystem = $this->getTemporaryFilesystemMock();
        
        $imagine = $this->getMock('Imagine\Image\ImagineInterface');
        $imagine
            ->expects($this->exactly(1))
            ->method('load')
            ->will($this->returnValue($image))
        ;
        
        $imageManager = $this->getImageManager($tempFilesystem);
        $imageManager->setImagine($imagine);
        
        $result = $imageManager->crop('image.jpg', array('w' => 100, 'h' => 100, 'x' => 100, 'y' => 100));
        
        $this->assertFalse($result);
    }
    
    public function testRotate()
    {
        $image = $this->getMock('Imagine\Image\ImageInterface');
        $image
            ->expects($this->exactly(1))
            ->method('rotate')
            ->will($this->returnSelf())
        ;
        
        $image
            ->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValue('binary'))
        ;
        
        $tempFilesystem = $this->getTemporaryFilesystemMock();
        
        $imagine = $this->getMock('Imagine\Image\ImagineInterface');
        $imagine
            ->expects($this->exactly(1))
            ->method('load')
            ->will($this->returnValue($image))
        ;
        
        $imageManager = $this->getImageManager($tempFilesystem);
        $imageManager->setImagine($imagine);
        
        $result = $imageManager->rotate('image.jpg');
        
        $this->assertTrue($result);
    }
    
    public function testReset()
    {
        $tempFilesystem = $this->getTemporaryFilesystemMock();
        $imageManager = $this->getImageManager($tempFilesystem);

        $result = $imageManager->reset('image.jpg');

        $this->assertTrue($result);
    }
    
    protected function getImageManager($tempFilesystem = null, $mediaFilesystem = null)
    {
        
        $fileSystemMapMock = $this
            ->getMockBuilder('Knp\Bundle\GaufretteBundle\FilesystemMap')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $fileSystemMapMock
            ->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue($tempFilesystem))
        ;
        
        $fileSystemMapMock
            ->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue($mediaFilesystem))
        ;

        
        $imageManager = new \ReflectionClass('Thrace\MediaBundle\Manager\ImageManager');
  
        return $imageManager->newInstance($fileSystemMapMock, 'temp', 'media');        
    }
    
    protected function getTemporaryFilesystemMock()
    {
        $mock = $this
            ->getMockBuilder('Gaufrette\Filesystem')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        return $mock;
    }
    
    protected function getPermanentFilesystemMock()
    {
        $mock = $this
            ->getMockBuilder('Gaufrette\Filesystem')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        return $mock;
    }
    
    public function getImagine()
    {
        $mock = $his->getMock('Imagine\Image\ImagineInterface');
        
        return $mock;
    }
}