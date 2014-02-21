<?php
namespace Thrace\MediaBundle\Tests\Form\EventSubscriber;

use Symfony\Component\Form\FormEvent;

use Thrace\MediaBundle\Form\EventSubscriber\FileUploadSubscriber;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

use Symfony\Component\Form\FormEvents;

class FileUploadEventSubscriberTest extends BaseTestCase
{
    public function testSubmitEvent()
    {
        $subscriber = new FileUploadSubscriber($this->createFileManagerMock());
        $subscriber->submit($this->createEventMockForSubmit());
        $this->assertSame(
            array(FormEvents::POST_SET_DATA => 'postSetData', FormEvents::SUBMIT => 'submit'), 
            FileUploadSubscriber::getSubscribedEvents()
        );
        
    }
    
    public function testPostSetDataEvent()
    {
        $fileManager = $this->createFileManagerMock();
        $fileManager
            ->expects($this->exactly(1))
            ->method('copyFileToTemporaryDirectory')
        ;
        
        $subscriber = new FileUploadSubscriber($fileManager);
        $subscriber->postSetData($this->createEventMockForPostSetData()); 
    }
    
    protected function createFileManagerMock()
    {
        return  $this->getMock('Thrace\MediaBundle\Manager\FileManagerInterface'); 
    }
    
    protected function createEventMockForSubmit()
    {
        $mock = 
            $this
                ->getMockBuilder('Symfony\Component\Form\FormEvent')
                ->disableOriginalConstructor()
                ->getMock()
            ;
        
        $mock
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($this->createMockFileForSubmit()))
        ;
        
        $mock
            ->expects($this->once())
            ->method('setData')
            ->with(null)
        ;
        
        return $mock;
    }
    
    protected function createEventMockForPostSetData()
    {
        $mock = 
            $this
                ->getMockBuilder('Symfony\Component\Form\FormEvent')
                ->disableOriginalConstructor()
                ->getMock()
            ;
        
        $mock
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($this->createMockFileForPostSetData()))
        ;
        
        return $mock;
    }
    
    protected function createMockFileForSubmit()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
    
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null))
        ;
    
        $mock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(null))
        ;
    
        return $mock;
    }
    
    protected function createMockFileForPostSetData()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');
    
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1))
        ;
        
        return $mock;
    }
}