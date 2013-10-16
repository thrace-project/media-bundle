<?php
namespace Thrace\MediaBundle\Tests\Form\Type;

use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;

use Thrace\MediaBundle\Form\Type\MultiImageUploadType;

use Thrace\MediaBundle\Form\Type\MultiImageUploadCollectionType;

use Thrace\MediaBundle\Tests\Form\Extension\TypeExtensionTest;

class MultiImageUploadCollectionTypeTest extends TypeTestCase
{
    public function testInvalidConfigs()
    {
        $this->setExpectedException('InvalidArgumentException');
    	$form = $this->factory->create('thrace_multi_image_upload_collection');
    }

    public function testDefaultConfigs()
    {
        $form = $this->factory->create('thrace_multi_image_upload_collection', null, array(
            'configs' => array('minWidth' => 300, 'minHeight' => 100, 'extensions' => 'pdf')
        ));
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
            'enabled_value' => false,
            'id' => 'thrace_multi_image_upload_collection',
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
				    new MultiImageUploadCollectionType(
			            $this->createSessionMock(), $this->createRouterMock(),
			            $this->createMockFormEventSubscriber(), 
			            $options
		            ),
				    new MultiImageUploadType()
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
            ->getMock();
    
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
        $this->getMockBuilder('Thrace\MediaBundle\Form\EventSubscriber\MultiImageUploadSubscriber')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    
        $mock::staticExpects($this->any())
            ->method('getSubscribedEvents')
            ->will($this->returnValue(array()))
        ;
    
        return $mock;
    }
}