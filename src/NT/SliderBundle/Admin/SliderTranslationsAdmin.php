<?php

namespace NT\SliderBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class SliderTranslationsAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->add('image', 'sonata_type_model_list', array(
            'label' => 'form.image',
            'translation_domain' => 'NTSliderBundle'
        ), array(
            'link_parameters' => array(
                'context' => 'nt_slider'
            ))
        )
        ->end();
    }
}
