<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Access\Role;

use Piwik\Access\Role;
use Piwik\Piwik;

class View extends Role
{
    public const ID = 'view';

    public function getName(): string
    {
        return Piwik::translate('UsersManager_PrivView');
    }

    public function getId(): string
    {
        return self::ID;
    }

    public function getDescription(): string
    {
        return Piwik::translate('UsersManager_PrivViewDescription');
    }

    public function getHelpUrl(): string
    {
        return 'https://n3rds.work/faq/general/faq_70/';
    }


}
