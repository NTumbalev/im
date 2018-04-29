<?php

namespace NT\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class FrontendController extends Controller
{
    /**
     * @Route("/", name="homepage", defaults={"_locale"="en"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $content = $em->getRepository('NTContentBundle:Content')->findOneById(1);
        if (!$content) {
            throw $this->createNotFoundException('The page is not found');
        }

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent($content);
        $dispatcher->dispatch('nt.seo', $event);

        $this->get('nt.og_tags')->loadOgTags($content);

        return array(
            'content' => $content,
        );
    }

    /**
     * @Template("NTFrontendBundle:Frontend:footer.html.twig")
     */
    public function renderFooterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $this->container->get('nt.settings_manager');
        $locale = $request->getLocale();

        $footerLeft = $em->getRepository('NTContentBundle:Content')->findOneById(13);
        $footerMiddle = $em->getRepository('NTContentBundle:Content')->findOneById(14);
        $footerRight = $em->getRepository('NTContentBundle:Content')->findOneById(15);

        return array(
            'settings' => $settings,
            'footerLeft' => $footerLeft,
            'footerMiddle' => $footerMiddle,
            'footerRight' => $footerRight
        );
    }

    /**
     * @Template("NTFrontendBundle:Frontend:header.html.twig")
     */
    public function renderHeaderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $this->container->get('nt.settings_manager');
        $route = $request->attributes->get('route');
        if ($route == null) {
            $route = $request->attributes->get('_route');
        }
        $routeParams = $request->attributes->get('route_params');
        if ($routeParams == null) {
            $routeParams = $request->attributes->get('_route_params');
        }

        $currLocale = $request->getLocale();
        $locales = $this->container->getParameter('locales');
        unset($locales[array_search($currLocale, $locales)]);

        $urls = array();
        $itm = null;
        if ($route != null) {
            if (isset($routeParams['slug'])) {
                $itm = $this->findObject($route, $routeParams['slug'], $currLocale, $em);
            }

            if (isset($routeParams['categorySlug'])) {
                $itm = $this->findObject($route, $routeParams['categorySlug'], $currLocale, $em);
            }

            foreach ($locales as $key) {
                if ($itm != null) {
                    $trans = $itm->getTranslations()->get($key);
                    if ($trans != null && ($localeSlug = $trans->getSlug())) {
                        if (isset($routeParams['categorySlug'])) {
                            $urls[$key] = $this->generateUrl($route, array_merge($routeParams, array('locale' => $key, 'categorySlug' => $localeSlug)));
                        } else {
                            $urls[$key] = $this->generateUrl($route, array_merge($routeParams, array('locale' => $key, 'slug' => $localeSlug)));
                        }
                    } else {
                        $urls[$key] = $this->generateUrl($route, array_merge($routeParams, array('locale' => $key)));
                    }
                } else {
                    foreach ($routeParams as $k => $v) {
                        if ($k[0] === '_') {
                            unset($routeParams[$k]);
                        }
                    }
                    $urls[$key] = $this->generateUrl($route, array_merge($routeParams, array('_locale' => $key)));
                }
            }
        }

        return $this->render("NTFrontendBundle:Frontend:header.html.twig", array(
            'locales'  => $locales,
            'urls'     => $urls,
            'settings' => $settings
        ));
    }

    private function findObject($route, $slug, $locale, $em, $specific = null)
    {
        if ($route == 'content') {
            $repo = $em->getRepository('NTContentBundle:Content');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'news_view') {
            $repo = $em->getRepository('NTNewsBundle:News');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'product') {
            $repo = $em->getRepository('NTProductsBundle:Product');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'products_categories_category_view') {
            $repo = $em->getRepository('NTProductsBundle:ProductCategory');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'service') {
            $repo = $em->getRepository('NTServicesBundle:Service');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'service_category') {
            $repo = $em->getRepository('NTServicesBundle:ServiceCategory');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'dealer_view') {
            $repo = $em->getRepository('NTDealersBundle:Dealer');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        } elseif ($route == 'career_view') {
            $repo = $em->getRepository('NTCareersBundle:Career');

            return $repo->findOneBySlugAndLocale($slug, $locale);
        }

        return;
    }

    /**
     * @Template("NTFrontendBundle:Frontend:403.html.twig")
     */
    public function custom403Action()
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository("NTContentBundle:Content")->findOneById(19);

        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $breadCrumbs = array(
            $content->getTitle() => null,
        );

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent();
        $event->setTitle($content->getTitle());
        $dispatcher->dispatch('nt.seo', $event);

        return array(
            'content' => $content,
            'breadCrumbs' => $breadCrumbs,
        );
    }

    /**
     * @Template("NTFrontendBundle:Frontend:404.html.twig")
     */
    public function custom404Action()
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository("NTContentBundle:Content")->findOneById(11);

        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $breadCrumbs = array(
            $content->getTitle() => null,
        );

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent();
        $event->setTitle($content->getTitle());
        $dispatcher->dispatch('nt.seo', $event);

        return array(
            'content' => $content,
            'breadCrumbs' => $breadCrumbs,
        );
    }

    /**
     * @Template("NTFrontendBundle:Frontend:500.html.twig")
     */
    public function custom500Action()
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository("NTContentBundle:Content")->findOneById(18);

        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $breadCrumbs = array(
            $content->getTitle() => null,
        );

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent();
        $event->setTitle($content->getTitle());
        $dispatcher->dispatch('nt.seo', $event);

        return array(
            'content' => $content,
            'breadCrumbs' => $breadCrumbs,
        );
    }

    /**
     * @Template("NTFrontendBundle:Frontend:503.html.twig")
     */
    public function custom503Action()
    {
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository("NTContentBundle:Content")->findOneById(20);

        if (!$content) {
            throw $this->createNotFoundException("Page not found");
        }

        $breadCrumbs = array(
            $content->getTitle() => null,
        );

        $dispatcher = $this->get('event_dispatcher');
        $event = new \NT\SEOBundle\Event\SeoEvent();
        $event->setTitle($content->getTitle());
        $dispatcher->dispatch('nt.seo', $event);

        return array(
            'content' => $content,
            'breadCrumbs' => $breadCrumbs,
        );
    }
}
