<?php
namespace Thrace\MediaBundle\Tests\DependancyInjection;

use Thrace\MediaBundle\DependencyInjection\ThraceMediaExtension;

use Thrace\ComponentBundle\Test\Tool\BaseTestCase;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ThraceMediaBundleExtensionTest extends BaseTestCase
{
    
    public function testConfiguration()
    {
        $container = new ContainerBuilder();
        $loader = new ThraceMediaExtension();
        $loader->load($this->getConfigs(), $container);
        
        $this->assertTrue($container->hasParameter('thrace_media.temporary_filesystem_key'));
        $this->assertSame('temp_key', $container->getParameter('thrace_media.temporary_filesystem_key'));
        
        $this->assertTrue($container->hasParameter('thrace_media.media_filesystem_key'));
        $this->assertSame('media_key', $container->getParameter('thrace_media.media_filesystem_key'));
        
        $this->assertTrue($container->hasParameter('thrace_media.enable_version'));
        $this->assertFalse($container->getParameter('thrace_media.enable_version'));
        
        $this->assertTrue($container->hasParameter('thrace_media.jwplayer.options'));
        $this->assertSame(array(
            'key' => 'some_key',
            'html5player' => 'path_to_js_file',
            'flashplayer' => 'path_to_swf_file',
            'skin' => null,
            'autostart' => false,
            'width' => '600',
            'height' => '400',
            'type' => 'flv',
        ), $container->getParameter('thrace_media.jwplayer.options'));
        
        $this->assertTrue($container->hasParameter('thrace_media.plupload.options'));
        $this->assertSame(array(
            'plupload_flash_path_swf' => 'path_to_swf_file',
            'runtimes' => 'html5,flash',
            'max_upload_size' => '4M',
            'normalize_width' => 1000,
            'normalize_height' => 1000,
        ), $container->getParameter('thrace_media.plupload.options'));
    }
    
    public function testDefault ()
    {
        $container = new ContainerBuilder();
        $loader = new ThraceMediaExtension();
        $loader->load($this->getConfigs(), $container);
        
        $this->assertTrue($container->hasDefinition('thrace_media.filemanager'));
        $this->assertTrue($container->hasParameter('thrace_media.filemanager.class'));
        
        $this->assertTrue($container->hasDefinition('thrace_media.form.event_subscriber.file_upload'));
        $this->assertTrue($container->hasParameter('thrace_media.form.event_subscriber.file_upload.class'));
        
        $this->assertTrue($container->hasDefinition('thrace_media.form.type.file_upload'));
        $this->assertTrue($container->hasParameter('thrace_media.form.type.file_upload.class'));
        $this->assertTrue($container->getDefinition('thrace_media.form.type.file_upload')->hasTag('form.type'));
        
        $this->assertTrue($container->hasDefinition('thrace_media.doctrine.orm.event_subscriber.file_upload'));
        $this->assertTrue($container->hasParameter('thrace_media.doctrine.orm.event_subscriber.file_upload.class'));
        $this->assertTrue($container->getDefinition('thrace_media.doctrine.orm.event_subscriber.file_upload')->hasTag('doctrine.event_subscriber'));
        
        $this->assertTrue($container->hasDefinition('thrace_media.doctrine.orm.event_subscriber.image_upload'));
        $this->assertTrue($container->hasParameter('thrace_media.doctrine.orm.event_subscriber.image_upload.class'));
        $this->assertTrue($container->getDefinition('thrace_media.doctrine.orm.event_subscriber.image_upload')->hasTag('doctrine.event_subscriber'));
        
        $this->assertTrue($container->hasDefinition('thrace_media.imagemanager'));
        $this->assertTrue($container->hasParameter('thrace_media.imagemanager.class'));
        
        $this->assertTrue($container->hasDefinition('thrace_media.form.event_subscriber.image_upload'));
        $this->assertTrue($container->hasParameter('thrace_media.form.event_subscriber.image_upload.class'));
        
        $this->assertTrue($container->hasDefinition('thrace_media.form.type.image_upload'));
        $this->assertTrue($container->hasParameter('thrace_media.form.type.image_upload.class'));
        $this->assertTrue($container->getDefinition('thrace_media.form.type.image_upload')->hasTag('form.type'));
        
        $this->assertTrue($container->hasDefinition('thrace_media.form.type.media_upload'));
        $this->assertTrue($container->hasParameter('thrace_media.form.type.media_upload.class'));
        $this->assertTrue($container->getDefinition('thrace_media.form.type.media_upload')->hasTag('form.type'));
        
        $this->assertTrue($container->hasDefinition('thrace_media.form.type.multi_file_upload_collection'));
        $this->assertTrue($container->hasParameter('thrace_media.form.type.multi_file_upload_collection.class'));
        $this->assertTrue($container->getDefinition('thrace_media.form.type.multi_file_upload_collection')->hasTag('form.type'));
 
        $this->assertTrue($container->hasDefinition('thrace_media.form.type.multi_file_upload'));
        $this->assertTrue($container->hasParameter('thrace_media.form.type.multi_file_upload.class'));
        $this->assertTrue($container->getDefinition('thrace_media.form.type.multi_file_upload')->hasTag('form.type'));

        $this->assertTrue($container->hasDefinition('thrace_media.form.event_subscriber.multi_file_upload'));
        $this->assertTrue($container->hasParameter('thrace_media.form.event_subscriber.multi_file_upload.class'));        
        
        $this->assertTrue($container->hasDefinition('thrace_media.form.type.multi_image_upload_collection'));
        $this->assertTrue($container->hasParameter('thrace_media.form.type.multi_image_upload_collection.class'));
        $this->assertTrue($container->getDefinition('thrace_media.form.type.multi_image_upload_collection')->hasTag('form.type'));
        
        $this->assertTrue($container->hasDefinition('thrace_media.form.type.multi_image_upload'));
        $this->assertTrue($container->hasParameter('thrace_media.form.type.multi_image_upload.class'));
        $this->assertTrue($container->getDefinition('thrace_media.form.type.multi_image_upload')->hasTag('form.type'));
        
        $this->assertTrue($container->hasDefinition('thrace_media.form.event_subscriber.multi_image_upload'));
        $this->assertTrue($container->hasParameter('thrace_media.form.event_subscriber.multi_image_upload.class'));
       
        $this->assertTrue($container->hasDefinition('thrace_media.twig.extension.media'));
        $this->assertTrue($container->hasParameter('thrace_media.twig.extension.media.class'));
        $this->assertTrue($container->getDefinition('thrace_media.twig.extension.media')->hasTag('twig.extension'));
        
    }
    
    protected function getConfigs()
    {
        return array(array(
            'temporary_filesystem_key' => 'temp_key',        
            'media_filesystem_key' => 'media_key',
            'plupload' => array(
                'plupload_flash_path_swf' => 'path_to_swf_file',        
            ),
            'jwplayer' => array(
                'key' => 'some_key',
                'html5player' => 'path_to_js_file',
                'flashplayer' => 'path_to_swf_file',
            )
        ));
    }
}