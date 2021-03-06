<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\MobileAppMeasurable;

class MeasurableSettings extends \Piwik\Plugins\WebsiteMeasurable\MeasurableSettings
{
    protected function shouldShowSettingsForType($type)
    {
        return $type === Type::ID;
    }
}
