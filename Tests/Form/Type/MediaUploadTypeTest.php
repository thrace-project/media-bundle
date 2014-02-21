<?php
namespace Thrace\MediaBundle\Tests\Form\Type;

use Thrace\MediaBundle\Tests\Form\Extension\TypeExtensionTest;

use Thrace\MediaBundle\Form\Type\MediaUploadType;

use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;

class MediaUploadTypeTest extends TypeTestCase
{
    public function testInvalidConfigs()
    {
        $this->setExpectedException('InvalidArgumentException');
    	$form = $this->factory->create('thrace_media_upload');
    }

    public function testDefaultConfigs()
    {
        $form = $this->factory->create('thrace_media_upload', null, array(
            'data_class' => 'Thrace\MediaBundle\Tests\Fixture\Entity\Media',
            'configs' => array('extensions' => 'flv')
        ));
        
        $form->setData($this->createMockFile());
        
        $view = $form->createView();
        $configs = $view->vars['configs']; 
        
        $expected = array(
            'runtimes' => 'html5',
            'max_upload_size' => '4M',  
            'key' => 'some_key',          
            'upload_url' => null,
            'enabled_button' => true,
            'meta_button' => true,
            'extensions' => 'flv',
            'id' => 'thrace_media_upload',
            'name_id' => 'thrace_media_upload_name',
            'original_name_id' => 'thrace_media_upload_originalName',
            'title_id' => 'thrace_media_upload_title',
            'caption_id' => 'thrace_media_upload_caption',
            'description_id' => 'thrace_media_upload_description',
            'author_id' => 'thrace_media_upload_author',
            'copywrite_id' => 'thrace_media_upload_copywrite',
            'hash_id' => 'thrace_media_upload_hash',
            'enabled_id' => 'thrace_media_upload_enabled',
            'scheduled_for_deletion_id' => 'thrace_media_upload_scheduledForDeletion',
            'enabled_value' => false,
        );
        $this->assertSame($expected, $configs);
    }

    protected function getExtensions()
    {
        $options = array(
            'runtimes' => 'html5',
            'max_upload_size' => '4M',
        );
        
        $mediaType = new MediaUploadType(
            $this->createSessionMock(),
            $this->createRouterMock(),
            $this->createMockFormEventSubscriber(),
            $options
        );

        $mediaType->setJwplayerOptions(array(
            'key' => 'some_key'        
        ));
        
    	return array(
			new TypeExtensionTest(
				array($mediaType)
			)
    	);
    }
    protected function createSessionMock()
    {
        $session =
            $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    
        return $session;
    }
    
    protected function createRouterMock()
    {
    
        $router =
        $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $router
            ->expects($this->any())
            ->method('generate')
            ->will($this->returnValue(null))
        ;
    
        return $router;
    }
    
    
    protected function createMockFormEventSubscriber()
    {
        $mock =
            $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')
                ->disableOriginalConstructor()
                ->getMock()
            ;
    
        $mock::staticExpects($this->any())
            ->method('getSubscribedEvents')
            ->will($this->returnValue(array()))
        ;
    
        return $mock;
    }
    
    protected function createMockFile()
    {
        $mock =
            $this->getMock('Thrace\MediaBundle\Tests\Fixture\Entity\Media');
    
        $mock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1))
        ;
    
        $mock
            ->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->returnValue(false))
        ;
    
        return $mock;
    }
}