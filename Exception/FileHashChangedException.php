<?php
/*
 * This file is part of ThraceMediaBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\MediaBundle\Exception;

class FileHashChangedException extends \InvalidArgumentException implements ExceptionInterface
{
    public function __construct($name)
    {
        parent::__construct(sprintf('File with name "%s" has changed hash', $name));
    }
}