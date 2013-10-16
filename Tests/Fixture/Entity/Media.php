<?php
namespace Thrace\MediaBundle\Tests\Fixture\Entity;

use Thrace\MediaBundle\Entity\AbstractMedia;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Media extends AbstractMedia
{
    /**
     * @var integer
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    public function getUploadDir()
    {
        return '/media/files/media';
    }
    
    public function getType()
    {
        return self::TYPE_FLV;
    }
}