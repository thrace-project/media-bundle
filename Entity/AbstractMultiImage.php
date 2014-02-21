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

use Thrace\MediaBundle\Model\MultiImageInterface;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
abstract class AbstractMultiImage extends AbstractImage implements MultiImageInterface
{
    
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="position", length=10, nullable=false, unique=false)
     */
    protected $position = 0;
    
    /**
     * {@inheritDoc}
     */
    public function setPosition($position)
    {
        $this->position = (int) $position;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPosition()
    {
        return $this->position;
    }
}