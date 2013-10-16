<?php
namespace Thrace\MediaBundle\Tests\Fixture\Entity;

use Thrace\MediaBundle\Model\MultiImageInterface;

use Thrace\MediaBundle\Model\MultiFileInterface;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 */
class Project 
{
    /**
     * @var integer 
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * 
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string 
     * 
     * @ORM\Column(type="string", name="title", length=255, nullable=true, unique=false)
     */
    protected $title;
    
    /**
     * @ORM\ManyToMany(targetEntity="MultiFile", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC"})
     * @ORM\JoinTable(
     *   inverseJoinColumns={@ORM\JoinColumn(unique=true,  onDelete="CASCADE")}
     * )
     */
    protected $files;
    
    /**
     * @ORM\ManyToMany(targetEntity="MultiImage", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC"})
     * @ORM\JoinTable(
     *   inverseJoinColumns={@ORM\JoinColumn(unique=true,  onDelete="CASCADE")}
     * )
     */
    protected $images;
    
    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function addFile(MultiFileInterface $file)
    {
        if (!$this->files->contains($file)){
            $this->files->add($file);
        }
    }
    
    public function getFiles()
    {
        return $this->files;
    }
    
    public function removeFile(MultiFileInterface $file)
    {
        if ($this->files->contains($file)){
            $this->files->removeElement($file);
        }
    }
    
    public function addImage(MultiImageInterface $image)
    {
        if (!$this->images->contains($image)){
            $this->images->add($image);
        }
    }
    
    public function getImages()
    {
        return $this->images;
    }
    
    public function removeImage(MultiImageInterface $image)
    {
        if ($this->images->contains($image)){
            $this->images->removeElement($image);
        }
    }

}