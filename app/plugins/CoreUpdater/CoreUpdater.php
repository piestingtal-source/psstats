<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CoreUpdater;

use Exception;
use Piwik\API\ResponseBuilder;
use Piwik\Common;
use Piwik\Filesystem;
use Piwik\FrontController;
use Piwik\Piwik;
use Piwik\SettingsPiwik;
use Piwik\UpdateCheck;
use Piwik\Updater as PiwikCoreUpdater;
use Piwik\Version;

/**
 *
 */
class CoreUpdater extends \Piwik\Plugin
{
    /**
     * @see \Piwik\Plugin::registerEvents
     */
    public function registerEvents()
    {
        return array(
            'Request.dispatchCoreAndPluginUpdatesScreen' => 'dispatch',
            'Platform.initialized'                       => 'updateCheck',
        );
    }

    public function dispatch()
    {
        if (!SettingsPiwik::isAutoUpdateEnabled()) {
            return;
        }

        $module = Common::getRequestVar('module', '', 'string');
        $action = Common::getRequestVar('action', '', 'string');

        if ($module == 'CoreUpdater'
            // Proxy module is used to redirect users to piwik.org, should still work when Piwik must be updated
            || $module == 'Proxy'
            // Do not show update page during installation.
            || $module == 'Installation'
            || ($module == 'CorePluginsAdmin' && $action == 'deactivate')
            || ($module == 'CorePluginsAdmin' && $action == 'uninstall')
            || ($module == 'LanguagesManager' && $action == 'saveLanguage')) {
            return;
        }

        $updater = new PiwikCoreUpdater();
        $updates = $updater->getComponentsWithNewVersion(array('core' => Version::VERSION));

        if (!empty($updates)) {
            Filesystem::deleteAllCacheOnUpdate();
        }

        if ($updater->getComponentUpdates() !== null) {
            if (FrontController::shouldRethrowException()) {
                throw new Exception("Piwik and/or some plugins have been upgraded to a new version. \n" .
                    "--> Please run the update process first. See documentation: https://n3rds.work/docs/update/ \n");
            } elseif ($module === 'API' && ('' == $action || 'index' == $action))  {

                $outputFormat = strtolower(Common::getRequestVar('format', 'xml', 'string', $_GET + $_POST));
                $response = new ResponseBuilder($outputFormat);
                $e = new Exception('Database Upgrade Required. Your Psstats database is out-of-date, and must be upgraded before you can continue.');
                echo $response->getResponseException($e);
                Common::sendResponseCode(503);
                exit;

            } else {
                Piwik::redirectToModule('CoreUpdater');
            }
        }
    }

    public function updateCheck()
    {
        UpdateCheck::check();
    }
}
