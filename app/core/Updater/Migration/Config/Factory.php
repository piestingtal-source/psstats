<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Updater\Migration\Config;

use Piwik\Container\StaticContainer;

/**
 * Provides config migrations.
 *
 * @api
 */
class Factory
{
    /**
     * @var \DI\Container
     */
    private $container;

    /**
     * @ignore
     */
    public function __construct()
    {
        $this->container = StaticContainer::getContainer();
    }

    /**
     * Sets a configuration to the Psstats config file
     *
     * @param string $section
     * @param string $key
     * @param string $value
     * @return Set
     */
    public function set($section, $key, $value)
    {
        return $this->container->make('Piwik\Updater\Migration\Config\Set', array(
            'section' => $section,
            'key' => $key,
            'value' => $value,
        ));
    }
}
