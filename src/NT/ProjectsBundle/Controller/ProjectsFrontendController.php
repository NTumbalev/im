<?php

namespace NT\ProjectsBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProjectsFrontendController extends Controller
{
    use \NT\FrontendBundle\Traits\NTHelperTrait;

    protected $matcher, $router;
    protected $contentPageId          = 18;
    protected $mainRootName           = 'projects_listing';
    protected $projectsPerPage        = 4;
    protected $itemsRepo              = 'NTProjectsBundle:Project';

    /**
     * @Route("/projects/{page}", name="projects_listing")
     * @Template("NTProjectsBundle:Frontend:projects_listing.html.twig")
     */
    public function projectsListingAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $projectsRepo = $em->getRepository($this->itemsRepo);

        $content = $this->getContentPage();

        $query = $projectsRepo->getProjectsListingQuery($locale, $page, $this->projectsPerPage);
        $projects = new Paginator($query, true);

        $this->generateSeoAndOgTags($content);

        return array(
            'projects'    => $projects,
            'content'     => $content,
            'breadCrumbs' => $this->generateBreadCrumbs($request),
            // 'sideBar'     => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/project/{slug}", name="project_view")
     * @Template("NTProjectsBundle:Frontend:project_view.html.twig")
     */
    public function projectViewAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $projectsRepo = $em->getRepository($this->itemsRepo);

        $project = $projectsRepo->findOneBySlugAndLocale($slug, $locale);
        if (!$project) {
            throw $this->createNotFoundException(sprintf('Project "%s" not found', $slug));
        }

        $this->generateSeoAndOgTags($project);

        return array(
            'project'     => $project,
            'content'     => $this->getContentPage(),
            'breadCrumbs' => $this->generateBreadCrumbs($request),
            'sideBar'     => $this->getSideBar($request),
        );
    }

    private function getSideBar(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($this->itemsRepo);
        $locale = $request->getLocale();

        $sideBar = null;
        $menuChildrens = $repo->findAllByLocale($locale);
        $factory = new MenuFactory();
        $sideBar = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'top-menu'
            )
        ));

        $this->router = $this->container->get("router");
        $this->matcher = new Matcher();
        $this->matcher->addVoter(new UriVoter($_SERVER['REQUEST_URI']));
        $this->createMenu($menuChildrens, $sideBar, $repo, $locale);
        $renderer = new ListRenderer(new \Knp\Menu\Matcher\Matcher());

        return $sideBar != null ? $renderer->render($sideBar, array('currentClass' => 'selected', 'ancestorClass'=>'selected')) : false;
    }

    private function createMenu($children, $menu, $repo, $locale)
    {
        $request = $this->container->get('request');
        $route = $request->get('_route');
        $slug = $request->get('slug');
        $requestUri = $request->getRequestUri();

        $params = array();

        foreach ($children as $itm) {
            $uri = $this->generateUrl($itm->getRoute(), $itm->getRouteParams());
            $subMenu = $menu->addChild($itm->getTitle(), array('uri' => $uri, 'currentClass' => 'selected'));
            if ($itm->getSlug() == $slug || $categorySlug == $itm->getSlug()) {
                if ($parentMenu = $subMenu->getParent()) {
                    $parentMenu->setAttribute('class', 'selected');
                }
                $subMenu->setAttribute('class', 'selected');
            }
            // if (count($children = $repo->findAllChildrenCategoriesByLocale($itm->getId(), $locale))) {
            //     $subMenu->setAttribute('class', $subMenu->getAttribute('class').' hasDropdown');
            //     $subMenu->setChildrenAttribute('class', 'dropdown');
            //     $this->createMenu($children, $subMenu, $repo, $locale);
            // }
        }
    }
}
