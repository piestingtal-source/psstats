<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\Actions\Columns;

use Piwik\Columns\Dimension;
use Piwik\Piwik;

class SearchNoResultKeyword extends Dimension
{
    protected $type = self::TYPE_TEXT;
    protected $nameSingular = 'Actions_ColumnNoResultKeyword';
}
