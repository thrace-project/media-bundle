<?php
namespace Thrace\MediaBundle\Test\Doctrine\ORM\EventSubscriber;

use Doctrine\ORM\Events;

use Thrace\MediaBundle\Tests\Fixture\Entity\MultiFile;

use Thrace\MediaBundle\Tests\Fixture\Entity\Project;

use Thrace\MediaBundle\Doctrine\ORM\EventSubscriber\FileUploadSubscriber;

use Doctrine\Common\EventManager;

use Thrace\ComponentBundle\Test\Tool\BaseTestCaseORM;

class FileUploadSubscriberTest extends BaseTestCaseORM
{
    const FIXTURE_MULTI_FILE = 'Thrace\MediaBundle\Tests\Fixture\Entity\MultiFile';

    public function testInsert()
    {
        $subscriber = new FileUploadSubscriber($this->createFileManagerMockForInsert(), true);
        $evm = new EventManager();
        $evm->addEventSubscriber($subscriber);
        $this->createMockEntityManager($evm);
        $this->populate();
        $this->assertSame(array(Events::onFlush, Events::postFlush), $subscriber->getSubscribedEvents());
    }

    public function testUpdate()
    {
        $subscriber = new FileUploadSubscriber($this->createFileManagerMockForUpdate(), true);
        $evm = new EventManager();
        $evm->addEventSubscriber($subscriber);
        $this->createMockEntityManager($evm);
        $this->populate();
        
        $file = $this->em->find(self::FIXTURE_MULTI_FILE, 1);
        $file->setName('file2');
        $file->setHash('new hash');
        
        $this->em->flush();
    }

    public function testUpdateScheduledForDeletion()
    {
        $subscriber = new FileUploadSubscriber($this->createFileManagerMockForUpdateScheduledForDeletion(), true);
        $evm = new EventManager();
        $evm->addEventSubscriber($subscriber);
        $this->createMockEntityManager($evm);
        $this->populate();
        
        $file = $this->em->find(self::FIXTURE_MULTI_FILE, 1);
        $file->setName('file2');
        $file->setHash(null);
        $file->setScheduledForDeletion(true); 
        
        $this->em->flush();
    }
    
    public function testInvalidHash()
    {
        $this->setExpectedException('Thrace\MediaBundle\Exception\FileHashChangedException');
        
        $subscriber = new FileUploadSubscriber($this->createFileManagerMockInvalidHash(), true);
        $evm = new EventManager();
        $evm->addEventSubscriber($subscriber);
        $this->createMockEntityManager($evm);
        $this->populate();
    
        $file = $this->em->find(self::FIXTURE_MULTI_FILE, 1);
        $file->setName('file2');
        $file->setHash('invalid hash');
    
        $this->em->flush();
    }
    
    protected function populate()
    {

        $multiFile = new MultiFile();
        $multiFile->setName('name');
        $multiFile->setHash('hash');
        $multiFile->setPosition(1);
        
        $this->em->persist($multiFile);
        $this->em->flush();
        $this->em->clear();
    }
    
    protected function getUsedEntityFixtures()
    {
        return array(self::FIXTURE_MULTI_FILE);
    }
    
    protected function createFileManagerMockForInsert()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Manager\FileManagerInterface');
    
        $mock
            ->expects($this->exactly(1))
            ->method('copyFileToPermanentDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('removeFileFromTemporaryDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('getTemporaryFile')
            ->will($this->returnValue($this->getMock('Thrace\MediaBundle\Model\FileInterface')))
        ;
        
        return $mock;
    }
    
    protected function createFileManagerMockForUpdate()
    {
        $fileMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');

        $mock = $this->getMock('Thrace\MediaBundle\Manager\FileManagerInterface');
    
        $mock
            ->expects($this->exactly(1))
            ->method('checksumTemporaryFileByName')
            ->will($this->returnValue('new hash'))
        ;
        
        $mock
            ->expects($this->exactly(2))
            ->method('copyFileToPermanentDirectory')
        ;
        
        $mock
            ->expects($this->exactly(2))
            ->method('removeFileFromTemporaryDirectory')
        ;
        
        $mock
            ->expects($this->exactly(2))
            ->method('getTemporaryFile')
            ->will($this->returnValue($fileMock))
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('checksumTemporaryFileByName')
            ->will($this->returnValue('new hash'))
        ;
        
        return $mock;
    }
    
    protected function createFileManagerMockForUpdateScheduledForDeletion()
    {   
        $fileMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
        $fileMock
            ->expects($this->exactly(1))
            ->method('getSize')
            ->will($this->returnValue(123))
        ;

        $mock = $this->getMock('Thrace\MediaBundle\Manager\FileManagerInterface');
    
        $mock
            ->expects($this->exactly(1))
            ->method('copyFileToPermanentDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('removeFileFromTemporaryDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('getTemporaryFile')
            ->will($this->returnValue($fileMock))
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('removeAllFiles')
        ;
        
        return $mock;
    }
    
    protected function createFileManagerMockInvalidHash()
    {   
        $fileMock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
        $fileMock
            ->expects($this->exactly(1))
            ->method('getSize')
            ->will($this->returnValue(123))
        ;

        $mock = $this->getMock('Thrace\MediaBundle\Manager\FileManagerInterface');
    
        $mock
            ->expects($this->exactly(1))
            ->method('checksumTemporaryFileByName')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('copyFileToPermanentDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('removeFileFromTemporaryDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('getTemporaryFile')
            ->will($this->returnValue($fileMock))
        ;
        
        return $mock;
    }
}