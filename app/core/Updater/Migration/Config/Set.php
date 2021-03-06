<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Updater\Migration\Config;

use Piwik\Config;
use Piwik\Updater\Migration;

/**
 * Sets the given configuration to Psstats config value
 */
class Set extends Migration
{
    /**
     * @var string
     */
    private $section;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;


    public function __construct($section, $key, $value)
    {
        $this->section = $section;
        $this->key = $key;
        $this->value = $value;
    }

    public function __toString()
    {
        $domain = Config::getLocalConfigPath() == Config::getDefaultLocalConfigPath() ? '' : Config::getHostname();
        $domainArg = !empty($domain) ? "--psstats-domain=\"$domain\" " : '';

        return sprintf('./console %sconfig:set --section="%s" --key="%s" --value="%s"', $domainArg, $this->section, $this->key, $this->value);
    }

    public function exec()
    {
        $config = Config::getInstance();
        $config->{$this->section}[$this->key] = $this->value;
        $config->forceSave();
    }

}
