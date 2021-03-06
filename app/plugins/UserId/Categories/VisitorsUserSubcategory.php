<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\UserId\Categories;

use Piwik\Category\Subcategory;
use Piwik\Piwik;

class VisitorsUserSubcategory extends Subcategory
{
    protected $categoryId = 'General_Visitors';
    protected $id = 'UserId_UserReportTitle';
    protected $order = 40;

    public function getHelp()
    {
        return '<p>' . Piwik::translate('UserId_VisitorsUserSubcategoryHelp') . '</p>';
    }
}
