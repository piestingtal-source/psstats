<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\LanguagesManager\Commands;

use Piwik\Plugins\LanguagesManager\API;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 */
class LanguageNames extends TranslationBase
{
    protected function configure()
    {
        $this->setName('translations:languagenames')
             ->setDescription('Shows available language names');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $languages = API::getInstance()->getAvailableLanguageNames();

        $languageNames = array();
        foreach ($languages as $languageInfo) {
            $languageNames[] = $languageInfo['english_name'];
        }

        sort($languageNames);

        $output->writeln("Currently available languages:");
        $output->writeln(implode("\n", $languageNames));
    }
}
