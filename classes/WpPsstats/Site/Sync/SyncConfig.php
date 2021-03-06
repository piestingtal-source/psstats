<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package psstats
 */

namespace WpPsstats\Site\Sync;

use Piwik\Config as PiwikConfig;
use WpPsstats\Bootstrap;
use WpPsstats\Logger;
use WpPsstats\ScheduledTasks;
use WpPsstats\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // if accessed directly
}

class SyncConfig
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Settings
     */
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->logger = new Logger();
        $this->settings = $settings;
    }

    public function sync_config_for_current_site()
    {
        if ($this->settings->is_network_enabled()) {
            $config = PiwikConfig::getInstance();
            $has_change = false;
            foreach ($this->get_all() as $category => $keys) {
                $cat = $config->{$category};
                if (empty($cat)) {
                    $cat = array();
                }

                if (empty($keys) && !empty($cat)) {
                    // need to unset all values
                    $has_change = true;
                    $config->{$category} = array();
                }

                if (!empty($keys)) {
                    foreach ($keys as $key => $value) {
                        if (!isset($cat[$key]) || $cat[$key] != $value) {
                            $has_change = true;
                            $cat[$key] = $value;
                            $config->{$category} = $cat;
                        }
                    }
                }
            }
            if ($has_change) {
                $config->forceSave();
            }
        }
    }

    private function get_all()
    {
        $options = $this->settings->get_global_option(Settings::NETWORK_CONFIG_OPTIONS);

        if (empty($options) || !is_array($options)) {
            $options = array();
        }

        return $options;
    }

    public function get_config_value($group, $key)
    {
        if ($this->settings->is_network_enabled()) {
            $config = $this->get_all();
            if (isset($config[$group][$key])) {
                return $config[$group][$key];
            }
        } else {
            Bootstrap::do_bootstrap();
            $config = PiwikConfig::getInstance();
            $the_group = $config->{$group};
            if (!empty($the_group) && isset($the_group[$key])) {
                return $the_group[$key];
            }
        }
    }

    public function set_config_value($group, $key, $value)
    {
        if ($this->settings->is_network_enabled()) {
            $config = $this->get_all();

            if (!isset($config[$group])) {
                $config[$group] = array();
            }
            $config[$group][$key] = $value;

            $this->settings->apply_changes(array(
                Settings::NETWORK_CONFIG_OPTIONS => $config
            ));
            // need to update all config files
            wp_schedule_single_event( time() + 5, ScheduledTasks::EVENT_SYNC );

        } elseif (!\WpPsstats::is_safe_mode()) {
            Bootstrap::do_bootstrap();
            $config = PiwikConfig::getInstance();
            $the_group = $config->{$group};
            if (empty($the_group)) {
                $the_group = array();
            }
            $the_group[$key] = $value;
            $config->{$group} = $the_group;
            $config->forceSave();
        }
    }

}
