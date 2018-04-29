<?php
namespace NT\FrontendBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\Matcher\Matcher;
use NT\FrontendBundle\Menu\UriVoter;

class Builder extends ContainerAware
{
    private $matcher, $router;

    public function footerLeftMenu(FactoryInterface $factory, array $options)
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->router = $this->container->get("router");
        $this->matcher = new Matcher();
        $this->matcher->addVoter(new UriVoter($_SERVER['REQUEST_URI']));
        $menu = $factory->createItem('root', $options);
        $repo = $em->getRepository('NTMenuBundle:Menu');
        $root = $repo->findOneById(8);
        //$roots = $repo->getRootNodes();
        $children = $repo->findAllChildren($root->getId()); #$root->getChildren();
        $this->createMenu($children, $menu, $repo);

        return $menu;
    }

    public function footerRightMenu(FactoryInterface $factory, array $options)
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->router = $this->container->get("router");
        $this->matcher = new Matcher();
        $this->matcher->addVoter(new UriVoter($_SERVER['REQUEST_URI']));
        $menu = $factory->createItem('root', $options);
        $repo = $em->getRepository('NTMenuBundle:Menu');
        $root = $repo->findOneById(15);
        //$roots = $repo->getRootNodes();
        $children = $repo->findAllChildren($root->getId()); #$root->getChildren();
        $this->createMenu($children, $menu, $repo);

        return $menu;
    }

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $em = $this->container->get('doctrine')->getManager();
        $this->router = $this->container->get("router");
        $this->matcher = new Matcher();
        $this->matcher->addVoter(new UriVoter($_SERVER['REQUEST_URI']));
        $menu = $factory->createItem('root', $options);
        $repo = $em->getRepository('NTMenuBundle:Menu');
        $root = $repo->findOneById(1);
        //$roots = $repo->getRootNodes();
        $children = $repo->findAllChildren($root->getId()); #$root->getChildren();
        $this->createMenu($children, $menu, $repo);

        return $menu;
    }

    private function createMenu($roots, $menu, $repo)
    {
        foreach ($roots as $itm) {
            if ($itm->getUrl() && $itm->getTitle()) {
                try {
                    $route = $this->router->match($itm->getUrl());

                    $params = array(
                        'route' => $route['_route'],
                        'linkAttributes' => array('target' => $itm->getTarget()),
                    );

                    unset($route['_controller'], $route['_route']);
                    $params['routeParameters'] = $route;

                    $subMenu = $menu->addChild($itm->getTitle(), $params);
                    $subMenu->setLinkAttributes(array(
                        'target' => $itm->getTarget(),
                        'class' => $itm->getClass() != null ? $itm->getClass() : '',
                    ));
                    if ($this->matcher->isCurrent($subMenu)) {
                        $subMenu->getParent()->setCurrent(true);
                        if ($subMenu->getParent()->getParent()) {
                            $subMenu->getParent()->getParent()->setCurrent(true);
                        }

                        $subMenu->setCurrent(true);
                    } else {
                        $em = $this->container->get('doctrine')->getManager();
                        $locale = $this->container->get('request')->getLocale();
                        if ($this->container->get('request')->get('route') == 'content') {
                            $slug = $this->container->get('request')->get('route_params')['slug'];
                            $repository = $em->getRepository('NTContentBundle:Content');
                            $obj = $repository->findOneBySlugAndLocale($slug, $locale);
                            $path = $repository->getPath($obj);
                            $uri = $itm->getUrl();
                            foreach ($path as $item) {
                                if (strpos($uri, $item->getSlug()) !== false) {
                                    $subMenu->setCurrent(true);
                                    break;
                                }
                            }
                        } elseif ($this->container->get('request')->get('route') == 'news_view') {
                            $slug = $this->container->get('request')->get('route_params')['slug'];
                            $repository = $em->getRepository('NTNewsBundle:News');
                            $obj = $repository->findOneBySlugAndLocale($slug, $locale);

                            $uri = $itm->getUrl();
                            if ($uri == '/'.$locale.'/news' && $obj) {
                                $subMenu->setCurrent(true);
                            }
                        } elseif ($this->container->get('request')->get('route') == 'estate') {
                            $slug = $this->container->get('request')->get('route_params')['slug'];
                            $repository = $em->getRepository('NTEstatesBundle:Estate');
                            $obj = $repository->findOneBySlugAndLocale($slug, $locale);

                            $uri = $itm->getUrl();
                            if ($uri == '/'.$locale.'/estates' && $obj) {
                                $subMenu->setCurrent(true);
                            }
                        } elseif ($this->container->get('request')->get('route') == 'estate_category') {
                            $slug = $this->container->get('request')->get('route_params')['slug'];
                            $repository = $em->getRepository('NTEstatesBundle:EstateCategory');
                            $obj = $repository->findOneBySlugAndLocale($slug, $locale);

                            $uri = $itm->getUrl();
                            if ($uri == '/'.$locale.'/estates' && $obj) {
                                $subMenu->setCurrent(true);
                            }
                        } elseif ($this->container->get('request')->get('route') == 'service') {
                            $slug = $this->container->get('request')->get('route_params')['slug'];
                            $repository = $em->getRepository('NTServicesBundle:Service');
                            $obj = $repository->findOneBySlugAndLocale($slug, $locale);

                            $uri = $itm->getUrl();
                            if ($uri == '/'.$locale.'/services' && $obj) {
                                $subMenu->setCurrent(true);
                            }
                        } elseif ($this->container->get('request')->get('route') == 'service_category') {
                            $slug = $this->container->get('request')->get('route_params')['slug'];
                            $repository = $em->getRepository('NTServicesBundle:ServiceCategory');
                            $obj = $repository->findOneBySlugAndLocale($slug, $locale);

                            $uri = $itm->getUrl();
                            if ($uri == '/'.$locale.'/services' && $obj) {
                                $subMenu->setCurrent(true);
                            }
                        } elseif ($this->container->get('request')->get('route') == 'careers') {
                            $slug = $this->container->get('request')->get('route_params')['slug'];
                            $repository = $em->getRepository('NTCareersBundle:Career');
                            $obj = $repository->findOneBySlugAndLocale($slug, $locale);

                            $uri = $itm->getUrl();
                            if ($uri == '/'.$locale.'/careers' && $obj) {
                                $subMenu->setCurrent(true);
                            }
                        }elseif ($this->container->get('request')->get('route') == 'career_view') {
                            $slug = $this->container->get('request')->get('route_params')['slug'];
                            $repository = $em->getRepository('NTCareersBundle:Career');
                            $obj = $repository->findOneBySlugAndLocale($slug, $locale);
                            $uri = $itm->getUrl();
                            if ($uri == '/'.$locale.'/careers' && $obj) {
                                $subMenu->setCurrent(true);
                            }
                        }  elseif ($this->container->get('request')->get('route') == 'dealers') {
                            $slug = $this->container->get('request')->get('route_params');
                            $repository = $em->getRepository('NTDealersBundle:Dealer');
                            $obj = $repository->findOneBySlugAndLocale($slug, $locale);

                            $uri = $itm->getUrl();
                            if ($uri == '/'.$locale.'/dealers' && $obj) {
                                $subMenu->setCurrent(true);
                            }
                        }elseif ($this->container->get('request')->get('route') == 'post_without_category') {

            			    $slug = $this->container->get('request')->get('route_params')['slug'];
            			    $repository = $em->getRepository('NTNewsBundle:News');
                            $obj = $repository->findOneBySlugAndLocale($slug, $locale);
                            $uri = $itm->getUrl();
                            if ($uri == '/'.$locale.'/posts' && $obj) {
                                $subMenu->setCurrent(true);
                            }
                        }elseif ($this->container->get('request')->get('route') == 'gallery_view') {

                            $slug = $this->container->get('request')->get('route_params')['slug'];
                            $repository = $em->getRepository('NTGalleriesBundle:Gallery');
                            $obj = $repository->findOneBySlugAndLocale($slug, $locale);
                            $uri = $itm->getUrl();
                            if ($uri == '/'.$locale.'/galleries' && $obj) {
                                $subMenu->setCurrent(true);
                            }
                        }

                    }

                    if ($children = $repo->findAllChildren($itm->getId())) {
                        $subMenu->setLinkAttributes(array(
                            'target' => $itm->getTarget(),
                            'class' => $itm->getClass() != null ? $itm->getClass() : '',
                        ));
                        $subMenu->setAttribute('class', 'test');
                        $this->createMenu($children, $subMenu, $repo);
                    }
                } catch (\Exception $e) {
                    $subMenu = $menu->addChild($itm->getTitle(), array(
                        'uri' => $itm->getUrl(),
                        'linkAttributes' => array(
                            'target' => $itm->getTarget(),
                            'class' => $itm->getClass() != null ? $itm->getClass() : '',
                        ),
                    ));

                    if ($children = $repo->findAllChildren($itm->getId())) {
                        if ($itm->getClass() != null) {
                        }
                        $subMenu->setLinkAttributes(array(
                            'target' => $itm->getTarget(),
                            'class' => $itm->getClass() != null ? $itm->getClass() : '',
                        ));
                        $this->createMenu($children, $subMenu, $repo);
                    }
                }
            }
        }
    }
}
