<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Tracker;

use Piwik\Config;

class TrackerConfig
{
    /**
     * Update Tracker config
     *
     * @param string $name Setting name
     * @param mixed $value Value
     */
    public static function setConfigValue($name, $value)
    {
        $section = self::getConfig();
        $section[$name] = $value;
        Config::getInstance()->Tracker = $section;
    }

    public static function getConfigValue($name)
    {
        $config = self::getConfig();
        return $config[$name];
    }

    private static function getConfig()
    {
        return Config::getInstance()->Tracker;
    }
}
