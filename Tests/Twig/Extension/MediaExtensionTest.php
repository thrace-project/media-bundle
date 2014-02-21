<?php
namespace Thrace\MediaBundle\Tests\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\DependencyInjection\Scope;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Thrace\MediaBundle\Twig\Extension\MediaExtension;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

class MediaExtensionTest extends BaseTestCase
{
    public function setUp()
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not available');
        }
    }
    
    public function testRenderImage()
    {
        $result = $this->getTemplate("{{ thrace_image(image,filter,options) }}")
            ->render(array(
                'image' => $this->createImageMock(),
                'filter' => 'some_filter',
                'options' => array()
            ))
        ;
            
        $this->assertSame('<p>rendered</p>', $result);
    }
    
    public function testGenerateDownloadUrl()
    {
        $result = $this->getTemplate("{{ thrace_file_download_url(file) }}")
            ->render(array(
                'file' => $this->createFileMock(),
            ))
        ;
            
        $this->assertSame('<a>generated</a>', $result);
    }
    
    public function testRenderMedia()
    {
        $result = $this->getTemplate("{{ thrace_media(media,options) }}")
            ->render(array(
                'media' => $this->createMediaMock(),
                'options' => array()
            ))
        ;
        
        $this->assertSame('<p>rendered</p>', $result);
    }
    
    protected function getTemplate($template)
    {
        $loader = new \Twig_Loader_Array(array('index' => $template));
        $twig = new \Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $twig->addExtension(new MediaExtension($this->createContainerMock()));
    
        return $twig->loadTemplate('index');
    }
    
    protected function createTemplatingMock()
    {
        $templatingMock = $this
            ->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
            ->disableOriginalConstructor()->getMock()
        ;
        
        $templatingMock
            ->expects($this->any())
            ->method('render')
            ->will($this->returnValue('<p>rendered</p>'))
        ;
        
        return $templatingMock;
    }
    
    protected function createRouterMock()
    {
        $mock = $this->getMock('Symfony\Component\Routing\RouterInterface');
        
        $mock
            ->expects($this->any())
            ->method('generate')
            ->will($this->returnValue('<a>generated</a>'))
        ;
        
        return $mock;
    }
    
    protected function createContainerMock()
    {
        $container = new ContainerBuilder();
        $container->addScope(new Scope('request'));
        $container
            ->register('request', 'Symfony\Component\HttpFoundation\Request')
            ->setScope('request')
        ;
        $container->enterScope('request');
    
        $container->set('templating', $this->createTemplatingMock());
        $container->set('router', $this->createRouterMock());
        $container->setParameter('thrace_media.jwplayer.options', array(
            'key' => '123',
            'html5player' => 'path_to_file',        
            'flashplayer' => 'path_to_file',        
        ));
        
        return $container;
    }
    
    protected function createImageMock()
    {
        $image = $this->getMock('Thrace\MediaBundle\Model\ImageInterface');
    
        $image
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('image.jpg'));
    
        $image
            ->expects($this->any())
            ->method('getUploadDir')
            ->will($this->returnValue('/image'));
    
        return $image;
    }
    
    protected function createFileMock()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Model\FileInterface');

        $mock
            ->expects($this->any())
            ->method('getOriginalName')
            ->will($this->returnValue('file.txt'));
    
        $mock
            ->expects($this->any())
            ->method('getFilePath')
            ->will($this->returnValue('/files/file.txt'));
    
        return $mock;
    }
    
    protected function createMediaMock()
    {
        $mock = $this->getMock('Thrace\MediaBundle\Model\MediaInterface');

        $mock
            ->expects($this->any())
            ->method('getMediaPath')
            ->will($this->returnValue('path_to_file/file.flv'))
        ;
        
        $mock
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('flv'))
        ;
    
    
        return $mock;
    }
}