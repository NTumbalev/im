<?php

namespace NT\TranslationsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Imports translation files content in the database.
 * Only imports files for locales defined in nt_translations.managed_locales.
 *
 * @author Hristo Hristoff <hristo.hristov@nt.bg>
 */
class ImportTranslationsCommand extends ContainerAwareCommand
{
    /**
     * @var Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * @var Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('nt:translations:import');
        $this->setDescription('Import all translations from flat files (xliff, yml, php) into the database.');

        $this->addOption('cache-clear', 'c', InputOption::VALUE_NONE, 'Remove translations cache files for managed locales.');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force import, replace database content.');
        $this->addOption('globals', 'g', InputOption::VALUE_NONE, 'Import only globals (app/Resources/translations.');
        $this->addOption('locales', 'l', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Import only for these locales, instead of using the managed locales.');

        $this->addArgument('bundle', InputArgument::OPTIONAL,'Import translations for this specific bundle.', null);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $locales = $this->input->getOption('locales');
        if (empty($locales)) {
            $locales = $this->getContainer()->getParameter('nt_translations.managed_locales');
        }

        $bundleName = $this->input->getArgument('bundle');
        if ($bundleName) {
            $bundle = $this->getApplication()->getKernel()->getBundle($bundleName);
            $this->importBundleTranslationFiles($bundle, $locales);
        } else {
            $this->output->writeln('<info>*** Importing application translation files ***</info>');
            $this->importAppTranslationFiles($locales);

            if (!$this->input->getOption('globals')) {
                $this->output->writeln('<info>*** Importing bundles translation files ***</info>');
                $this->importBundlesTranslationFiles($locales);

                $this->output->writeln('<info>*** Importing component translation files ***</info>');
                $this->importComponentTranslationFiles($locales);
            }
        }

        if ($this->input->getOption('cache-clear')) {
            $this->output->writeln('<info>Removing translations cache files ...</info>');
            $this->removeTranslationCache();
        }
    }

    /**
     * Imports Symfony's components translation files.
     *
     * @param array $locales
     */
    protected function importComponentTranslationFiles(array $locales)
    {
        $classes = array(
            'Symfony\Component\Validator\Validator' => '/Resources/translations',
            'Symfony\Component\Form\Form' => '/Resources/translations',
            'Symfony\Component\Security\Core\Exception\AuthenticationException' => '/../../Resources/translations',
        );

        $dirs = array();
        foreach ($classes as $namespace => $translationDir) {
            $reflection = new \ReflectionClass($namespace);
            $dirs[] = dirname($reflection->getFilename()) . $translationDir;
        }

        $formats = $this->getContainer()->get('nt_translations.translator')->getFormats();

        $finder = new Finder();
        $finder->files()
            ->name(sprintf('/(.*(%s)\.(%s))/', implode('|', $locales), implode('|', $formats)))
            ->in($dirs);

        $this->importTranslationFiles($finder);
    }

    /**
     * Imports application translation files.
     *
     * @param array $locales
     */
    protected function importAppTranslationFiles(array $locales)
    {
        $finder = $this->findTranslationsFiles($this->getApplication()->getKernel()->getRootDir(), $locales);
        $this->importTranslationFiles($finder);
    }

    /**
     * Imports translation files form all bundles.
     *
     * @param array $locales
     */
    protected function importBundlesTranslationFiles(array $locales)
    {
        $bundles = $this->getApplication()->getKernel()->getBundles();

        foreach ($bundles as $bundle) {
            $this->importBundleTranslationFiles($bundle, $locales);
        }
    }

    /**
     * Imports translation files form the specific bundles.
     *
     * @param BundleInterface $bundle
     * @param array $locales
     */
    protected function importBundleTranslationFiles($bundle, $locales) {
        $this->output->writeln(sprintf('<info># %s:</info>', $bundle->getName()));
        $finder = $this->findTranslationsFiles($bundle->getPath(), $locales);
        $this->importTranslationFiles($finder);
    }

    /**
     * Imports some translations files.
     *
     * @param Finder $finder
     */
    protected function importTranslationFiles($finder)
    {
        if ($finder instanceof Finder) {
            $importer = $this->getContainer()->get('nt_translations.importer.file');

            foreach ($finder as $file)  {
                $this->output->write(sprintf('<comment>Importing "%s" ... </comment>', $file->getPathname()));
                $number = $importer->import($file, $this->input->getOption('force'));
                $this->output->writeln(sprintf('<comment>%d translations</comment>', $number));
            }
        } else {
            $this->output->writeln('<comment>No file to import for managed locales.</comment>');
        }
    }

    /**
     * Return a Finder object if $path has a Resources/translations folder.
     *
     * @param string $path
     * @param array $locales
     * @return Symfony\Component\Finder\Finder
     */
    protected function findTranslationsFiles($path, array $locales)
    {
        $finder = null;

        if (preg_match('#^win#i', PHP_OS)) {
            $path = preg_replace('#'. preg_quote(DIRECTORY_SEPARATOR, '#') .'#', '/', $path);
        }

        $dir = $path.'/Resources/translations';

        if (is_dir($dir)) {
            $formats = $this->getContainer()->get('nt_translations.translator')->getFormats();

            $finder = new Finder();
            $finder->files()
                ->name(sprintf('/(.*(%s)\.(%s))/', implode('|', $locales), implode('|', $formats)))
                ->in($dir);
        }

        return $finder;
    }

    /**
     * Remove translation cache files managed locales.
     *
     */
    public function removeTranslationCache()
    {
        $locales = $this->getContainer()->getParameter('nt_translations.managed_locales');
        $this->getContainer()->get('translator')->removeLocalesCacheFiles($locales);
    }
}
