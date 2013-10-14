<?php
namespace Thrace\MediaBundle\Tests\Form\Type;

use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;

use Thrace\MediaBundle\Form\Type\MultiFileUploadType;

use Thrace\MediaBundle\Form\Type\MultiFileUploadCollectionType;

use Thrace\MediaBundle\Tests\Form\Extension\TypeExtensionTest;

class MultiFleUploadCollectionTypeTest extends TypeTestCase
{
    public function testInvalidConfigs()
    {
        $this->setExpectedException('InvalidArgumentException');
    	$form = $this->factory->create('thrace_multi_file_upload_collection');
    }

    public function testDefaultConfigs()
    {
        $form = $this->factory->create('thrace_multi_file_upload_collection', null, array(
            'configs' => array('extensions' => 'pdf')
        ));
        $view = $form->createView();
        $configs = $view->vars['configs'];
        
        $expected = array(
            'runtimes' => 'html5',
            'max_upload_size' => '4M',
            
            'enabled_button' => true,
            'meta_button' => true,
            'extensions' => 'pdf',
            'upload_url' => null,
            'enabled_value' => false,
            'id' => 'thrace_multi_file_upload_collection',
        );
        
        $this->assertSame($expected, $configs);
    }

    protected function getExtensions()
    {
        $options = array(
            'runtimes' => 'html5',
            'max_upload_size' => '4M'
        );
        
    	return array(
			new TypeExtensionTest(
				array(
				    new MultiFileUploadCollectionType(
			            $this->createSessionMock(), $this->createRouterMock(),
			            $this->createMockFormEventSubscriber(), 
			            $options
		            ),
				    new MultiFileUploadType()
			    )
			)
    	);
    }

    protected function createSessionMock()
    {
        $session = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    
        return $session;
    }
    
    protected function createRouterMock()
    {
    
        $router = $this
            ->getMockBuilder('Symfony\Bundle\FrameworkBundle\Routing\Router')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        return $router;
    }
    
    
    protected function createMockFormEventSubscriber()
    {
        $mock = $this
            ->getMockBuilder('Thrace\MediaBundle\Form\EventSubscriber\MultiFileUploadSubscriber')
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