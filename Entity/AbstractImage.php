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

use Thrace\MediaBundle\Model\ImageInterface;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
abstract class AbstractImage extends AbstractBaseFile implements ImageInterface
{
    /**
     * {@inheritDoc}
     */
    public function getImagePath()
    {
        return $this->getUploadDir() . DIRECTORY_SEPARATOR . $this->getName();
    }
}