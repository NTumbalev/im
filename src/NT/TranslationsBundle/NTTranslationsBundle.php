<?php

namespace NT\TranslationsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use NT\TranslationsBundle\DependencyInjection\Compiler\TranslatorPass;

class NTTranslationsBundle extends Bundle
{
	public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TranslatorPass());
    }
}
