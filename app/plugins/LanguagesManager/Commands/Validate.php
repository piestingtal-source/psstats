<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link     https://n3rds.work
 * @license  http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\LanguagesManager\Commands;

use Piwik\Plugin\Manager;
use Piwik\Plugins\LanguagesManager\API;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 */
class Validate extends TranslationBase
{
    protected function configure()
    {
        $this->setName('translations:validate')
            ->setDescription('Validates translation files')
            ->addOption('username', 'u', InputOption::VALUE_OPTIONAL, 'Transifex username')
            ->addOption('password', 'p', InputOption::VALUE_OPTIONAL, 'Transifex password')
            ->addOption('slug', 's', InputOption::VALUE_OPTIONAL, 'Transifex project slug')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Force to update all plugins (even non core). Can not be used with plugin option')
            ->addOption('plugin', 'P', InputOption::VALUE_OPTIONAL, 'optional name of plugin to update translations for');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setDecorated(true);

        $start = microtime(true);

        $languages = API::getInstance()->getAvailableLanguageNames();

        $languageCodes = array();
        foreach ($languages as $languageInfo) {
            $languageCodes[] = $languageInfo['code'];
        }

        $plugin = $input->getOption('plugin');

        $pluginList = array($plugin);
        if (empty($plugin)) {
            $pluginList = self::getAllPlugins();
            array_unshift($pluginList, '');
        }

        file_put_contents(PIWIK_DOCUMENT_ROOT . '/filter.txt', '');

        foreach ($pluginList as $plugin) {

            $output->writeln("");

            // fetch base or specific plugin
            $this->fetchTranslations($input, $output, $plugin);

            $files = _glob(FetchTranslations::getDownloadPath() . DIRECTORY_SEPARATOR . '*.json');

            if (count($files) == 0) {
                $output->writeln("No translation updates available! Skipped.");
                continue;
            }

            foreach ($files as $filename) {

                $code = basename($filename, '.json');

                $command = $this->getApplication()->find('translations:set');
                $arguments = array(
                    'command' => 'translations:set',
                    '--code' => $code,
                    '--file' => $filename,
                    '--plugin' => $plugin,
                    '--validate' => PIWIK_DOCUMENT_ROOT . '/filter.txt'
                );
                $inputObject = new ArrayInput($arguments);
                $inputObject->setInteractive($input->isInteractive());
                $command->run($inputObject, $output);
            }

            $output->writeln('');
        }

        $output->writeln("Finished in " . round(microtime(true)-$start, 3) . "s");
    }

    /**
     * Returns all plugins having their own translations that are bundled in core
     * @return array
     */
    public static function getAllPlugins()
    {
        static $pluginsWithTranslations;

        if (!empty($pluginsWithTranslations)) {
            return $pluginsWithTranslations;
        }

        $pluginsWithTranslations = array();
        foreach (Manager::getPluginsDirectories() as $pluginsDir) {
            $pluginsWithTranslations = array_merge($pluginsWithTranslations, glob(sprintf('%s*/lang/en.json', $pluginsDir)));
        }
        $pluginsWithTranslations = array_map(function ($elem) {
            $replace = Manager::getPluginsDirectories();
            $replace[] = '/lang/en.json';
            return str_replace($replace, '', $elem);
        }, $pluginsWithTranslations);

        return $pluginsWithTranslations;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $plugin
     * @throws \Exception
     */
    protected function fetchTranslations(InputInterface $input, OutputInterface $output, $plugin)
    {

        $command = $this->getApplication()->find('translations:fetch');
        $arguments = array(
            'command' => 'translations:fetch',
            '--username' => $input->getOption('username'),
            '--password' => $input->getOption('password'),
            '--slug'     => $input->getOption('slug'),
            '--plugin'   => $plugin,
            '--lastupdate' => 1
        );

        $inputObject = new ArrayInput($arguments);
        $inputObject->setInteractive($input->isInteractive());
        $command->run($inputObject, $output);
    }
}
