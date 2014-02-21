<?php
namespace Thrace\MediaBundle\Test\Doctrine\ORM\EventSubscriber;

use Thrace\MediaBundle\Tests\Fixture\Entity\MultiImage;

use Thrace\MediaBundle\Doctrine\ORM\EventSubscriber\ImageUploadSubscriber;

use Doctrine\ORM\Events;

use Thrace\MediaBundle\Tests\Fixture\Entity\Project;

use Doctrine\Common\EventManager;

use Thrace\ComponentBundle\Test\Tool\BaseTestCaseORM;

class ImageUploadSubscriberTest extends BaseTestCaseORM
{
    const FIXTURE_MULTI_IMAGE = 'Thrace\MediaBundle\Tests\Fixture\Entity\MultiImage';

    public function testInsert()
    {
        $subscriber = new ImageUploadSubscriber($this->createImageManagerMockForInsert(), true);
        $evm = new EventManager();
        $evm->addEventSubscriber($subscriber);
        $this->createMockEntityManager($evm);
        $this->populate();
        $this->assertSame(array(Events::onFlush, Events::postFlush), $subscriber->getSubscribedEvents());
    }
    
    public function testUpdate()
    {
        $subscriber = new ImageUploadSubscriber($this->createImageManagerMockForUpdate(), true);
        $evm = new EventManager();
        $evm->addEventSubscriber($subscriber);
        $this->createMockEntityManager($evm);
        $this->populate();
    
        $file = $this->em->find(self::FIXTURE_MULTI_IMAGE, 1);
        $file->setName('image2.jpg');
        $file->setHash('new hash');
    
        $this->em->flush();
    }
    
    public function testUpdateWithHashChangedOnly()
    {
        $subscriber = new ImageUploadSubscriber($this->createImageManagerMockForUpdateWithHashChangedOnly(), true);
        $evm = new EventManager();
        $evm->addEventSubscriber($subscriber);
        $this->createMockEntityManager($evm);
        $this->populate();
    
        $file = $this->em->find(self::FIXTURE_MULTI_IMAGE, 1);
        $file->setName('name.jpg');
        $file->setHash('new hash');
    
        $this->em->flush();
    }
    

    public function testUpdateScheduledForDeletion()
    {
        $subscriber = new ImageUploadSubscriber($this->createImageManagerMockForUpdateScheduledForDeletion(), true);
        $evm = new EventManager();
        $evm->addEventSubscriber($subscriber);
        $this->createMockEntityManager($evm);
        $this->populate();
    
        $file = $this->em->find(self::FIXTURE_MULTI_IMAGE, 1);
        $file->setName('image2.jpg');
        $file->setHash(null);
        $file->setScheduledForDeletion(true);
    
        $this->em->flush();
    }
    
    public function testInvalidHash()
    {
        $this->setExpectedException('Thrace\MediaBundle\Exception\FileHashChangedException');
    
        $subscriber = new ImageUploadSubscriber($this->createImageManagerMockInvalidHash(), true);
        $evm = new EventManager();
        $evm->addEventSubscriber($subscriber);
        $this->createMockEntityManager($evm);
        $this->populate();
    
        $file = $this->em->find(self::FIXTURE_MULTI_IMAGE, 1);
        $file->setName('image2.jpg');
        $file->setHash('invalid hash');
    
        $this->em->flush();
    }
    
    protected function populate()
    {

        $object = new MultiImage();
        $object->setName('name.jpg');
        $object->setHash('hash');
        $object->setPosition(1);
        
        $this->em->persist($object);
        $this->em->flush();
        $this->em->clear();
    }
    
    protected function getUsedEntityFixtures()
    {
        return array(self::FIXTURE_MULTI_IMAGE);
    }
    
    protected function createImageManagerMockForInsert()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
        
        $imageMock = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        $imageMock
            ->expects($this->exactly(1))
            ->method('getSize')
            ->will($this->returnValue(123))
        ;
    
        $mock
            ->expects($this->exactly(1))
            ->method('copyImagesToPermanentDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('removeImagesFromTemporaryDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('getTemporaryImage')
            ->will($this->returnValue($imageMock))
        ;
        
        return $mock;
    }
    
    protected function createImageManagerMockForUpdate()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
        
        $imageMock = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        $imageMock
            ->expects($this->exactly(2))
            ->method('getSize')
            ->will($this->returnValue(123))
        ;
    
        $mock
            ->expects($this->exactly(2))
            ->method('copyImagesToPermanentDirectory')
        ;
        
        $mock
            ->expects($this->exactly(2))
            ->method('removeImagesFromTemporaryDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('removeAllImages')
        ;
        
        $mock
            ->expects($this->exactly(2))
            ->method('getTemporaryImage')
            ->will($this->returnValue($imageMock))
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('checksumTemporaryFileByName')
            ->will($this->returnValue('new hash'))
        ;
        
        return $mock;
    }
    
    protected function createImageManagerMockForUpdateWithHashChangedOnly()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
        
        $imageMock = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        $imageMock
            ->expects($this->exactly(2))
            ->method('getSize')
            ->will($this->returnValue(123))
        ;
    
        $mock
            ->expects($this->exactly(2))
            ->method('copyImagesToPermanentDirectory')
        ;
        
        $mock
            ->expects($this->exactly(2))
            ->method('removeImagesFromTemporaryDirectory')
        ;
        
        $mock
            ->expects($this->exactly(0))
            ->method('removeAllImages')
        ;
        
        $mock
            ->expects($this->exactly(2))
            ->method('getTemporaryImage')
            ->will($this->returnValue($imageMock))
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('checksumTemporaryFileByName')
            ->will($this->returnValue('new hash'))
        ;
        
        return $mock;
    }
    
    protected function createImageManagerMockForUpdateScheduledForDeletion()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
        
        $imageMock = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        $imageMock
            ->expects($this->exactly(1))
            ->method('getSize')
            ->will($this->returnValue(123))
        ;
    
        $mock
            ->expects($this->exactly(1))
            ->method('copyImagesToPermanentDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('removeImagesFromTemporaryDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('removeAllImages')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('getTemporaryImage')
            ->will($this->returnValue($imageMock))
        ;
        
        $mock
            ->expects($this->exactly(0))
            ->method('checksumTemporaryFileByName')
            ->will($this->returnValue('new hash'))
        ;
        
        return $mock;
    }
    
    
    protected function createImageManagerMockInvalidHash()
    {   
        $imageMock = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
        $imageMock
            ->expects($this->exactly(1))
            ->method('getSize')
            ->will($this->returnValue(123))
        ;

        $mock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
    
        $mock
            ->expects($this->exactly(1))
            ->method('checksumTemporaryFileByName')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('copyImagesToPermanentDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('removeImagesFromTemporaryDirectory')
        ;
        
        $mock
            ->expects($this->exactly(1))
            ->method('getTemporaryImage')
            ->will($this->returnValue($imageMock))
        ;
        
        return $mock;
    }
}