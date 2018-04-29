<?php

namespace NT\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use NT\CoreBundle\DependencyInjection\Compiler\SonataRoutePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NTCoreBundle extends Bundle
{
	/**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SonataRoutePass());
    }
}
