<?php
namespace NT\NewsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class NewsCategoriesAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'rank',
    );

    /**
     * Configure the list
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('title', null, array('label' => 'form.title'))
            ->add('publishWorkflow.isActive', null, array('label' => 'form.isActive', 'editable' => true))
            ->add('createdAt', null, array('label' => 'form.createdAt'))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        #'history' => array('template' => 'NTCoreBundle:Admin:list_action_history.html.twig'),
                    ), 'label' => 'actions',
                ))
            ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('order', 'order');
    }

    /**
     * Configure the form
     *
     * @param FormMapper $formMapper formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $mediaAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Media");
        $translationAdmin = $this->configurationPool->getAdminByAdminCode('nt.news_categories.admin.news_categories_translation');
        $ffds = $translationAdmin->getFormFieldDescriptions();
        $ffds['image']->setAssociationAdmin($mediaAdmin);

        $formMapper
            ->with('General', array('tab' => true))
                ->with('General')
                    ->add('translations', 'a2lix_translations', array(
                        'fields' => array(
                            'slug' => array(
                                'field_type' => 'text',
                                'required' => false,
                                'label' => 'form.slug',
                            ),
                            'title' => array(
                                'field_type' => 'text',
                                'label' => 'form.title',
                            ),
                            'description' => array(
                                'field_type' => 'textarea',
                                'required' => false,
                                'attr' => array(
                                    'class' => 'tinymce',
                                    'data-theme' => 'bbcode',
                                ),
                                'label' => 'form.description',
                            ),
                            'image' => array(
                                'label' => 'form.image',
                                'required' => false,
                                'field_type' => 'sonata_type_model_list',
                                'model_manager' => $this->getModelManager(),
                                'sonata_field_description' => $ffds['image'],
                                'class' => $mediaAdmin->getClass(),
                                'sonata_admin' => $mediaAdmin->getClass(),
                                'translation_domain' => 'NTNewsBundle',
                            ),
                        ),
                        'translation_domain' => 'NTNewsBundle',
                        'label' => 'form.translations',
                    ))
                ->end()
            ->end()
            ->with('SEO', array('tab' => true))
                ->with('SEO', array('collapsed' => true, 'class' => 'col-md-12'))
                    ->add('metaData', 'meta_data')
                ->end()
            ->end()
            ->with('Publish Workflow', array('tab' => true))
                ->with('Publish Workflow', array(
                    'class' => 'col-md-12',
                    'label' => 'form.general',
                    'translation_domain' => 'NTNewsBundle',
                ))
                    ->add('publishWorkflow', 'nt_publish_workflow', array(
                        'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true,
                    ))
                ->end()
            ->end();
    }
}
