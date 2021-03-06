<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\MobileAppMeasurable;

class Type extends \Piwik\Measurable\Type
{
    const ID = 'mobileapp';
    protected $name = 'MobileAppMeasurable_MobileApp';
    protected $namePlural = 'MobileAppMeasurable_MobileApps';
    protected $description = 'MobileAppMeasurable_MobileAppDescription';
    protected $howToSetupUrl = 'https://developer.psstats.org/guides/tracking-api-clients#mobile-sdks';

}

