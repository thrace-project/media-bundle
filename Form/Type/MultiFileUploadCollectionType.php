<?php
/*
 * This file is part of ThraceBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\MediaBundle\Form\Type;

use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\FormView;

use Symfony\Component\OptionsResolver\Options;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Symfony\Component\Form\FormInterface;

use Symfony\Component\Form\AbstractType;

/**
 * This class creates multi file upload collection element
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class MultiFileUploadCollectionType extends AbstractType
{

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * @var \Symfony\Component\EventDispatcher\EventSubscriberInterface
     */
    protected $subscriber;

    /**
     * @var array
     */
    protected $options;

    /**
     * Construct
     * 
     * @param Session $session
     * @param Router $router
     * @param EventSubscriberInterface $subscriber
     * @param array $options
     */
    public function __construct(Session $session, Router $router, EventSubscriberInterface $subscriber, array $options)
    {
        $this->session = $session;
        $this->router = $router;
        $this->subscriber = $subscriber;
        $this->options = $options;
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->subscriber->setTypeOptions($options['options']);
        $builder->addEventSubscriber($this->subscriber);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::finishView()
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $options['configs']['id'] = $view->vars['id'];
        $this->session->set($view->vars['id'], $options['configs']);
        $view->vars['configs'] = $options['configs'];
    }
    
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {  
        $defaultOptions = $this->options;
    
        $defaultConfigs = array(
            'maxSize' => $this->options['max_upload_size'],
            'enabled_button' => true,
            'meta_button' => true,
        );
    
        $router = $this->router;
    
        $resolver->setDefaults(array(
            'allow_add' => true,
            'allow_delete' => true,
            'prototype'    => true,
            'error_bubbling' => false,
            'translation_domain' => 'ThraceMediaBundle',
            'configs' => $defaultConfigs,
        ));
    
        $resolver->setNormalizers(array(
            'type' => function (Options $options, $value) use ($defaultOptions, $router){
                return 'thrace_media_multi_file_upload';
            },
            'configs' => function (Options $options, $value) use ($defaultOptions, $defaultConfigs, $router){
                $configs = array_replace_recursive($defaultOptions, $defaultConfigs, $value);

                $requiredConfigs = array('maxSize', 'extensions');

                if (count(array_diff($requiredConfigs, array_keys($configs))) > 0){
                    throw new \InvalidArgumentException(sprintf('Some of the configs "%s" are missing', json_encode($requiredConfigs)));
                }

                $configs['upload_url'] = $router->generate('thrace_media_file_upload');
                $configs['enabled_value'] = false;

                return $configs;
            }
        ));
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.AbstractType::getParent()
     */
    public function getParent()
    {
        return 'collection';
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'thrace_media_multi_file_upload_collection';
    }

}