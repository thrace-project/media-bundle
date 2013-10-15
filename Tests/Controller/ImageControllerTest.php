<?php
namespace Thrace\MediaBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\HeaderBag;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

use Symfony\Component\DependencyInjection\Container;

use Thrace\MediaBundle\Controller\ImageController;

use org\bovigo\vfs\vfsStream;

class ImageControllerTest extends BaseTestCase
{    
    private $root;
    
    public function setUp()
    {
        $this->root = vfsStream::setup('application');
    }
    
    public function testInvalidRequest()
    {
        $controller = new ImageController();
        $controller->setContainer($this->getContainerInvalidRequest());
        
        $response = $controller->uploadAction();
        
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertRegExp('/translated/', $response->getContent());
    }
    
    public function testUploadAction()
    {
        vfsStream::newFile('tmp/test.jpg')->at($this->root);
        $controller = new ImageController();
        $controller->setContainer($this->getContainer());

        $response = $controller->uploadAction();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertRegExp('/"success":true/', $response->getContent());
    }
    
    public function testUploadActionInvalidConfigs()
    {
        $controller = new ImageController();
        $controller->setContainer($this->getContainerInvalidConfigs());
    
        $this->setExpectedException('InvalidArgumentException');
        $controller->uploadAction();
    }
    
    public function testUploadActionInvalidFile()
    {
        $controller = new ImageController();
        $controller->setContainer($this->getContainerInvalidFile());
        $response = $controller->uploadAction();
    
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertRegExp('/"success":false/', $response->getContent());
        $this->assertRegExp('/"key":"translated_message"/', $response->getContent());
    }
    
    public function testRenderTemporaryAction()
    {
        $controller = new ImageController();
        $controller->setContainer($this->getContainerRenderTemporaryAction());
        $response = $controller->renderTemporaryAction();
    
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame('binary', $response->getContent());
    }
    
    public function testRenderAction()
    {
        $controller = new ImageController();
        $controller->setContainer($this->getContainerRenderAction());
        $response = $controller->renderAction();
    
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame('binary', $response->getContent());
    
        $controller->setContainer($this->getContainerRenderActionCached());
        $response = $controller->renderAction();
    }
    
    public function testInvalidAjaxRequest()
    {
        $this->setExpectedException('LogicException');
        
        $container = new Container();
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $requestMock
            ->expects($this->exactly(1))
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(false))
        ;
        
        $container->set('request', $requestMock);
        
        $controller = new ImageController();
        $controller->setContainer($container);
        
        $reflectionClass = new \ReflectionClass($controller);
        $validateRequest = $reflectionClass->getMethod('validateRequest');
        $validateRequest->setAccessible(true);
        $validateRequest->invoke($controller);
    }
    
    public function testCropAction()
    {
        $controller = new ImageController();
        $controller->setContainer($this->getContainerCropAction());
        
        $response = $controller->cropAction();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertRegExp('/"success":true/', $response->getContent());
    }
    
    public function testCropActionWithInvalidImage()
    {
        $controller = new ImageController();
        $controller->setContainer($this->getContainerCropActionWithInvalidImagePath());
    
        $response = $controller->cropAction();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertRegExp('/"success":false/', $response->getContent());
    }
    
    public function testRotateAction()
    {
        $controller = new ImageController();
        $controller->setContainer($this->getContainerRotateAction());
    
        $response = $controller->rotateAction();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertRegExp('/"success":true/', $response->getContent());
    }
    
    public function testResetAction()
    {
        $controller = new ImageController();
        $controller->setContainer($this->getContainerResetAction());
    
        $response = $controller->resetAction();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertRegExp('/"success":true/', $response->getContent());
    }
    
    private function getContainerInvalidRequest()
    {    

        $fileBagMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\FileBag')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $fileBagMock
            ->expects($this->once())
            ->method('get')
            ->with('file')
            ->will($this->returnValue(null))
        ;
        
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
         
        $requestMock->files = $fileBagMock;
        
        $translatorMock = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $translatorMock
            ->expects($this->once())
            ->method('trans')
            ->will($this->returnValue('translated'))
        ;
         
        $container = new Container();
        $container->set('request', $requestMock);     
        $container->set('translator', $translatorMock);
           
        return $container;
    }
    
    private function getContainer()
    {    
        
        $uploadedFileMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $uploadedFileMock
            ->expects($this->once())
            ->method('getRealPath')
            ->will($this->returnValue(vfsStream::url('application/tmp/test.jpg')))
        ;
        
        $uploadedFileMock
            ->expects($this->once())
            ->method('guessExtension')
            ->will($this->returnValue('jpg'))
        ;
        
        $fileBagMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\FileBag')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $fileBagMock
            ->expects($this->once())
            ->method('get')
            ->with('file')
            ->will($this->returnValue($uploadedFileMock))
        ;
        
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
         
        $requestMock->files = $fileBagMock;
         
        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
        $managerMock
            ->expects($this->exactly(1))
            ->method('normalizeImage')
            ->will($this->returnValue('image_normalized_binary'))
        ;
        
        $managerMock
            ->expects($this->exactly(1))
            ->method('saveToTemporaryDirectory')
            ->with($this->anything(), $this->equalTo('image_normalized_binary'))
        ;
        
        $managerMock
            ->expects($this->exactly(1))
            ->method('makeImageCopyToOriginalDirectory')
        ;
        
        $managerMock
            ->expects($this->exactly(1))
            ->method('checksumTemporaryFileByName')
            ->will($this->returnValue('hash'))
        ;
        
        $sessionMock = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        
        $sessionMock
            ->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValue(array(
                'max_upload_size' => '2M',
                'extensions' => 'jpg,jpeg,png',
                'minWidth' => 300,
                'minHeight' => 100
            )))
        ;
            
        $validatorMock = $this->getMock('Symfony\Component\Validator\ValidatorInterface');
        $validatorMock
            ->expects($this->once())
            ->method('validateValue')
            ->will($this->returnValue(array()))
        ;
        
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.imagemanager', $managerMock);
        $container->set('session', $sessionMock);
        $container->set('validator', $validatorMock);
        $container->setParameter('thrace_media.plupload.options', array(
            'normalize_width' => 1000,
            'normalize_height' => 1000        
        ));
        
        return $container;
    }
    
    private function getContainerInvalidConfigs()
    {    
        
        $uploadedFileMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $uploadedFileMock
            ->expects($this->once())
            ->method('guessExtension')
            ->will($this->returnValue('jpg'))
        ;
        
        $fileBagMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\FileBag')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $fileBagMock
            ->expects($this->once())
            ->method('get')
            ->with('file')
            ->will($this->returnValue($uploadedFileMock))
        ;
        
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $requestMock->files = $fileBagMock;
         
        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\FileManagerInterface');

        
        $sessionMock = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $sessionMock
            ->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValue(null))
        ;
        

        
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.imagemanager', $managerMock);
        $container->set('session', $sessionMock);
        $container->setParameter('thrace_media.plupload.options', array(
            'normalize_width' => 1000,
            'normalize_height' => 1000
        ));
        
        return $container;
    }
    
    private function getContainerInvalidFile()
    {    
        
        $uploadedFileMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $uploadedFileMock
            ->expects($this->once())
            ->method('guessExtension')
            ->will($this->returnValue('txt'))
        ;
        
        $fileBagMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\FileBag')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $fileBagMock
            ->expects($this->once())
            ->method('get')
            ->with('file')
            ->will($this->returnValue($uploadedFileMock))
        ;
        
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $requestMock->files = $fileBagMock;
          
        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\FileManagerInterface');

        
        $sessionMock = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $sessionMock
            ->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValue(array(
                'max_upload_size' => '4M',
                'extensions' => 'jpg',
                'minWidth' => 300,
                'minHeight' => 100
            )))
        ;
        
        $constraintValidationMock = $this
            ->getMockBuilder('Symfony\Component\Validator\ConstraintViolation')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $constraintValidationMock
            ->expects($this->once())
            ->method('getMessageTemplate')
            ->will($this->returnValue('message_template'))
        ;
        
        $constraintValidationMock
            ->expects($this->once())
            ->method('getMessageParameters')
            ->will($this->returnValue(array()))
        ;
        
        $validatorMock = $this->getMock('Symfony\Component\Validator\ValidatorInterface');
        $validatorMock
            ->expects($this->once())
            ->method('validateValue')
            ->will($this->returnValue(array(
                $constraintValidationMock
            )))
        ;
        
        $translatorMock = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $translatorMock
            ->expects($this->once())
            ->method('trans')
            ->will($this->returnValue(array('key' => 'translated_message')))
        ;
        
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.imagemanager', $managerMock);
        $container->set('session', $sessionMock);
        $container->set('validator', $validatorMock);
        $container->set('translator', $translatorMock);
        $container->setParameter('thrace_media.plupload.options', array(
            'normalize_width' => 1000,
            'normalize_height' => 1000
        ));
        
        return $container;
    }

    protected function getContainerRenderTemporaryAction()
    {
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $requestMock
            ->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue('test.jpg'))
        ;

        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
 
        $managerMock
            ->expects($this->exactly(1))
            ->method('getTemporaryImageBlobByName')
            ->with('test.jpg')
            ->will($this->returnValue('binary'))
        ;
        
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.imagemanager', $managerMock);
        return $container;
    }
    
    protected function getContainerRenderAction()
    {
        $headerBag = new HeaderBag();
        $headerBag->set('If-Modified-Since', true);

        
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $requestMock
            ->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue('test.jpg'))
        ;
        
        $requestMock
            ->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue('hash'))
        ;
        
        $requestMock
            ->expects($this->at(2))
            ->method('get')
            ->will($this->returnValue('image_filter'))
        ;
        
        
        $requestMock
            ->expects($this->any())
            ->method('isMethodSafe')
            ->will($this->returnValue(true))
        ;
        
        $requestMock
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('GET'))
        ;
        
        $requestMock
            ->expects($this->any())
            ->method('getEtags')
            ->will($this->returnValue(array('"cached_hash"')))
        ;
        
        $requestMock->headers = $headerBag;

        
        $imageMock = $this->getMock('Imagine\Image\ImageInterface');
        $imageMock
            ->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValue('binary'))
        ;
        
        $filterManager = $this
            ->getMockBuilder('Liip\ImagineBundle\Imagine\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $filterManager
            ->expects($this->exactly(1))
            ->method('applyFilter')
            ->with($imageMock, 'image_filter')
            ->will($this->returnValue($imageMock))
        ;
        
        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
 
        $managerMock
            ->expects($this->exactly(1))
            ->method('loadPermanentImageByName')
            ->with('test.jpg')
            ->will($this->returnValue($imageMock)) 
        ;
        
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.imagemanager', $managerMock);
        $container->set('liip_imagine.filter.manager', $filterManager);
        return $container;
    }
    
    protected function getContainerRenderActionCached()
    {
        $headerBag = new HeaderBag();
        $headerBag->set('If-Modified-Since', false);

        
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $requestMock
            ->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue('test.jpg'))
        ;
        
        $requestMock
            ->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue('cached_hash'))
        ;
        
        $requestMock
            ->expects($this->at(2))
            ->method('get')
            ->will($this->returnValue('image_filter'))
        ;
        
        $requestMock
            ->expects($this->any())
            ->method('isMethodSafe')
            ->will($this->returnValue(true))
        ;
        
        $requestMock
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('GET'))
        ;
        
        $requestMock
            ->expects($this->any())
            ->method('getEtags')
            ->will($this->returnValue(array('"'. md5('cached_hashimage_filter') .'"')))
        ;
        
        $requestMock->headers = $headerBag;

        $container = new Container();
        $container->set('request', $requestMock);

        return $container;
    }
    

    private function getContainerCropAction()
    {
    
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    
        $requestMock
            ->expects($this->at(0))
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(true))
        ;
    
        $requestMock
            ->expects($this->at(1))
            ->method('get')
            ->with('name')
            ->will($this->returnValue('image.jpg'))
        ;
         
        $requestMock
            ->expects($this->at(2))
            ->method('get')
            ->with('x')
            ->will($this->returnValue(20))
        ;
    
        $requestMock
            ->expects($this->at(3))
            ->method('get')
            ->with('y')
            ->will($this->returnValue(20))
        ;
    
        $requestMock
            ->expects($this->at(4))
            ->method('get')
            ->with('w')
            ->will($this->returnValue(20))
        ;
    
        $requestMock
            ->expects($this->at(5))
            ->method('get')
            ->with('h')
            ->will($this->returnValue(20))
        ;
    
        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
        $managerMock
            ->expects($this->exactly(1))
            ->method('crop')
            ->will($this->returnValue(true))
        ;
    
        $managerMock
            ->expects($this->exactly(1))
            ->method('checksumTemporaryFileByName')
            ->will($this->returnValue('hash'))
        ;

    
        $translatorMock = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $translatorMock
            ->expects($this->exactly(0))
            ->method('trans')
            ->will($this->returnValue(array('key' => 'translated_message')))
        ;
    
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.imagemanager', $managerMock);
        $container->set('translator', $translatorMock);
    
        return $container;
    }
    

    private function getContainerCropActionWithInvalidImagePath()
    {
    
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    
        $requestMock
            ->expects($this->at(0))
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(true))
        ;
    
        $requestMock
            ->expects($this->at(1))
            ->method('get')
            ->with('name')
            ->will($this->returnValue('image.jpg'))
        ;
         
        $requestMock
            ->expects($this->at(2))
            ->method('get')
            ->with('x')
            ->will($this->returnValue(20))
        ;
    
        $requestMock
            ->expects($this->at(3))
            ->method('get')
            ->with('y')
            ->will($this->returnValue(20))
        ;
    
        $requestMock
            ->expects($this->at(4))
            ->method('get')
            ->with('w')
            ->will($this->returnValue(20))
        ;
    
        $requestMock
            ->expects($this->at(5))
            ->method('get')
            ->with('h')
            ->will($this->returnValue(20))
        ;
    
        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
        $managerMock
            ->expects($this->exactly(1))
            ->method('crop')
            ->will($this->returnValue(false))
        ;
    
    
        $translatorMock = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $translatorMock
            ->expects($this->exactly(1))
            ->method('trans')
            ->will($this->returnValue(array('key' => 'error.image_crop')))
        ;
    
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.imagemanager', $managerMock);
        $container->set('translator', $translatorMock);
    
        return $container;
    }
    

    private function getContainerRotateAction()
    {
    
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    
        $requestMock
            ->expects($this->at(0))
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(true))
        ;
    
        $requestMock
            ->expects($this->at(1))
            ->method('get')
            ->with('name')
            ->will($this->returnValue('image.jpg'))
        ;
    
        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
    
        $managerMock
            ->expects($this->exactly(1))
            ->method('rotate')
            ->with('image.jpg')
        ;
    
        $managerMock
            ->expects($this->exactly(1))
            ->method('checksumTemporaryFileByName')
            ->with('image.jpg')
            ->will($this->returnValue('hash'))
        ;
    
        $translatorMock = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $translatorMock
            ->expects($this->exactly(0))
            ->method('trans')
            ->will($this->returnValue(array('key' => 'translated_message')))
        ;
    
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.imagemanager', $managerMock);
        $container->set('translator', $translatorMock);
    
        return $container;
    }

    private function getContainerResetAction()
    {
    
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    
        $requestMock
            ->expects($this->at(0))
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(true))
        ;
    
        $requestMock
            ->expects($this->at(1))
            ->method('get')
            ->with('name')
            ->will($this->returnValue('image.jpg'))
        ;
    
        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\ImageManagerInterface');
    
        $managerMock
            ->expects($this->exactly(1))
            ->method('reset')
            ->with('image.jpg')
        ;
    
        $managerMock
            ->expects($this->exactly(1))
            ->method('checksumTemporaryFileByName')
            ->with('image.jpg')
            ->will($this->returnValue('hash'))
        ;
    
        $translatorMock = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $translatorMock
            ->expects($this->exactly(0))
            ->method('trans')
            ->will($this->returnValue(array('key' => 'translated_message')))
        ;
    
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.imagemanager', $managerMock);
        $container->set('translator', $translatorMock);
    
        return $container;
    }
}