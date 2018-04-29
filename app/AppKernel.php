<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),

            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\UserBundle\SonataUserBundle(),
            new Sonata\SeoBundle\SonataSeoBundle(),

            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),

            // new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new DS\ReCaptchaBundle\ReCaptchaBundle(),
            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),

            new NT\SettingsBundle\NTSettingsBundle(),
            new NT\TranslationsBundle\NTTranslationsBundle(),
            new NT\PublishWorkflowBundle\NTPublishWorkflowBundle(),
            new NT\SEOBundle\NTSEOBundle(),
            new NT\ContentBundle\NTContentBundle(),
            new NT\FrontendBundle\NTFrontendBundle(),
            new NT\TinyMCEBundle\NTTinyMCEBundle(),
            new NT\SliderBundle\NTSliderBundle(),
            new NT\MenuBundle\NTMenuBundle(),
            new NT\DealersBundle\NTDealersBundle(),
            new NT\AccentsBundle\NTAccentsBundle(),
            new NT\NewsBundle\NTNewsBundle(),
            new NT\ProjectsBundle\NTProjectsBundle(),
            new NT\CoreBundle\NTCoreBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
