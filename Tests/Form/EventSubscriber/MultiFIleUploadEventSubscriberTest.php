<?php
namespace Thrace\MediaBundle\Tests\Form\EventSubscriber;

use Thrace\MediaBundle\Tests\Fixture\Entity\Project;

use Thrace\MediaBundle\Tests\Fixture\Entity\MultiFile;

use Thrace\MediaBundle\Test\Tool\BaseTestCaseORM;

use Thrace\MediaBundle\Form\EventSubscriber\MultiFileUploadSubscriber;

use Symfony\Component\Form\FormEvents;

class MultiFileUploadEventSubscriberTest extends \Thrace\ComponentBundle\Test\Tool\BaseTestCaseORM
{
    
    const FIXTURE_PROJECT = 'Thrace\MediaBundle\Tests\Fixture\Entity\Project';
    
    const FIXTURE_MULTI_FILE = 'Thrace\MediaBundle\Tests\Fixture\Entity\MultiFile';
    
    protected function setUp()
    {
        $this->createMockEntityManager();
    }
    
    public function testGetSubscribedEvents()
    {

        $this->assertSame(
            array(
                FormEvents::PRE_SUBMIT => 'preSubmit', 
                FormEvents::POST_SET_DATA => 'postSetData', 
                FormEvents::POST_SUBMIT => 'postSubmit'
            ), 
            MultiFileUploadSubscriber::getSubscribedEvents()
        );
    }
    
    public function testPreSubmitEvent()
    {
        $formFactory = $this->createFormFactoryMock();
        $formFactory
            ->expects($this->exactly(3))
            ->method('createNamed')
        ;
        
        $subscriber = new MultiFileUploadSubscriber($this->em, $this->createFileManagerMock(), $formFactory);
        $subscriber->setTypeOptions(array());
        
        $subscriber->preSubmit($this->createPreSubmitFormEventMock());
    }
    
    public function testPreSubmitEventWithInvalidData()
    {
        $this->setExpectedException('Symfony\Component\Form\Exception\UnexpectedTypeException');
        
        $subscriber = new MultiFileUploadSubscriber($this->em, $this->createFileManagerMock(), $this->createFormFactoryMock());
        
        $formEvent = $this
            ->getMockBuilder('Symfony\Component\Form\FormEvent')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $formEvent
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue('string'))
        ;
        
        $subscriber->preSubmit($formEvent);
        
    }
    
    public function testPreSubmitEventWithNullData()
    {
        $form = $this
            ->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $form
            ->expects($this->exactly(1))
            ->method('all')
            ->will($this->returnValue(array()))
        ;
        
        $subscriber = new MultiFileUploadSubscriber($this->em, $this->createFileManagerMock(), $this->createFormFactoryMock());
        
        $formEvent = $this
            ->getMockBuilder('Symfony\Component\Form\FormEvent')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $formEvent
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(null))
        ;
        
        $formEvent
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form))
        ;
        
        $subscriber->preSubmit($formEvent);
        
    }
    
    public function testPostSubmitEvent()
    {
        $this->populate();
        
        $collection = $this->em->find(self::FIXTURE_PROJECT, 1)->getFiles();
        $collection->remove(1);
        $collection->remove(2);
        
        $subscriber = new MultiFileUploadSubscriber($this->em, $this->createFileManagerMock(), $this->createFormFactoryMock());
        
        $subscriber->postSubmit($this->createFormEventMock($collection));
        
        $this->assertCount(2, $this->em->getUnitOfWork()->getScheduledEntityDeletions());
        
    }
    
    public function testPostSetData()
    {
        $this->populate();
        
        $collection = $this->em->find(self::FIXTURE_PROJECT, 1)->getFiles();
        
        $fileManager = $this->createFileManagerMock();
        $fileManager
            ->expects($this->exactly(4))
            ->method('copyFileToTemporaryDirectory')
        ;
        
        $subscriber = new MultiFileUploadSubscriber($this->em, $fileManager, $this->createFormFactoryMock());
        
        $subscriber->postSetData($this->createFormEventMock($collection));
    }
    
    protected function createFormEventMock($collection)
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
            ->will($this->returnValue($collection))
        ;

        return $mock;
    }
    
    protected function createPreSubmitFormEventMock()
    {
        $form = $this
            ->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $form
            ->expects($this->exactly(1))
            ->method('all')
            ->will($this->returnValue(array(
                'name1' => 'value1',
                'name2' => 'value2',
                'name3' => 'value3',
            )))
        ;
        
        $form
            ->expects($this->exactly(3))
            ->method('has')
        ;
        
        $form
            ->expects($this->exactly(3))
            ->method('add')
        ;

        $data = array(
            'name_1' => array(
                'value' => 'test_1',
                'position' => 1        
            ),
            'name_2' => array(
                'value' => 'test_2',
                'position' => 2    
            ),
            'name_3' => array(
                'value' => 'test_3',
                'position' => 2
            ),
        );

        $mock = 
            $this
                ->getMockBuilder('Symfony\Component\Form\FormEvent')
                ->disableOriginalConstructor()
                ->getMock()
            ;
        
        $mock
            ->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data))
        ;
        
        $mock
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form))
        ;

        return $mock;
    }
    
    protected function createFileManagerMock()
    {
        return $this->getMock('Thrace\MediaBundle\Manager\FileManagerInterface');
    }
    
    protected function createFormFactoryMock()
    {
        return $this->getMock('Symfony\Component\Form\FormFactoryInterface');
    }
    
    protected function populate()
    {
        $project = new Project();
        $project->setTitle('project');
        $this->em->persist($project);
        
        for ($i = 1; $i < 5; $i++){
            $multiFile = new MultiFile();
            $multiFile->setName('name' . $i);
            $multiFile->setOriginalName('original_name' . $i);
            $multiFile->setHash(md5($i));
            $multiFile->setPosition($i);
            $this->em->persist($multiFile);
            $project->addFile($multiFile);
        }
        
        
        $this->em->flush();
        $this->em->clear();
    }
    
    protected function getUsedEntityFixtures()
    {
        return array(self::FIXTURE_PROJECT, self::FIXTURE_MULTI_FILE);
    }
}