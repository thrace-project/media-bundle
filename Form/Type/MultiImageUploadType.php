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

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * This class creates multi image upload element
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class MultiImageUploadType extends AbstractType
{
    
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'hidden', array('attr' => array('class' => 'thrace_multi_image_upload_name')));
        $builder->add('originalName', 'hidden', array('attr' => array('class' => 'thrace_multi_image_upload_originalName')));
        $builder->add('title', 'hidden', array('attr' => array('class' => 'thrace_multi_image_upload_title')));
        $builder->add('caption', 'hidden', array('attr' => array('class' => 'thrace_multi_image_upload_caption')));
        $builder->add('description', 'hidden', array('attr' => array('class' => 'thrace_multi_image_upload_description')));
        $builder->add('author', 'hidden', array('attr' => array('class' => 'thrace_multi_image_upload_author')));
        $builder->add('copywrite', 'hidden', array('attr' => array('class' => 'thrace_multi_image_upload_copywrite')));
        $builder->add('hash', 'hidden', array('attr' => array('class' => 'thrace_multi_image_upload_hash')));
        $builder->add('position', 'hidden', array('attr' => array('class' => 'thrace_multi_image_upload_position')));
        $builder->add('enabled', 'hidden', array('attr' => array('class' => 'thrace_multi_image_upload_enabled')));
        $builder->add('currentVersion', 'hidden', array('attr' => array('class' => 'thrace_multi_image_upload_currentVersion')));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'auto_initialize' => false
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'thrace_multi_image_upload';
    }
}