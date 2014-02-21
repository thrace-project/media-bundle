<?php
namespace Thrace\MediaBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\HeaderBag;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

use Symfony\Component\DependencyInjection\Container;

use Thrace\MediaBundle\Controller\FileController;

use org\bovigo\vfs\vfsStream;

class FileControllerTest extends BaseTestCase
{    
    private $root;
    
    public function setUp()
    {
        $this->root = vfsStream::setup('application');
    }
    
    public function testInvalidRequest()
    {
        $controller = new FileController();
        $controller->setContainer($this->getContainerInvalidRequest());
        
        $response = $controller->uploadAction();
        
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertRegExp('/translated/', $response->getContent());
    }
    
    public function testUploadAction()
    {
        vfsStream::newFile('tmp/test.txt')->at($this->root);
        $controller = new FileController();
        $controller->setContainer($this->getContainer());

        $response = $controller->uploadAction();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertRegExp('/"success":true/', $response->getContent());
    }
    
    public function testUploadActionInvalidConfigs()
    {
        $controller = new FileController();
        $controller->setContainer($this->getContainerInvalidConfigs());
        
        $this->setExpectedException('InvalidArgumentException');
        $controller->uploadAction();
    }
    
    public function testUploadActionInvalidFile()
    {
        $controller = new FileController();
        $controller->setContainer($this->getContainerInvalidFile());
        $response = $controller->uploadAction();
 
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertRegExp('/"success":false/', $response->getContent());
        $this->assertRegExp('/"key":"translated_message"/', $response->getContent());
    }
    
    public function testDownloadAction()
    {
        $controller = new FileController();
        $controller->setContainer($this->getContainerDownloadAction());
        $response = $controller->downloadAction();
        
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame('binary', $response->getContent());
    }
    
    public function testRenderTemporaryAction()
    {
        $controller = new FileController();
        $controller->setContainer($this->getContainerRenderTemporaryAction());
        $response = $controller->renderTemporaryAction();
        
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame('binary', $response->getContent());
    }
    
    public function testRenderAction()
    {
        $controller = new FileController();
        $controller->setContainer($this->getContainerRenderAction());
        $response = $controller->renderAction();
        
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame('binary', $response->getContent());
        
        $controller->setContainer($this->getContainerRenderActionCached());
        $response = $controller->renderAction();
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
            ->will($this->returnValue(vfsStream::url('application/tmp/test.txt')))
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
        $managerMock
            ->expects($this->exactly(1))
            ->method('saveToTemporaryDirectory')
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
                'extensions' => 'pdf,txt'   
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
        $container->set('thrace_media.filemanager', $managerMock);
        $container->set('session', $sessionMock);
        $container->set('validator', $validatorMock);
        
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
            ->will($this->returnValue(null))
        ;
        

        
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.filemanager', $managerMock);
        $container->set('session', $sessionMock);
        
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
                'extensions' => 'pdf'
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
        $container->set('thrace_media.filemanager', $managerMock);
        $container->set('session', $sessionMock);
        $container->set('validator', $validatorMock);
        $container->set('translator', $translatorMock);
        
        return $container;
    }
    
    protected function getContainerDownloadAction()
    {
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $requestMock
            ->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue('files/test.txt'))
        ;
        
        $requestMock
            ->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue('originalName.txt'))
        ;

        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\FileManagerInterface');
 
        $managerMock
            ->expects($this->exactly(1))
            ->method('getPermanentFileBlobByName')
            ->with('files/test.txt')
            ->will($this->returnValue('binary'))
        ;
        

        
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.filemanager', $managerMock);
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
            ->will($this->returnValue('test.txt'))
        ;

        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\FileManagerInterface');
 
        $managerMock
            ->expects($this->exactly(1))
            ->method('getTemporaryFileBlobByName')
            ->with('test.txt')
            ->will($this->returnValue('binary'))
        ;
        
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.filemanager', $managerMock);
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
            ->will($this->returnValue('test.txt'))
        ;
        
        $requestMock
            ->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue('hash'))
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

        $managerMock = $this->getMock('Thrace\MediaBundle\Manager\FileManagerInterface');
 
        $managerMock
            ->expects($this->exactly(1))
            ->method('getPermanentFileBlobByName')
            ->with('test.txt')
            ->will($this->returnValue('binary'))
        ;
        
        $container = new Container();
        $container->set('request', $requestMock);
        $container->set('thrace_media.filemanager', $managerMock);
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
            ->will($this->returnValue('test.txt'))
        ;
        
        $requestMock
            ->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue('cached_hash'))
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

        $container = new Container();
        $container->set('request', $requestMock);

        return $container;
    }
}