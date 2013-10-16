<?php
namespace Thrace\MediaBundle\Tests\Fixture\Entity;

use Thrace\MediaBundle\Entity\AbstractMultiFile;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 */
class MultiFile extends AbstractMultiFile
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
        return '/media/files/main';
    }
}