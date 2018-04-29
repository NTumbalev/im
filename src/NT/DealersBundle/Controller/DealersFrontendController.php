<?php

namespace NT\DealersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DealersFrontendController extends Controller
{
    /**
     * @Route("/distributors", name="distributors")
     * @Template("NTDealersBundle:Frontend:listing.html.twig")
     */
    public function indexAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$translator = $this->get('translator');

        $dealersRepository = $em->getRepository('NTDealersBundle:Dealer');
        $distributors = $dealersRepository->findAllByLocale($request->getLocale());

        $content = $em->getRepository('NTContentBundle:Content')->findOneById(7);
        if (!$content) {
            throw $this->createNotFoundException('Page not found');
        }
    	foreach ($distributors as $distributor) {
			$distributorsArray[$distributor->getId()]['latitude'] = $distributor->getLatitude();
			$distributorsArray[$distributor->getId()]['longitude'] = $distributor->getLongitude();
			$distributorsArray[$distributor->getId()]['latitude'] = $distributor->getLatitude();
			$distributorsArray[$distributor->getId()]['description'] = $distributor->getPinDescription();
    	}

    	$dispatcher = $this->get('event_dispatcher');
    	$event = new \NT\SEOBundle\Event\SeoEvent($content);
    	$dispatcher->dispatch('nt.seo', $event);
    	$breadCrumbs = array($content->getTitle() => null);
        return array(
        	'distributors' => $distributors,
        	'content' => $content,
        	'breadCrumbs' => $breadCrumbs,
        	'distributorsArray' => json_encode($distributorsArray),
            'length' => count($distributors),
    	);
    }
}
