<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\UserLanguage\Reports;

use Piwik\Plugins\CoreVisualizations\Visualizations\Graph;

abstract class Base extends \Piwik\Plugin\Report
{
    protected function init()
    {
        $this->categoryId = 'General_Visitors';
        $this->subcategoryId = 'UserCountry_SubmenuLocations';
    }
}
