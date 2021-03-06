<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\Goals\Categories;

use Piwik\Category\Subcategory;
use Piwik\Piwik;

class ManageGoalsSubcategory extends Subcategory
{
    protected $categoryId = 'Goals_Goals';
    protected $id = 'Goals_ManageGoals';
    protected $order = 9999;

    public function getHelp()
    {
        return '<p>' . Piwik::translate('Goals_ManageGoalsSubcategoryHelp1') . '</p>'
            . '<p><a href="https://n3rds.work/docs/tracking-goals-web-analytics/" rel="noreferrer noopener" target="_blank">' . Piwik::translate('Goals_ManageGoalsSubcategoryHelp2') . '</a></p>';
    }
}
