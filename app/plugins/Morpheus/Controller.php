<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\Morpheus;

use Piwik\Development;
use Piwik\Piwik;
use Piwik\View;

class Controller extends \Piwik\Plugin\Controller
{
    public function demo()
    {
        if (! Development::isEnabled() || !Piwik::isUserHasSomeAdminAccess()) {
            return;
        }

        return $this->renderTemplate('demo');
    }
}
