<?php
/**
 * This file is part of the NTDealersBundle.
 *
 * (c) Georgi Gyuroff <georgi@nt.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NT\DealersBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\Admin;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

/**
 *  Extended Admin class for Distributor
 *
 * @package \NT\DealersBundle\Entity\Distributor
 * @author  Georgi Gyuroff <georgi@nt.bg>
 */
class DealersAdmin extends Admin
{
    /**
     * Configure the list
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list list
     */
    protected function configureListFields(ListMapper $list)
    {
        $request = $this->getRequest();
        $list
            ->addIdentifier('title', null, array('label' => 'form.title'))
            ->add('publishWorkflow.isActive', null, array('label' => 'form.isActive', 'editable' => true))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        #'history' => array('template' => 'NTCoreBundle:Admin:list_action_history.html.twig'),
                    ), 'label' => 'link_actions',
                ))
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('title')
            ->end()
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $imageAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Media");
        $translationAdmin = $this->configurationPool->getAdminByAdminCode('nt.admin.dealers.translation');
        $ffds = $translationAdmin->getFormFieldDescriptions();

        $a2lixFields = array(
            'fields' => array(
                'slug' => array(
                    'field_type' => 'text',
                    'label' =>'form.slug',
                    'translation_domain' => 'NTDealersBundle',
                    'required' => false
                ),
                'title' => array(
                    'field_type' => 'text',
                    'label' =>'form.title',
                    'translation_domain' => 'NTDealersBundle',
                ),
                // 'simpleDescription' => array(
                //     'field_type' => 'textarea',
                //     'label' =>'form.simpleDescription',
                //     'required' => false,
                //     'translation_domain' => 'NTDealersBundle'
                // ),
                // 'description' => array(
                //     'field_type' => 'textarea',
                //     'label' =>'form.description',
                //     'attr' => array(
                //         'class' => 'tinymce',
                //         'data-theme' => 'bbcode',
                //     ),
                //     'required' => false,
                //     'translation_domain' => 'NTDealersBundle'
                // ),
                'pinDescription' => array(
                    'field_type' => 'textarea',
                    'label' =>'form.pin_description',
                    'attr' => array(
                        'class' => 'tinymce',
                        'data-theme' => 'bbcode',
                    ),
                    'required' => false,
                    'translation_domain' => 'NTDealersBundle'
                ),
                'image' => array(
                    'label' => 'Снимка',
                    'required' => false,
                    'field_type' => 'sonata_type_model_list',
                    'model_manager' => $this->getModelManager(),
                    'sonata_field_description' => $ffds['image'],
                    'class' => $imageAdmin->getClass(),
                    'translation_domain' => 'NTDealersBundle',
                ),
                // 'isContact' => array(
                //     'label' =>'form.isContact',
                //     'translation_domain' => 'NTDealersBundle',
                //     'required' => false,
                // ),
                // 'notInDistributors' => array(
                //     'label' =>'form.isDistributor',
                //     'translation_domain' => 'NTDealersBundle',
                //     'required' => false,
                // )
                ),
                'exclude_fields' => array('simpleDescription', 'description', 'isContact', 'notInDistributors'),
                'label' => 'form.translations',
                'translation_domain' => 'NTDealersBundle'
        );

        $a2lixFields['fields']['slug']['display'] = false;
        $a2lixFields['fields']['simpleDescription']['display'] = false;
        $a2lixFields['fields']['image']['display'] = false;

        $formMapper
                ->with('General', array(
                    'class' => 'col-md-12',
                    'label' => 'form.general',
                    'translation_domain' => 'NTDealersBundle'
                ))
                    ->add('latitude', null, array('required' => true, 'label' => 'form.latitude'))
                    ->add('longitude', null, array('required' => true, 'label' => 'form.longitude'))
                ->end()
                ->with('form.translations', array(
                        'class' => 'col-md-12',
                        'label' => 'form.translations',
                        'translation_domain' => 'NTDealersBundle'
                    )
                );
                $formMapper->add('translations', 'a2lix_translations', $a2lixFields)
                ->end()->end()
                ->with('SEO', array('tab' => true))
                    ->with('MetaData', array(
                        'collapsed' => true,
                        'class' => 'col-md-12',
                        'translation_domain' => 'NTDealersBundle',
                    ))
                        ->add('metaData', 'meta_data', array('translation_domain' => 'NTDealersBundle'))
                    ->end()
                ->end()
                ->with('Publish Workflow', array('tab' => true))
                    ->with('Publish Workflow', array(
                        'class' => 'col-md-12',
                        'label' => 'form.general',
                        'translation_domain' => 'NTDealersBundle',
                    ))
                        ->add('publishWorkflow', 'nt_publish_workflow', array(
                            'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true,
                        ))
                    ->end()
                ->end();
    }

    public function prePersist($item)
    {
        $item->setIsContact(1);
    }

    public function preUpdate($item)
    {
        $item->setIsContact(true);
    }

}
