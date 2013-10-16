<?php
namespace Thrace\MediaBundle\Tests\Entity;

use Thrace\MediaBundle\Model\MediaInterface;

use Thrace\MediaBundle\Tests\Fixture\Entity\Media;

use Thrace\MediaBundle\Tests\Fixture\Entity\MultiFile;

use Thrace\ComponentBundle\Test\Tool\BaseTestCaseORM;

class MediaTest extends BaseTestCaseORM
{
    const ENTITY = 'Thrace\MediaBundle\Tests\Fixture\Entity\Media';
    
    protected function setUp()
    {
        $this->createMockEntityManager();
    }
    
    public function testDefault()
    {
        $entity = new Media();
        $entity->setName('media.flv');
        $entity->setOriginalName('media_original.flv');
        $entity->setSize(123456);
        $entity->setHash('hash');
        $entity->setTitle('title');
        $entity->setCaption('caption');
        $entity->setDescription('desc');
        $entity->setCopywrite('copywrite');
        $entity->setAuthor('author');
        $entity->setMetadata(array('key' => 'value'));
        $entity->setEnabled(true);
        $entity->setScheduledForDeletion(true);
        
        $this->assertNull($entity->getCurrentVersion());
        
        $this->em->persist($entity);
        $this->em->flush($entity);

        
        $this->assertNotNull($entity->getId());
        $this->assertSame('media.flv', $entity->getName());
        $this->assertSame('media_original.flv', $entity->getOriginalName());
        $this->assertSame(123456, $entity->getSize());
        $this->assertSame('hash', $entity->getHash());
        $this->assertSame('title', $entity->getTitle());
        $this->assertSame('caption', $entity->getCaption());
        $this->assertSame('desc', $entity->getDescription());
        $this->assertSame('copywrite', $entity->getCopywrite());
        $this->assertSame('author', $entity->getAuthor());
        $this->assertSame(array('key' => 'value'), $entity->getMetadata());
        $this->assertSame(1, $entity->getVersion());
        $this->assertTrue($entity->isEnabled());
        $this->assertTrue($entity->isScheduledForDeletion());
        $this->assertSame('/media/files/media', $entity->getUploadDir());
        $this->assertSame('/media/files/media/media.flv', $entity->getMediaPath());
        $this->assertSame(MediaInterface::TYPE_FLV, $entity->getType());
        
        $entity->setCurrentVersion(2);
        
        
    }
    
    protected function getUsedEntityFixtures()
    {
        return array(self::ENTITY);
    }
}