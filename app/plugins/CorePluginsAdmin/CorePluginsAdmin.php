<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CorePluginsAdmin;

use Piwik\Config;
use Piwik\Piwik;
use Piwik\Plugin;
use Piwik\Plugins\CoreHome\SystemSummary;
use Piwik\Plugins\CorePluginsAdmin\Model\TagManagerTeaser;

class CorePluginsAdmin extends Plugin
{
    /**
     * @see Plugin::registerEvents
     */
    public function registerEvents()
    {
        return array(
            'AssetManager.getJavaScriptFiles'        => 'getJsFiles',
            'AssetManager.getStylesheetFiles'        => 'getStylesheetFiles',
            'System.addSystemSummaryItems'           => 'addSystemSummaryItems',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
            'PluginManager.pluginActivated'          => 'onPluginActivated'
        );
    }

    public function onPluginActivated($pluginName)
    {
        if ($pluginName === 'TagManager') {
            // make sure once activated once, it won't appear when disabling Tag Manager later 
            $tagManagerTeaser = new TagManagerTeaser(Piwik::getCurrentUserLogin());
            $tagManagerTeaser->disableGlobally();
        }
    }

    public function addSystemSummaryItems(&$systemSummary)
    {
        $numPlugins = Plugin\Manager::getInstance()->getNumberOfActivatedPluginsExcludingAlwaysActivated();
        $systemSummary[] = new SystemSummary\Item($key = 'plugins', Piwik::translate('CoreHome_SystemSummaryNActivatedPlugins', $numPlugins), $value = null, $url = array('module' => 'CorePluginsAdmin', 'action' => 'plugins'), $icon = '', $order = 11);
    }

    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = "plugins/CorePluginsAdmin/stylesheets/plugins_admin.less";
        $stylesheets[] = "plugins/CorePluginsAdmin/angularjs/plugin-settings/plugin-settings.directive.less";
        $stylesheets[] = "plugins/CorePluginsAdmin/angularjs/form-field/field-expandable-select.less";
        $stylesheets[] = "plugins/CorePluginsAdmin/angularjs/form-field/field-multituple.less";
    }

    public static function isPluginsAdminEnabled()
    {
        return (bool) Config::getInstance()->General['enable_plugins_admin'];
    }

    public static function isPluginUploadEnabled()
    {
        return (bool) Config::getInstance()->General['enable_plugin_upload'];
    }

    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = "node_modules/jquery.dotdotdot/dist/jquery.dotdotdot.js";
        $jsFiles[] = "plugins/CoreHome/javascripts/popover.js";
    }

    public function getClientSideTranslationKeys(&$translations)
    {
        $translations[] = 'CorePluginsAdmin_NoZipFileSelected';
        $translations[] = 'CorePluginsAdmin_FileExceedsUploadLimit';
        $translations[] = 'CorePluginsAdmin_NoPluginSettings';
        $translations[] = 'CoreAdminHome_PluginSettingsIntro';
        $translations[] = 'CoreAdminHome_PluginSettingsSaveSuccess';
        $translations[] = 'General_Save';
    }

}
