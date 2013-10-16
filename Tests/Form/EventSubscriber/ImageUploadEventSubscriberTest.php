<?php
namespace Thrace\MediaBundle\Tests\Form\EventSubscriber;

use Thrace\MediaBundle\Form\EventSubscriber\ImageUploadSubscriber;

use Symfony\Component\Form\FormEvent;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

use Symfony\Component\Form\FormEvents;

class ImageUploadEventSubscriberTest extends BaseTestCase
{
    public function testSubmitEvent()
    {
        $subscriber = new ImageUploadSubscriber($this->createImageManagerMock());
        $subscriber->submit($this->createEventMockForSubmit());
        $this->assertSame(
            array(FormEvents::POST_SET_DATA => 'postSetData', FormEvents::SUBMIT => 'submit'), 
            ImageUploadSubscriber::getSubscribedEvents()
        );
        
    }
    
    public function testPostSetDataEvent()
    {
        $imageManager = $this->createImageManagerMock();
        $imageManager
            ->expects($this->exactly(1))
            ->method('copyImagesToTemporaryDirectory')
        ;
        
        $subscriber = new ImageUploadSubscriber($imageManager);
        $subscriber->postSetData($this->createEventMockForPostSetData()); 
    }
    
    protected function createImageManagerMock()
    {
        return  $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface'); 
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
            ->will($this->returnValue($this->createMockImageForSubmit()))
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
            ->will($this->returnValue($this->createMockImageForPostSetData()))
        ;
        
        return $mock;
    }
    
    protected function createMockImageForSubmit()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
    
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
    
    protected function createMockImageForPostSetData()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
    
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1))
        ;
        
        return $mock;
    }
}