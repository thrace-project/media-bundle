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

use Thrace\MediaBundle\Model\MultiFileInterface;

use Doctrine\Common\Persistence\ObjectManager;

use Doctrine\ORM\PersistentCollection;

use Symfony\Component\Form\FormEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * MultiFile form subscriber
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class MultiFileUploadSubscriber implements EventSubscriberInterface
{
    /**
     * @var Doctrine\Common\Persistence\ObjectManager
     */
    protected $om;
    
    protected $fileManager;

    /**
     * Construct
     * 
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, FileManagerInterface $fileManager)
    {
        $this->om = $om;
        $this->fileManager = $fileManager;
    }

    /**
     * Copy file from permenent to temporary directory
     *
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $collection = $event->getData();
    
        if ($collection instanceof PersistentCollection){
            foreach ($collection as $file){
                if ($file instanceof MultiFileInterface){
                    $this->fileManager->copyFileToTemporaryDirectory($file);
                }
            }
        }
    }
    
    /**
     * Remove deleted files
     * 
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $collection = $event->getData();

        if ($collection instanceof PersistentCollection){
            foreach ($collection->getDeleteDiff() as $entity){
                if ($entity instanceof MultiFileInterface){
                    $this->om->remove($entity);
                } 
            }
        }
    }

    /**
     * Register callbacks
     */
    static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::POST_SUBMIT => 'postSubmit',
        );
    }
}