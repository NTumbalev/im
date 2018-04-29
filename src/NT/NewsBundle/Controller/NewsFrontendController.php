<?php

namespace NT\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;

class NewsFrontendController extends Controller
{
    use \NT\FrontendBundle\Traits\NTHelperTrait;

    protected $matcher, $router;
    protected $contentPageId          = 12;
    protected $mainRootName           = 'posts_list';
    protected $postsCategoriesPerPage = 4;
    protected $postsPerPage           = 4;
    protected $itemsRepo              = 'NTNewsBundle:News';
    protected $itemsCategoriesRepo    = 'NTNewsBundle:NewsCategory';

    /**
     * @Route("/posts/{page}", name="posts_list", requirements={"page": "\d+"})
     * @Template("NTNewsBundle:Frontend:posts_list.html.twig")
     */
    public function postsListAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $postsRepo = $em->getRepository($this->itemsRepo);
        $postsCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $postsCategories = $postsCategoriesRepo->findAllMainCategoriesByLocale($request->getLocale());
        if (count($postsCategories) > 0) {
            return $this->forward('NTNewsBundle:NewsFrontend:postsCategoriesList', array('_route' => 'posts_categories_list', 'page'=> $page));
        }

        $content = $this->getContentPage();

        $query = $postsRepo->getPostsListingQuery(null, $locale, $page, $this->postsPerPage);
        $posts = new Paginator($query, true);

        $this->generateSeoAndOgTags($content);

        return array(
            'posts'       => $posts,
            'content'     => $content,
            'breadCrumbs' => $this->generateBreadCrumbs($request),
            'sideBar'     => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/posts/{page}", name="posts_categories_list", requirements={"page": "\d+"})
     * @Template("NTNewsBundle:Frontend:posts_categories_list.html.twig")
     */
    public function postsCategoriesListAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $postsRepo = $em->getRepository($this->itemsRepo);

        $query = $postsRepo->getPostsListingQuery(null, $locale, $page, $this->postsPerPage);
        $posts = new Paginator($query, true);

        $content = $this->getContentPage();

        $this->generateSeoAndOgTags($content);

        return array(
            'content'     => $content,
            'posts'       => $posts,
            'breadCrumbs' => $this->generateBreadCrumbs($request),
            'sideBar'     => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/posts/{categorySlug}/{page}", name="posts_categories_category_view", requirements={"page": "\d+"})
     * @Template("NTNewsBundle:Frontend:posts_categories_category_view.html.twig")
     */
    public function postsCategoriesCategoryViewAction(Request $request, $categorySlug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $postsCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $postCategory = $postsCategoriesRepo->findOneBySlugAndLocale($categorySlug, $locale);
        if (!$postCategory) {
            throw $this->createNotFoundException(sprintf("Post category '%s' not found", $categorySlug));
        }

        $query = $postsCategoriesRepo->getCategoriesListingQuery($postCategory->getId(), $locale, $page, $this->postsCategoriesPerPage);
        $postCategoryChildren = new Paginator($query, true);

        if ($postCategoryChildren->count() <= 0) {
            //if no categories go to render posts
            $postsRepo = $em->getRepository($this->itemsRepo);
            $query = $postsRepo->getPostsListingQuery($postCategory->getId(), $locale, $page, $this->postsPerPage);
            $categoryPosts = new Paginator($query, true);

            $this->generateSeoAndOgTags($postCategory);

            return $this->render('NTNewsBundle:Frontend:posts_category_posts_list.html.twig', array(
                'postCategory'  => $postCategory,
                'categoryPosts' => $categoryPosts,
                'content'       => $this->getContentPage(),
                'breadCrumbs'   => $this->generateBreadCrumbs($request),
                'sideBar'       => $this->getSideBar($request),
            ));
        }

        $this->generateSeoAndOgTags($postsCategory);

        return array(
            'postsCategory'         => $postsCategory,
            'postsCategoryChildren' => $postsCategoryChildren,
            'content'                 => $this->getContentPage(),
            'breadCrumbs'             => $this->generateBreadCrumbs($request),
            'sideBar'                 => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/posts/{categorySlug}/{slug}", name="posts_category_post_view")
     * @Template("NTNewsBundle:Frontend:posts_category_post_view.html.twig")
     */
    public function postsCategoryPostViewAction(Request $request, $categorySlug = null, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $postsRepo = $em->getRepository($this->itemsRepo);
        $postsCategoriesRepo = $em->getRepository($this->itemsCategoriesRepo);

        $post = $postsRepo->findOneBySlugAndLocale($slug, $locale);
        if (!$post) {
            throw $this->createNotFoundException(sprintf('Post "%s" not found', $slug));
        }

        $postCategory = $postsCategoriesRepo->findOneBySlugAndLocale($categorySlug, $locale);
        if (!$postCategory) {
            throw $this->createNotFoundException(sprintf('Category "%s" not found', $categorySlug));
        }

        $params = $this->getImageUrlFromGallery($post->getTranslations()->get($locale)->getGallery());
        $this->generateSeoAndOgTags($post, $params);

        return array(
            'post'         => $post,
            'postCategory' => $postCategory,
            'latestPosts'  => $postsRepo->findAllByLocale($locale, 3),
            'content'      => $this->getContentPage(),
            'breadCrumbs'  => $this->generateBreadCrumbs($request),
            'sideBar'      => $this->getSideBar($request),
        );
    }

    /**
     * @Route("/post/{slug}", name="post_without_category")
     * @Template("NTNewsBundle:Frontend:post_without_category.html.twig")
     */
    public function postWithoutCategoryAction(Request $request, $categorySlug = null, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        $postsRepo = $em->getRepository($this->itemsRepo);

        $post = $postsRepo->findOneBySlugAndLocale($slug, $locale);
        if (!$post) {
            throw $this->createNotFoundException(sprintf('Post "%s" not found', $slug));
        }

        $params = $this->getImageUrlFromGallery($post->getTranslations()->get($locale)->getGallery());
        $this->generateSeoAndOgTags($post, $params);

        return array(
            'post'        => $post,
            'content'     => $this->getContentPage(),
            'latestPosts' => $postsRepo->findAllByLocale($locale, 3),
            'breadCrumbs' => $this->generateBreadCrumbs($request),
            'sideBar'     => $this->getSideBar($request),
        );
    }

    /**
     * Render posts on homepage
     * @Template("NTNewsBundle:Frontend:homepagePosts.html.twig")
     */
    public function homepagePostsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('NTNewsBundle:News')
                    ->findAllOnHomepageByLocale($request->getLocale());

        return array(
            'posts' => $posts
        );
    }

    private function getSideBar(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($this->itemsCategoriesRepo);
        $locale = $request->getLocale();

        $sideBar = null;
        $menuChildrens = $repo->findAllByLocale($locale);
        $factory = new MenuFactory();
        $sideBar = $factory->createItem('root', array());

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
        $categorySlug = $request->get('categorySlug');
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
            if (count($children = $repo->findAllChildrenCategoriesByLocale($itm->getId(), $locale))) {
                $subMenu->setAttribute('class', $subMenu->getAttribute('class').' hasDropdown');
                $subMenu->setChildrenAttribute('class', 'dropdown');
                $this->createMenu($children, $subMenu, $repo, $locale);
            }
        }
    }
}
