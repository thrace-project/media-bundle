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

use Thrace\MediaBundle\Model\ImageInterface;

use Symfony\Component\Form\FormEvent;

use Thrace\MediaBundle\Manager\ImageManagerInterface;

use Symfony\Component\Form\FormEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Thrace image upload form event subscriber
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class ImageUploadSubscriber implements EventSubscriberInterface
{
    
    /**
     *  @var Thrace\MediaBundle\Manager\ImageManagerInterface
     */
    protected $imageManager;

    /**
     * Construct
     * 
     * @param ImageManagerInterface $imageManager
     */
    public function __construct(ImageManagerInterface $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * Copy image to temporary directory
     *
     * @param FilterDataEvent $event
     * @return void
     */
    public function postSetData(FormEvent $event)
    {  
        $entity = $event->getData();

        if ($entity instanceof ImageInterface && null !== $entity->getId()){
            $this->imageManager->copyImagesToTemporaryDirectory($entity);
        }
    }
    
    /**
     * Form event - removes image if scheduled.
     * 
     * @param DataEvent $event
     */
    public function submit(FormEvent $event)
    {
        $entity = $event->getData();
        
        if ($entity instanceof ImageInterface && null === $entity->getId() && null === $entity->getHash()){
            $event->setData(null);
        }
    }

    /**
     * Register callbacks
     */
    static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::SUBMIT => 'submit',
        );
    }
}