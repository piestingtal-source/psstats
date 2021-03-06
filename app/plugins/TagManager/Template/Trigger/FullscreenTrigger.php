<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\TagManager\Template\Trigger;

use Piwik\Settings\FieldConfig;
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NotEmpty;
use Piwik\Validators\NumberRange;

class FullscreenTrigger extends BaseTrigger
{
    public function getCategory()
    {
        return self::CATEGORY_USER_ENGAGEMENT;
    }

    public function getIcon()
    {
        return 'plugins/TagManager/images/icons/fullscreen.svg';
    }

    public function getParameters()
    {
        return array(
            $this->makeSetting('triggerAction', 'enter', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
                $field->title = 'Fire this trigger when action is';
                $field->uiControl = FieldConfig::UI_CONTROL_SINGLE_SELECT;
                $field->validators[] = new NotEmpty();
                $field->availableValues = array(
                    'any' => 'Any',
                    'enter' => 'Only when entering fullscreen',
                    'exit' => 'Only when exiting fullscreen',
                );
            }),
            $this->makeSetting('triggerLimit', 0, FieldConfig::TYPE_INT, function (FieldConfig $field) {
                $field->title = 'Trigger limit';
                $field->description = 'Enter "0" to trigger it each time the event occurs';
                $field->validators[] = new NumberRange($min = 0);
            }),
        );
    }

}
