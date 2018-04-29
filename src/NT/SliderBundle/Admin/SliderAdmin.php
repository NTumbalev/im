<?php
namespace NT\SliderBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class SliderAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'rank',
    );

    private $choices = array(
        '_self' => 'В същия прозорец',
        '_blank' => 'В нов прозорец',
    );

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['hide'] = [
            'label'            => $this->trans('action_hide', array(), 'NTCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        ];
        $actions['show'] = [
            'label'            => $this->trans('action_show', array(), 'NTCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        ];

        return $actions;
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);

        if ($action == 'history') {
            $id = $this->getRequest()->get('id');
            $menu->addChild(
                "General",
                array('uri' => $this->generateUrl('history', array('id' => $id)))
            );

            $locales = $this->getConfigurationPool()->getContainer()->getParameter('locales');

            foreach ($locales as $value) {
                $menu->addChild(
                    strtoupper($value),
                    array('uri' => $this->generateUrl('history', array('id' => $id, 'locale' => $value)))
                );
            }
        }
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('title', null, array('label' => 'form.title'))
                ->add('image', null, array('label' => 'form.image'))
                ->add('createdAt', null, array('label' => 'form.createdAt'))
                ->add('updatedAt', null, array('label' => 'form.updatedAt'))
            ->end()
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('order', 'order');
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'form.title', 'translation_domain' => 'NTSliderBundle'))
        ;
    }

    /**
     * Configure the list
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('custom', null, array(
                'template' => 'NTSliderBundle:Admin:list_slider_custom.html.twig',
                'label' => 'form.image',
            ))
            ->add('createdAt', null, array('label' => 'form.createdAt'))
            ->add('publishWorkflow.isActive', null, array('label' => 'Активен', 'editable' => true))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        #'history' => array('template' => 'NTCoreBundle:Admin:list_action_history.html.twig'),
                    ),
                    'label' => 'form.actions',
                    'translation_domain' => 'NTSliderBundle',
                ))
            ;
    }

    /**
     * Configure the form
     *
     * @param FormMapper $formMapper formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $mediaAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Media");
        $translationAdmin = $this->configurationPool->getAdminByAdminCode('nt.slider.admin.slider_translations');
        $ffds = $translationAdmin->getFormFieldDescriptions();
        $ffds['image']->setAssociationAdmin($mediaAdmin);

        $formMapper
        ->with('General', array('tab' => true))
            ->with('General', array('collapsed' => true, 'class' => 'col-md-12'))
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'title' => array(
                            'field_type' => 'text',
                            'label' => 'form.title',
                            'required' => false
                        ),
                        'description' => array(
                            'field_type' => 'textarea',
                            'label' => 'form.description',
                            'attr' => array(
                                'class' => 'tinymce',
                                'data-theme' => 'bbcode'
                            ),
                            'required' => false
                        ),
                        'url' => array(
                            'required' => false,
                            'label' => 'form.url',
                        ),
                        'buttonTitle' => array(
                            'field_type' => 'text',
                            'label' => 'form.buttonTitle',
                            'required' => false
                        ),
                        'image' => array(
                            'label' => 'form.image',
                            'field_type' => 'sonata_type_model_list',
                            'model_manager' => $this->getModelManager(),
                            'sonata_field_description' => $ffds['image'],
                            'class' => $mediaAdmin->getClass(),
                            'translation_domain' => 'NTSliderBundle',
                        ),
                        // 'alt' => array(
                        //     'required' => false,
                        //     'label' => 'form.alt',
                        // ),
                        'target' => array(
                            'field_type' => 'choice',
                            'label' => 'form.target',
                            'choices' => $this->choices
                        ),
                    ),
                        'exclude_fields' => array('description', 'buttonTitle', 'alt'),
                        'label' => 'form.translations',
                        'translation_domain' => 'NTSliderBundle',
                ))
            ->end()
        ->end()
        ->with('Publish Workflow', array('tab' => true))
            ->with('Publish Workflow', array('collapsed' => true, 'class' => 'col-md-12'))
                ->add('publishWorkflow', 'nt_publish_workflow', array(
                    'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true,
                ))
            ->end()
        ->end()
        ;
    }
}
