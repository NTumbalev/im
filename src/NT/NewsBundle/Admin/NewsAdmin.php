<?php
namespace NT\NewsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewsAdmin extends Admin
{
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
        } else if ($action == 'list') {
            $menu->addChild(
                $this->getTranslator()->trans("action.list", array(), 'NTCoreBundle'),
                array('uri' => $this->generateUrl('list'))
            );
        }
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('slug')
                ->add('title')
                ->add('simple_description')
                ->add('description')
                ->add('createdAt')
                ->add('updatedAt')
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'form.title'))
            ->add('description', null, array('label' => 'form.description'))
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
            ->addIdentifier('title', null, array('label' => 'list.title'))
            // ->add('postsCategories', null, array('label' => 'form.newsCategories'))
            ->add('publishWorkflow.isActive', null, array('label' => 'form.isActive', 'editable' => true))
            ->add('isHomepage', null, array('label' => 'form.isHomepage', 'editable' => true))
            ->add('isTop', null, array('label' => 'form.isTop', 'editable' => true))
            ->add('createdAt', null, array('label' => 'list.createdAt'))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        #'history' => array('template' => 'NTCoreBundle:Admin:list_action_history.html.twig'),
                    ), 'label' => 'actions',
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
        $galleryAdmin = $this->configurationPool->getAdminByClass("Application\\Sonata\\MediaBundle\\Entity\\Gallery");
        $translationAdmin = $this->configurationPool->getAdminByAdminCode('nt.news.admin.news_translation');
        $ffds = $translationAdmin->getFormFieldDescriptions();
        $ffds['image']->setAssociationAdmin($mediaAdmin);
        $ffds['gallery']->setAssociationAdmin($galleryAdmin);

        $date = new \DateTime();
        $formMapper
        ->with('General', array('tab' => true))
            ->with('General', array('collapsed' => true, 'class' => 'col-md-12'))
                ->add('publishedDate', 'sonata_type_datetime_picker', array(
                    'format' => 'dd/MM/yyyy HH:mm',
                    'widget' => 'single_text',
                    'data' => $this->getSubject() && $this->getSubject()->getPublishedDate() ? $this->getSubject()->getPublishedDate() : new \DateTime(),
                    'dp_side_by_side' => true,
                    'required' => true,
                    'label' => 'form.published_date',
                    'constraints' => new NotBlank(array('message' => 'Задължително !')),
                ))
                // ->add('postsCategories', 'sonata_type_model', array(
                //     'label' => 'form.newsCategories',
                //     'required' => false,
                //     'multiple' => true,
                //     'btn_add' => false
                // ))
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
                        'simpleDescription' => array(
                            'required' => false,
                            'label' => 'form.simpleDescription',
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
                            'label' => 'Изображение',
                            'required' => false,
                            'field_type' => 'sonata_type_model_list',
                            'model_manager' => $this->getModelManager(),
                            'sonata_field_description' => $ffds['image'],
                            'class' => $mediaAdmin->getClass(),
                            'sonata_admin' => $mediaAdmin->getClass(),
                            'translation_domain' => 'NTNewsBundle'
                        ),
                        // 'gallery' => array(
                        //     'label' => 'form.gallery',
                        //     'required' => false,
                        //     'field_type' => 'sonata_type_model_list',
                        //     'model_manager' => $this->getModelManager(),
                        //     'sonata_field_description' => $ffds['gallery'],
                        //     'class' => $galleryAdmin->getClass(),
                        //     'translation_domain' => 'NTNewsBundle',
                        // )
                    ),
                    'exclude_fields' => array('gallery'),
                    'translation_domain' => 'NTNewsBundle',
                    'label' => 'form.translations',
                ))
                ->add('isHomepage', null, array(
                    'label' => 'form.isHomepage',
                    'required' => false
                ))
                ->add('isTop', null, array(
                    'label' => 'form.isTop',
                    'required' => false
                ))
                #->add('shareIcons', null, array('label' => 'form.showSocialIcons'))
            ->end()
        ->end()
        ->with('SEO', array('tab' => true))
            ->with('SEO', array('collapsed' => true, 'class' => 'col-md-12'))
                ->add('metaData', 'meta_data')
            ->end()
        ->end()
        ->with('Publish Workflow', array('tab' => true))
            ->with('Workflow', array('collapsed' => true, 'class' => 'col-md-12'))
                ->add('publishWorkflow', 'nt_publish_workflow', array(
                    'is_active' => $this->getSubject()->getPublishWorkflow() ? $this->getSubject()->getPublishWorkflow()->getIsActive() : true,
                ))
            ->end()
        ->end()
        ;
    }
}
