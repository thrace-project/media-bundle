<?php
/*
 * This file is part of ThraceMediaBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\MediaBundle\Form\EventSubscriber;

use Thrace\MediaBundle\Manager\FileManagerInterface;

use Symfony\Component\Form\FormEvent;

use Thrace\MediaBundle\Model\FileInterface;

use Symfony\Component\Form\FormEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Thrace file upload form event subscriber
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class FileUploadSubscriber implements EventSubscriberInterface
{

    protected $fileManager;
    
    public function __construct(FileManagerInterface $fileManager)
    {
        $this->fileManager = $fileManager;
    }
    
    /**
     * Copy media to temporary directory
     *
     * @param FormEvent $event
     * @return void
     */
    public function preSetData(FormEvent $event)
    {
        $entity = $event->getData();
    
        if ($entity instanceof FileInterface && null !== $entity->getId()){
            try {
                $this->fileManager->copyFileToTemporaryDirectory($entity);
            } catch (\Gaufrette\Exception\FileNotFound $e) {
                $event->setData(null);
            }
        }
    }
    
    /**
     * Form event - removes file if scheduled.
     * 
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        $entity = $event->getData();
        
        if ($entity instanceof FileInterface && null === $entity->getId() && null === $entity->getHash()){
            $event->setData(null);
        }
    }

    /**
     * Register callbacks
     */
    static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::SUBMIT => 'submit',
        );
    }
}