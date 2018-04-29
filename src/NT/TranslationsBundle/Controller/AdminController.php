<?php

namespace NT\TranslationsBundle\Controller;

use NT\CoreBundle\Controller\HistoryCRUDController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

class AdminController extends HistoryCRUDController
{

	public function editAction($id = NULL, Request $request = null)
	{
		return $this->redirect($this->generateUrl('admin_nt_translations_translation_list'));
	}

    public function clearCacheAction(Request $request)
    {
        $this->get('translator')->removeLocalesCacheFiles($this->getManagedLocales());

        $message = $this->get('translator')->trans('translations.cache_removed', array(), 'NTTranslationsBundle');

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('message' => $message));
        }

        $this->get('session')->getFlashBag()->add('success', $message);

        return $this->redirect($this->generateUrl('admin_nt_translations_translation_list'));
    }

    /**
     * Returns managed locales.
     *
     * @return array
     */
    protected function getManagedLocales()
    {
        return $this->container->getParameter('nt_translations.managed_locales');
    }
}
