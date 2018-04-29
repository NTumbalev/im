<?php

namespace NT\DealersBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class DealersTranslationsAdmin extends Admin
{

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->add('image', 'sonata_type_model_list', array(
            'label' => 'form.image',
            'translation_domain' => 'NTDealersBundle'
        ), array(
            'link_parameters' => array(
                'context' => 'nt_dealers_images'
            ))
        )
        ->end();
    }
}