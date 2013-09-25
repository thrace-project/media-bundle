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

use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
abstract class AbstractBaseFile 
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
    
    public function getId()
    {
        return $this->id;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::setName()
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::setOriginalName()
     */
    public function setOriginalName($name)
    {
        $this->originalName = (string) $name;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model.FileInterface::getOriginalName()
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::setSize()
     */
    public function setSize($size)
    {
        $this->size = (int) $size;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::getSize()
     */
    public function getSize()
    {
        return $this->size;
    }
    
    public function setMetadata(array $metadata = null)
    {
        $this->metadata = $metadata;
    }
    
    public function getMetadata()
    {
        return $this->metadata;
    }
    
    public function setAuthor($author)
    {
        $this->author = (string) $author;
    }
    
    public function getAuthor()
    {
        return $this->author;
    }
    
    public function setCopywrite($copywrite)
    {
        $this->copywrite = (string) $copywrite;
    }
    
    public function getCopywrite()
    {
        return $this->copywrite;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::setTitle()
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::getTitle()
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::setCaption()
     */
    public function setCaption($caption)
    {
        $this->caption = (string) $caption;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::getCaption()
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::setDescription()
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::getDescription()
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model.FileInterface::setHash()
     */
    public function setHash($hash)
    {
        $this->hash = (string) $hash;
    }

    /**
     * (non-PHPdoc)
     * @see Thrace\MediaBundle\Model\FileInterface::getHash()
     */
    public function getHash()
    {
        return $this->hash;
    }

    public function setCurrentVersion($currentVersion)
    {
        $this->currentVersion = (int) $currentVersion;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\MediaBundle\Model\FileInterface::getCurrentVersion()
     */
    public function getCurrentVersion()
    {
        if (null === $this->currentVersion){
            $this->currentVersion = $this->version;
        }
    
        return $this->currentVersion;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\MediaBundle\Model\FileInterface::getVersion()
     */
    public function getVersion()
    {
        return $this->version;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\MediaBundle\Model\FileInterface::setEnabled()
     */
    public function setEnabled($bool)
    {
        $this->enabled = (bool) $bool;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\MediaBundle\Model\FileInterface::isEnabled()
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\MediaBundle\Model\FileInterface::setScheduledForDeletion()
     */
    public function setScheduledForDeletion($bool)
    {
        $this->scheduledForDeletion = (bool) $bool;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Thrace\MediaBundle\Model\FileInterface::isScheduledForDeletion()
     */
    public function isScheduledForDeletion()
    {
        return $this->scheduledForDeletion;
    }
}