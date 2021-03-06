<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\Marketplace\Input;
use Piwik\Common;

/**
 */
class Mode
{

    public function getMode()
    {
        $mode = Common::getRequestVar('mode', 'admin', 'string');

        if (!in_array($mode, array('user', 'admin'))) {
            $mode = 'admin';
        }

        return $mode;
    }

}
