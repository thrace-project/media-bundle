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

use Symfony\Component\Form\FormFactoryInterface;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\Common\Collections\Collection;

use Symfony\Component\Form\Exception\UnexpectedTypeException;

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
    
    protected $formFactory;
    
    protected $typeOptions = array();

    /**
     * Construct
     * 
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, FileManagerInterface $fileManager, FormFactoryInterface $formFactory)
    {
        $this->om = $om;
        $this->fileManager = $fileManager;
        $this->formFactory = $formFactory;
    }
    
    public function setTypeOptions(array $typeOptions)
    {
        $this->typeOptions = $typeOptions;
    }
    
    /**
     * Reorder collection
     * 
     * @param FormEvent $event
     * @throws UnexpectedTypeException
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
    
        if (null === $data) {
            $data = array();
        }
    
        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new UnexpectedTypeException($data, 'array or \Traversable');
        }
       
        foreach ($form as $name => $child) {
            $form->remove($name);
        }
    
        uasort($data, function($a, $b){
            if ($a['position'] == $b['position']) {
                return 0;
            }
            return ($a['position'] < $b['position']) ? -1 : 1;
        });
        
        foreach ($data as $name => $value) {
            if (!$form->has($name)) {
                $options = array_merge($this->typeOptions, array(
                    'property_path' => '[' . $name . ']',
                ));
        
                $form->add($this->formFactory->createNamed($name, 'thrace_multi_file_upload', null, $options));
            }
        }
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
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::POST_SUBMIT => 'postSubmit',                
        );
    }
}