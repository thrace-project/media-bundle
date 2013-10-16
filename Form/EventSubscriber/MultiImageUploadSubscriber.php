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

use Symfony\Component\Form\Exception\UnexpectedTypeException;

use Symfony\Component\Form\FormEvent;

use Thrace\MediaBundle\Model\MultiImageInterface;

use Thrace\MediaBundle\Manager\ImageManagerInterface;

use Doctrine\Common\Persistence\ObjectManager;

use Doctrine\ORM\PersistentCollection;

use Symfony\Component\Form\FormEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * MultiImage form subscriber
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class MultiImageUploadSubscriber implements EventSubscriberInterface
{
    /**
     * @var Doctrine\Common\Persistence\ObjectManager
     */
    protected $om;
    
    /**
     *  @var Thrace\MediaBundle\Manager\ImageManagerInterface
     */
    protected $imageManager;
    
    protected $formFactory;
    
    protected $typeOptions = array();

    /**
     * Construct
     * 
     * @param ObjectManager $om
     * @param ImageManagerInterface $imageManager
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(ObjectManager $om, ImageManagerInterface $imageManager, FormFactoryInterface $formFactory)
    {
        $this->om = $om;
        $this->imageManager = $imageManager;
        $this->formFactory = $formFactory;
    }
    
    public function setTypeOptions(array $typeOptions)
    {
        $this->typeOptions = $typeOptions;
    }
    
    /**
     * Copy images from permenent to temporary directory
     * 
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $collection = $event->getData();
        
        if ($collection instanceof PersistentCollection){
        
            foreach ($collection as $image){
                if ($image instanceof MultiImageInterface && null !== $image->getId()){
                    $this->imageManager->copyImagesToTemporaryDirectory($image);
                }
            }
        }
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
         
        foreach ($form->all() as $name => $child) {
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
    
                    $form->add($this->formFactory->createNamed($name, 'thrace_multi_image_upload', null, $options));
                }
            }
    }

    /**
     * Remove deleted images
     * 
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $collection = $event->getData();

        if ($collection instanceof PersistentCollection){
            foreach ($collection->getDeleteDiff() as $entity){
                if ($entity instanceof MultiImageInterface){
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
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::POST_SUBMIT => 'postSubmit',
        );
    }
}