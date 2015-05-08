<?php
/*
 * This file is part of ThraceMediaBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\MediaBundle\Entity;

use Thrace\MediaBundle\Model\BaseFileInterface;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
abstract class AbstractBaseFile implements BaseFileInterface
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=255, nullable=false, unique=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="original_name", length=255, nullable=true, unique=false)
     */
    protected $originalName;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="size", length=20, nullable=true, unique=false)
     */
    protected $size;
    
    /**
     * @var array 
     *
     * @ORM\Column(type="array", name="metadata", nullable=true)
     */
    protected $metadata;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="copywrite", length=255, nullable=true, unique=false)
     */
    protected $copywrite;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", name="author", length=255, nullable=true, unique=false)
     */
    protected $author;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="title", length=255, nullable=true, unique=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="caption", length=255, nullable=true, unique=false)
     */
    protected $caption;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="description", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="hash", length=255, nullable=false, unique=false)
     */
    protected $hash;
    
    /**
     * @var integer
     *
     * @ORM\Version @ORM\Column(type="integer")
     */
    protected $version;
    
    /**
     * This property is not mapped by Doctrine.
     * Used to store current version of the file
     *
     * @var integer
     */
    protected $currentVersion;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="enabled")
     */
    protected $enabled = false;
    
    /**
     * This property is not mapped by Doctrine.
     * Used to identify if entity is marked for deletion
     * 
     * @var boolean
     */
    protected $scheduledForDeletion = false;
    
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setOriginalName($name)
    {
        if(is_string($name) && strlen($name)){
            $this->originalName = htmlentities($name);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * {@inheritDoc}
     */
    public function setSize($size)
    {
        $this->size = (int) $size;
    }

    /**
     * {@inheritDoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritDoc}
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    public function setCaption($caption)
    {
        $this->caption = (string) $caption;
    }

    /**
     * {@inheritDoc}
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setMetadata(array $metadata = null)
    {
        $this->metadata = $metadata;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setAuthor($author)
    {
        $this->author = (string) $author;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getAuthor()
    {
        return $this->author;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setCopywrite($copywrite)
    {
        $this->copywrite = (string) $copywrite;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getCopywrite()
    {
        return $this->copywrite;
    }

    /**
     * {@inheritDoc}
     */
    public function setHash($hash)
    {
        $this->hash = (string) $hash;
    }

    /**
     * {@inheritDoc}
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrentVersion($currentVersion)
    {
        $this->currentVersion = (int) $currentVersion;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getCurrentVersion()
    {
        if (null === $this->currentVersion){
            $this->currentVersion = $this->version;
        }
    
        return $this->currentVersion;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getVersion()
    {
        return $this->version;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setEnabled($bool)
    {
        $this->enabled = (bool) $bool;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setScheduledForDeletion($bool)
    {
        $this->scheduledForDeletion = (bool) $bool;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isScheduledForDeletion()
    {
        return $this->scheduledForDeletion;
    }
}