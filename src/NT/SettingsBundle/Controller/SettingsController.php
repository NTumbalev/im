<?php

namespace NT\SettingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SettingsController extends Controller
{
   /**
	* @Template("NTSettingsBundle::render.html.twig")
	*/
	public function renderAction($key)
	{
		$settingsManager = $this->get('nt.settings_manager');
		$content = $settingsManager->get($key);

		return array('content' => $content);
	}
}
