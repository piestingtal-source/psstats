<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CoreHome\Categories;

use Piwik\Category\Subcategory;
use Piwik\Piwik;

class VisitorsOverviewSubcategory extends Subcategory
{
    protected $categoryId = 'General_Visitors';
    protected $id = 'General_Overview';
    protected $order = 2;

    public function getHelp()
    {
        return '<p>' . Piwik::translate('CoreHome_VisitorsOverviewHelp') . '</p>';
    }
}
