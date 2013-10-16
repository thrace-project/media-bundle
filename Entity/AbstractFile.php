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

use Thrace\MediaBundle\Model\FileInterface;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
abstract class AbstractFile extends AbstractBaseFile implements FileInterface
{
    /**
     * {@inheritDoc}
     */
    public function getFilePath()
    {
        return $this->getUploadDir() . DIRECTORY_SEPARATOR . $this->getName();
    }
}