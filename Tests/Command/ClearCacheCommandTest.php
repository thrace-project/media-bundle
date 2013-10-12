<?php
namespace Thrace\MediaBundle\Tests\Command;

use Thrace\MediaBundle\Command\ClearCacheCommand;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\Console\Tester\CommandTester;

use Symfony\Component\Console\Application;

class ClearCacheCommandTest extends  BaseTestCase
{
    public function testExecute()
    {

        $application = new Application();
        $command = new ClearCacheCommand();
        $command->setContainer($this->createContainerMock());
        $application->add($command);
    
        $command = $application->find('thrace:media:cache-clear');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
   
    }
    
    protected function createContainerMock()
    {
        $mockFileManager = $this->getMockBuilder('Thrace\MediaBundle\Manager\FileManager')->disableOriginalConstructor()->getMock();
        $mockFileManager->expects($this->once())->method('clearCache')->with(7200);
        
        $container = new ContainerBuilder();
        $container->set('thrace_media.filemanager', $mockFileManager);
    
        return $container;
    }
}