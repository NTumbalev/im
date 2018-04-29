<?php

namespace NT\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use NT\ContentBundle\Entity\Content;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;

class ContentFrontendController extends Controller
{
    /**
     * @Route("/contacts", name="contacts")
     * Route("/contacts/service/{item}", name="contact_service")
     * Route("/contacts/product/{item}", name="contact_product")
     * @Route("/contacts/success", name="contact_success")
     * @Template("NTContentBundle:Frontend:contacts.html.twig")
     */
    public function contactsAction(Request $request, $item = null)
    {
        if ($request->get('_route') == 'contact_success' && $request->headers->get('referer') == null) {
            return $this->redirect($this->generateUrl('contacts'));
        }

        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');
        $settings = $this->get('nt.settings_manager');
        $action = array(
            'action' => $this->generateUrl('contacts', array(), true)
        );

        $path = null;

        $route = $request->get('_route');
        if ($route == 'contact_product' && !is_null($item)) {
            $item = $em->getRepository('NTProductsBundle:Product')->findOneBySlugAndLocale($item, $request->getLocale());
            if (!$item) {
                throw $this->createNotFoundException('Product not found');
            }

            $action = array(
                'action' => $this->generateUrl('contact_product', array('item' => $item->getSlug()), true)
            );

            $path = $this->generateUrl('product_without_category', array('slug' => $item->getSlug()));
        } elseif ($route == 'contact_service' && !is_null($item)) {
            $item = $em->getRepository('NTServicesBundle:Service')->findOneBySlugAndLocale($item, $request->getLocale());
            if (!$item) {
                throw $this->createNotFoundException('Service not found');
            }

            $action = array(
                'action' => $this->generateUrl('contact_service', array('item' => $item->getSlug()), true)
            );

            $path = $this->generateUrl('service_without_category', array('slug' => $item->getSlug()));
        }

        $content = $em->getRepository('NTContentBundle:Content')->findOneById(6);
        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $breadCrumbs = array($content->getTitle() => null);

        $form = $this->createForm('contacts', null, $action);

        if ($request->isMethod('POST')) {
            $this->get('session')->getFlashBag()->clear();
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $adminMessage = \Swift_Message::newInstance()
                    ->setSubject($translator->trans('contact.subject', array(), 'NTFrontendBundle'))
                    ->setFrom($settings->get('sender_email'))
                    ->setTo(explode(',', $settings->get('contact_email')))
                    ->setBody(
                        $this->renderView(
                            'NTContentBundle:Email:contact_mail.html.twig', array(
                                'data' => $data,
                                'item' => $item,
                                'path' => $path
                            )
                        ),
                        'text/html'
                    )
                ;

                // $userMessage = \Swift_Message::newInstance()
                //     ->setSubject($translator->trans('contact.user_message_subject', array(), 'NTFrontendBundle'))
                //     ->setFrom($settings->get('sender_email'))
                //     ->setTo($data['email'])
                //     ->setBody(
                //         $this->renderView(
                //             'NTContentBundle:Email:contact_mail.html.twig', array(
                //                 'data' => $data,
                //                 'item' => $item,
                //                 'path' => $path
                //             )
                //         ),
                //         'text/html'
                //     )
                // ;

                $mailer = $this->get('mailer');
                $mailer->send($adminMessage);
                // $mailer->send($userMessage);

                $this->get('session')->getFlashBag()->add('success', 'Your message has been sent.');
                return $this->redirect($this->generateUrl('contact_success'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Your message has not been sent.');
            }
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('nt.seo', $event);

        if ($image = $content->getTranslations()->get($request->getLocale())->getHeaderImage()) {
            $provider = $this->container->get($image->getProviderName());
            $params = array(
                'image_url' => $request->getSchemeAndHttpHost() . $provider->generatePublicUrl($image, 'reference')
            );
        } else {
            $params = array();
        }

        $this->get('nt.og_tags')->loadOgTags($content, $params);

        $dealers = $em->getRepository('NTDealersBundle:Dealer')->findAllOnContactsByLocale($request->getLocale());

        return array(
            'form'        => $form->createView(),
            'content'     => $content,
            'breadCrumbs' => $breadCrumbs,
            'dealers'     => $dealers,
            'item'        => $item
        );
    }

    /**
     * Route("/sitemap", name="sitemap")
     * @Template("NTContentBundle:Frontend:sitemap.html.twig")
     */
    public function sitemapAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locale  = $request->getLocale();
        $sitemapContent = $em->getRepository('NTContentBundle:Content')->findOneById(3);
        $breadCrumbs = array($sitemapContent->getTitle() => null);

        $bundles = $this->container->getParameter('kernel.bundles');
        if (array_key_exists('NTContentBundle', $bundles)) {
            $content = $em->getRepository('NTContentBundle:Content')->findAllByLocale($locale);
        }
        if (array_key_exists('NTCareersBundle', $bundles)) {
            $careers = $em->getRepository('NTCareersBundle:Career')->findAllByLocale($locale);
        }
        if (array_key_exists('NTProductsBundle', $bundles)) {
            $products = $em->getRepository('NTProductsBundle:Product')->findAllByLocale($locale);
        }
        if (array_key_exists('NTServicesBundle', $bundles)) {
            $services = $em->getRepository('NTServicesBundle:Service')->findAllByLocale($locale);
        }
        if (array_key_exists('NTBrandsBundle', $bundles)) {
            $brands = $em->getRepository('NTBrandsBundle:Brand')->findAllByLocale($locale);
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent($sitemapContent);
        $dispatcher->dispatch('nt.seo', $event);

        if ($image = $sitemapContent->getTranslations()->get($locale)->getHeaderImage()) {
            $provider = $this->container->get($image->getProviderName());
            $params = array(
                'image_url' => $request->getSchemeAndHttpHost() . $provider->generatePublicUrl($image, 'reference')
            );
        } else {
            $params = array();
        }

        $this->get('nt.og_tags')->loadOgTags($sitemapContent, $params);

        return array(
            'content'        => $content,
            'sitemapContent' => $sitemapContent,
            'careers'        => $careers,
            'products'       => $products,
            'services'       => $services,
            'brands'         => $brands,
            'breadCrumbs'    => $breadCrumbs,
        );
    }

    /**
     * @Route("/{slug}", name="content")
     * @Template("NTContentBundle:Frontend:index.html.twig")
     */
    public function indexAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $this->get('nt.settings_manager');
        $locale = $request->getLocale();

        $repo = $em->getRepository('NTContentBundle:Content');
        $content = $repo->findOneBySlugAndLocale($slug, $locale);
        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $children = $repo->findAllChildren($content->getId(), $locale);
        //if page have no description try to find children with description and redirect to it
        if (!$content->getDescription() && !$content->getParent()) {
            foreach ($children as $contentChild) {
                if ($contentChild->getDescription()) {
                    return $this->redirect($this->generateUrl('content', array('slug' => $contentChild->getSlug())));
                }
            }
        }

        //root is the parent
        $root = $content->getParent();

        //have no parent
        if ($root == null) {
            //check for children
            if (count($children) > 0) {
                //content is root
                $root = $content;
            }
        } else {
            //have parent, so we search for the root
            while ($root->getParent() != null) {
                $root = $root->getParent();
            }
        }

        //if there are root we build side menu
        $sideBar = null;
        if ($root != null) {
            $menuChildrens = $repo->findAllChildren($root->getId(), $locale);
            $factory = new MenuFactory();
            $sideBar = $factory->createItem('root', array(
                'childrenAttributes' => array(
                    'class' => 'top-menu'
                )
            ));

            $this->addMenuItem($sideBar, $root);

            $this->router = $this->container->get("router");
            $this->matcher = new Matcher();
            $this->matcher->addVoter(new UriVoter($_SERVER['REQUEST_URI']));
            $this->createMenu($menuChildrens, $sideBar, $repo, $locale);
            $renderer = new ListRenderer(new \Knp\Menu\Matcher\Matcher());
        }

        $breadCrumbs = array();
        foreach ($repo->getPath($content) as $object) {
            if ($object->getSlug() != null) {
                $breadCrumbs[$object->getTitle()] = $this->generateUrl('content', array('slug' => $object->getSlug()));
            }
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('nt.seo', $event);

        if ($image = $content->getTranslations()->get($locale)->getHeaderImage()) {
            $provider = $this->container->get($image->getProviderName());
            $params = array(
                'image_url' => $request->getSchemeAndHttpHost() . $provider->generatePublicUrl($image, 'reference')
            );
        } else {
            $params = array();
        }

        $this->get('nt.og_tags')->loadOgTags($content, $params);

        return array(
            'content'     => $content,
            'children'    => $children,
            'breadCrumbs' => $breadCrumbs,
            'settings'    => $settings,
            'sideBar'     => $sideBar != null ? $renderer->render($sideBar, array('currentClass' => 'selected', 'ancestorClass'=>'selected')) : false,
        );
    }

    private function createMenu($children, $menu, $repo, $locale)
    {
        foreach ($children as $itm) {
            $this->addMenuItem($menu, $itm);

            if (count($children = $repo->findAllChildren($itm->getId(), $locale))) {
                $subMenu->setAttribute('class', $subMenu->getAttribute('class').' hasDropdown');
                $subMenu->setChildrenAttribute('class', 'dropdown');
                $this->createMenu($children, $subMenu, $repo, $locale);
            }
        }
    }

    private function addMenuItem($menu, $itm)
    {
        $subMenu = $menu->addChild(
            $itm->getTitle(), 
            array(
                'uri' => $this->generateUrl('content', array('slug' => $itm->getSlug())), 
                'currentClass' => 'selected'
            )
        );

        if ($itm->getSlug() === '/' && $itm->getSlug() == $this->container->get('request')->getRequestUri()) {
            $subMenu->setAttribute('class', 'selected');
        } elseif ($itm->getSlug() !== '/' && strpos($this->container->get('request')->getRequestUri(), $itm->getSlug()) !== false) {
            $subMenu->setAttribute('class', 'selected');
        }
    }
}
