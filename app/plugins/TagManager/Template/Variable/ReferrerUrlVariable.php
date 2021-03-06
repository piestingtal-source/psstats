<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Variable;

use Piwik\Settings\FieldConfig;
use Piwik\Validators\NotEmpty;

class ReferrerUrlVariable extends BaseVariable
{
    const ID = 'ReferrerUrl';

    public function getId()
    {
        return self::ID;
    }

    public function getCategory()
    {
        return self::CATEGORY_PAGE_VARIABLES;
    }

    public function getParameters()
    {
        return array(
            $this->makeSetting('urlPart', 'href', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
                $field->title = 'URL Part';
                $field->description = 'Select the part of the URL you want to retrieve.';
                $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
                $field->validators[] = new NotEmpty();
                $field->availableValues = array(
                    'href' => 'Full',
                    'host' => 'Host',
                    'hostname' => 'Host name',
                    'origin' => 'Origin',
                    'pathname' => 'Path',
                    'port' => 'Port',
                    'protocol' => 'Protocol',
                    'search' => 'Search query',
                );
            }),

        );
    }

}
