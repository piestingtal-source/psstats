<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @method static \Piwik\AssetManager\UIAssetCacheBuster getInstance()
 */
namespace Piwik\AssetManager;

use Piwik\Plugin\Manager;
use Piwik\Singleton;
use Piwik\Version;

class UIAssetCacheBuster extends Singleton
{
    /**
     * Cache buster based on
     *  - Piwik version
     *  - Loaded plugins (name and version)
     *  - Super user salt
     *  - Latest
     *
     * @param string[] $pluginNames
     * @return string
     */
    public function piwikVersionBasedCacheBuster($pluginNames = false)
    {
        static $cachedCacheBuster = null;

        if (empty($cachedCacheBuster) || $pluginNames !== false) {

            $masterFile     = PIWIK_INCLUDE_PATH . '/.git/refs/heads/master';
            $currentGitHash = file_exists($masterFile) ? @file_get_contents($masterFile) : null;

            $plugins = !$pluginNames ? Manager::getInstance()->getLoadedPluginsName() : $pluginNames;
            sort($plugins);

            $pluginsInfo = '';
            foreach ($plugins as $pluginName) {
                $plugin      = Manager::getInstance()->getLoadedPlugin($pluginName);
                $pluginsInfo .= $plugin->getPluginName() . $plugin->getVersion() . ',';
            }

            $cacheBuster = md5($pluginsInfo . PHP_VERSION . Version::VERSION . trim($currentGitHash));

            if ($pluginNames !== false) {
                return $cacheBuster;
            }

            $cachedCacheBuster = $cacheBuster;
        }

        return $cachedCacheBuster;
    }

    /**
     * @param string $content
     * @return string
     */
    public function md5BasedCacheBuster($content)
    {
        return md5($content);
    }
}
