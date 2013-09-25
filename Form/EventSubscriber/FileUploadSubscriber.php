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
     
    /**
     * Form event - removes file if scheduled.
     * 
     * @param DataEvent $event
     */
    public function submit(FormEvent $event)
    {
        $entity = $event->getData();
        
        if ($entity instanceof FileInterface && null === $entity->getId() && null === $entity->getName()){
            $event->setData(null);
        }
    }

    /**
     * Subscription for Form Events
     */
    static function getSubscribedEvents()
    {
        return array(
            FormEvents::SUBMIT => 'submit',
        );
    }
}