<?php
namespace Thrace\MediaBundle\Tests\Manager;

use Thrace\MediaBundle\Manager\FileManager;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

class FileManagerTest extends BaseTestCase
{
    public function testGetExtension()
    {
        $extension = $this->getFileManager()->getExtension('path_to_file.file.txt');
        $this->assertSame('txt', $extension);
    }
    
    public function testChecksumTemporaryFileByName()
    {
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('checksum')
            ->will(($this->returnValue('hash')))
        ;
        
        $hash = $this->getFileManager($tempFileSystem)->checksumTemporaryFileByName('file.txt');
        $this->assertSame('hash', $hash);
    }
    
    public function testChecksumTemporaryFileByNameWithInvalidFilename()
    {
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('checksum')
            ->will($this->throwException(new \Gaufrette\Exception\FileNotFound('file.txt')))
        ;
        
        $hash = $this->getFileManager($tempFileSystem)->checksumTemporaryFileByName('file.txt');
        $this->assertNull($hash);
    }
    
    public function testSaveToTemporaryDirectory()
    {
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('write')
        ;
        
        $this->getFileManager($tempFileSystem)->saveToTemporaryDirectory('file.txt', 'content');
    }
    
    public function testClearCache()
    {
        $adapter = $this->getMock('Gaufrette\Adapter');
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('keys')
            ->will($this->returnValue(array('file1.txt', 'file2.txt')))
        ;
        
        $tempFileSystem
            ->expects($this->exactly(2))
            ->method('getAdapter')
            ->will($this->returnValue($adapter))
        ;
        
        $tempFileSystem
            ->expects($this->exactly(2))
            ->method('mtime')
            ->will($this->returnValue(100))
        ;
        
        $tempFileSystem
            ->expects($this->exactly(2))
            ->method('delete')
        ;
        
        $this->getFileManager($tempFileSystem)->clearCache(123);
    }
    
    public function testGetTemporaryFile()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('get')
        ;
        
        $this->getFileManager($tempFileSystem)->getTemporaryFile($objectMock);        
    }
    
    public function testGetTemporaryFileBlobByName()
    {
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('read')
        ;
        
        $this->getFileManager($tempFileSystem)->getTemporaryFileBlobByName('file.txt');        
    }
    
    public function testGetPermenentFileBlobByName()
    {
        $mediaFileSystem = $this->getPermanentFilesystemMock();
        
        $mediaFileSystem
            ->expects($this->exactly(1))
            ->method('read')
        ;
        
        $this->getFileManager(null, $mediaFileSystem)->getPermanentFileBlobByName('file.txt');        
    }
    
    public function testCopyFileToTemporaryDirectory()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('checksum')
            ->will($this->returnValue(null))
        ;
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('write')
        ;
        
        $mediaFileSystem = $this->getPermanentFilesystemMock();
        
        $mediaFileSystem
            ->expects($this->exactly(1))
            ->method('read')
        ;
        
        $this->getFileManager($tempFileSystem, $mediaFileSystem)->copyFileToTemporaryDirectory($objectMock);
    }
    
    public function testCopyFileToTemporaryDirectoryWithExistingFile()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('checksum')
            ->will($this->returnValue('hash'))
        ;
        
        $tempFileSystem
            ->expects($this->exactly(0))
            ->method('write')
        ;
        
        $mediaFileSystem = $this->getPermanentFilesystemMock();
        
        $mediaFileSystem
            ->expects($this->exactly(0))
            ->method('read')
        ;
        
        $this->getFileManager($tempFileSystem, $mediaFileSystem)->copyFileToTemporaryDirectory($objectMock);
    }
    
    public function testCopyFileToPermanentDirectory()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
        $tempFileSystem = $this->getTemporaryFilesystemMock();

        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('read')
        ;
        
        $mediaFileSystem = $this->getPermanentFilesystemMock();
        
        $mediaFileSystem
            ->expects($this->exactly(1))
            ->method('write')
        ;
        
        $this->getFileManager($tempFileSystem, $mediaFileSystem)->copyFileToPermanentDirectory($objectMock);
    }
    
    public function testRemoveFileFromTemporaryDirectory()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('delete')
        ;
        
        $this->getFileManager($tempFileSystem)->removeFileFromTemporaryDirectory($objectMock);
    }
    
    public function testRemoveFileFromTemporaryDirectoryWithInvalidFile()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
        $tempFileSystem = $this->getTemporaryFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('delete')
            ->will($this->throwException(new \Gaufrette\Exception\FileNotFound('file.txt')))
        ;
        
        $this->getFileManager($tempFileSystem)->removeFileFromTemporaryDirectory($objectMock);
    }
    
    public function testRemoveFileFromPermanentDirectory()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
        $mediaFileSystem = $this->getPermanentFilesystemMock();
        
        $mediaFileSystem
            ->expects($this->exactly(1))
            ->method('delete')
        ;
        
        $this->getFileManager(null, $mediaFileSystem)->removeFileFromPermanentDirectory($objectMock);
    }
    
    public function testRemoveAllFiles()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
        $tempFileSystem = $this->getPermanentFilesystemMock();
        
        $tempFileSystem
            ->expects($this->exactly(1))
            ->method('delete')
        ;
        
        $mediaFileSystem = $this->getPermanentFilesystemMock();
        
        $mediaFileSystem
            ->expects($this->exactly(1))
            ->method('delete')
        ;
        
        $this->getFileManager($tempFileSystem, $mediaFileSystem)->removeAllFiles($objectMock);
    }
    
    public function testRemoveFileFromPermanentDirectoryWithInvalidFile()
    {
        $objectMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
        $mediaFileSystem = $this->getPermanentFilesystemMock();
        
        $mediaFileSystem
            ->expects($this->exactly(1))
            ->method('delete')
            ->will($this->throwException(new \Gaufrette\Exception\FileNotFound('file.txt')))
        ;
        
        $this->getFileManager(null, $mediaFileSystem)->removeFileFromPermanentDirectory($objectMock);
    }
    
    protected function getFileManager($tempFilesystem = null, $mediaFilesystem = null)
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

        
        $fileManager = new \ReflectionClass('Thrace\MediaBundle\Manager\FileManager');
  
        return $fileManager->newInstance($fileSystemMapMock, 'temp', 'media');        
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
}