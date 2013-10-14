<?php
namespace Thrace\MediaBundle\Tests\Form\Type;

use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;

use Thrace\MediaBundle\Form\Type\ImageUploadType;

use Thrace\MediaBundle\Tests\Form\Extension\TypeExtensionTest;

class ImageUploadTypeTest extends TypeTestCase
{

    public function testInvalidConfigs()
    {
        $this->setExpectedException('InvalidArgumentException');
    	$form = $this->factory->create('thrace_image_upload');
    }

    public function testDefaultConfigs()
    {
        $form = $this->factory->create('thrace_image_upload', null, array(
            'data_class' => 'Thrace\MediaBundle\Tests\Fixture\Entity\MultiImage',
            'configs' => array('minWidth' => 300, 'minHeight' => 100, 'extensions' => 'pdf')
        ));
        $form->setData($this->createMockImage());
        
        $view = $form->createView();
        $configs = $view->vars['configs'];
        $expected = array(
            'runtimes' => 'html5',
            'max_upload_size' => '4M',
            'enabled_button' => true,
            'view_button'    => true,
            'crop_button'    => true,
            'meta_button'    => true,
            'rotate_button'  => true,
            'reset_button'   => true,
            'minWidth' => 300,
            'minHeight' => 100,
            'extensions' => 'pdf',
            'upload_url' => null,
            'upload_url' => null,
            'render_url' => null,
            'crop_url' => null,
            'rotate_url' => null,
            'reset_url' => null,            
            'upload_url' => null,            
            'id' => 'thrace_image_upload',
            'name_id' => 'thrace_image_upload_name',
            'original_name_id' => 'thrace_image_upload_originalName',
            'title_id' => 'thrace_image_upload_title',
            'caption_id' => 'thrace_image_upload_caption',
            'description_id' => 'thrace_image_upload_description',
            'author_id' => 'thrace_image_upload_author',
            'copywrite_id' => 'thrace_image_upload_copywrite',
            'hash_id' => 'thrace_image_upload_hash',
            'enabled_id' => 'thrace_image_upload_enabled',
            'scheduled_for_deletion_id' => 'thrace_image_upload_scheduledForDeletion',
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

    	return array(
			new TypeExtensionTest(
				array(
				    new ImageUploadType(
				        $this->createSessionMock(),
				        $this->createRouterMock(),
				        $this->createMockFormEventSubscriber(),
				        $options
				    )
				 )
			)
    	);
    }

    protected function createSessionMock()
    {
        $session =
            $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
                ->disableOriginalConstructor()
                ->getMock();

        return $session;
    }

    protected function createRouterMock()
    {

    	$router =
    	    $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Routing\Router')
    	        ->disableOriginalConstructor()
    	        ->getMock()
        ;

    	return $router;
    }
    
    
    protected function createMockFormEventSubscriber()
    {
        $mock =
            $this->getMock('Symfony\Component\EventDispatcher\EventSubscriberInterface');

        $mock::staticExpects($this->any())
            ->method('getSubscribedEvents')
            ->will($this->returnValue(array()));

        return $mock;
    }
   
    
    
    protected function createMockImage()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Tests\Fixture\Entity\MultiImage');;

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