<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\CoreConsole\Commands;

use Piwik\Filesystem;
use Piwik\Plugin\ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 */
class ClearCaches extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('core:clear-caches');
        $this->setAliases(array('cache:clear'));
        $this->setDescription('Clears all caches. This command can be useful for instance after updating Psstats files manually.');
    }

    /**
     * Execute command like: ./console core:clear-caches
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Note: the logic for this command must be refactored in this helper function below.
        Filesystem::deleteAllCacheOnUpdate();

        $this->writeSuccessMessage($output, array('Caches cleared'));
    }
}
