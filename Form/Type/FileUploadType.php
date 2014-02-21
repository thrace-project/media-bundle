<?php
/*
 * This file is part of ThraceMediaBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\MediaBundle\Form\Type;

use Thrace\MediaBundle\Model\FileInterface;

use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\FormView;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\OptionsResolver\Options;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Symfony\Component\Form\FormInterface;

use Symfony\Component\Form\AbstractType;

/**
 * This class creates jquery file upload element
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class FileUploadType extends AbstractType
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session
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
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'hidden');
        $builder->add('originalName', 'hidden');
        $builder->add('title', 'hidden');
        $builder->add('caption', 'hidden');
        $builder->add('description', 'hidden');
        $builder->add('author', 'hidden');
        $builder->add('copywrite', 'hidden');
        $builder->add('hash', 'hidden');
        $builder->add('enabled', 'hidden');
        $builder->add('currentVersion', 'hidden');
        $builder->add('scheduledForDeletion', 'hidden', array('data' => false));
        $builder->addEventSubscriber($this->subscriber);
    }

    /**
     * {@inheritDoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $configs = array_merge($options['configs'], array(
            'id' => $view->vars['id'],        
            'name_id' => $view->children['name']->vars['id'],        
            'original_name_id' => $view->children['originalName']->vars['id'],             
            'title_id' => $view->children['title']->vars['id'],        
            'caption_id' => $view->children['caption']->vars['id'],        
            'description_id' => $view->children['description']->vars['id'],  
            'author_id' => $view->children['author']->vars['id'],
            'copywrite_id' => $view->children['copywrite']->vars['id'],                          
            'hash_id' => $view->children['hash']->vars['id'],        
            'enabled_id' => $view->children['enabled']->vars['id'],   
            'scheduled_for_deletion_id' => $view->children['scheduledForDeletion']->vars['id'],   
            'enabled_value' => false,     
        ));
       
        $file = $form->getData();
        
        if ($file instanceof FileInterface && null !== $file->getId()){            
            $configs['enabled_value'] = $file->isEnabled();
        }
        
        $this->session->set($view->vars['id'], $configs);
        $view->vars['configs'] = $configs;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $defaultOptions = $this->options;
    
        $defaultConfigs = $this->getDefaultConfigs();
    
        $router = $this->router;
    
        $resolver->setDefaults(array(
            'error_bubbling' => false,
            'translation_domain' => 'ThraceMediaBundle',
            'configs' => $defaultConfigs,
        ));
    
        $resolver->setNormalizers(array(
            'configs' => function (Options $options, $value) use ($defaultOptions, $defaultConfigs, $router){
                $configs = array_replace_recursive($defaultOptions, $defaultConfigs, $value);

                $requiredConfigs = array('extensions');

                if (count(array_diff($requiredConfigs, array_keys($configs))) > 0){
                    throw new \InvalidArgumentException(sprintf('Some of the configs "%s" are missing', json_encode($requiredConfigs)));
                }

                return $configs;
            }
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'thrace_file_upload';
    }

    /**
     * Gets default configs for the widget
     * 
     * @return array
     */
    public function getDefaultConfigs()
    {
        return array(
            'upload_url' =>$this->router->generate('thrace_media_file_upload'),
            'enabled_button' => true,
            'meta_button' => true,
        );
    }
}