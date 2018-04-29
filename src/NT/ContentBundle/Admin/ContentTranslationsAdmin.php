<?php

namespace NT\ContentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class ContentTranslationsAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->add('headerImage', 'sonata_type_model_list', array(
            'label' => 'form.headerImage',
            'translation_domain' => 'NTContentBundle'
        ), array(
            'link_parameters' => array(
                'context' => 'nt_content_header_image'
            ))
        )
        ->end();
    }
}
