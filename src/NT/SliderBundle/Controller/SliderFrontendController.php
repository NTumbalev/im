<?php

namespace NT\SliderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use NT\ContentBundle\Entity\Content;

class SliderFrontendController extends Controller
{
    /**
     * @Template("NTSliderBundle:Frontend:index.html.twig")
     */
    public function homepageSliderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $slides = $em->getRepository('NTSliderBundle:Slider')->findAllByLocale($request->getLocale());

        return array(
            'slides' => $slides
        );
    }
}
