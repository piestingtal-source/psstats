<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\CoreAdminHome\Commands;

use Piwik\Common;
use Piwik\Config;
use Piwik\Container\StaticContainer;
use Piwik\Date;
use Piwik\Db;
use Piwik\Plugin\ConsoleCommand;
use Piwik\Plugins\UsersManager\Model;
use Piwik\Updater;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to selectively delete visits.
 */
class MigrateTokenAuths extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('core:psstats4-migrate-token-auths');
        $this->setDescription('Only needed for the psstats 3 to psstats 4 migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        self::migrate();
        $output->writeln('Done');
    }

    public static function migrate()
    {
        $migration = StaticContainer::get(MigrationFactory::class);

        /** APP SPECIFIC TOKEN START */
        $migrations[] = $migration->db->createTable('user_token_auth', array(
            'idusertokenauth' => 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT',
            'login' => 'VARCHAR(100) NOT NULL',
            'description' => 'VARCHAR('.Model::MAX_LENGTH_TOKEN_DESCRIPTION.') NOT NULL',
            'password' => 'VARCHAR(191) NOT NULL',
            'system_token' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'hash_algo' => 'VARCHAR(30) NOT NULL',
            'last_used' => 'DATETIME NULL',
            'date_created' => ' DATETIME NOT NULL',
            'date_expired' => ' DATETIME NULL',
        ), 'idusertokenauth');
        $migrations[] = $migration->db->addUniqueKey('user_token_auth', 'password', 'uniq_password');

        $migrations[] = $migration->db->dropIndex('user', 'uniq_keytoken');

        $userModel = new Model();
        foreach ($userModel->getUsers(array()) as $user) {
            if (!empty($user['token_auth'])) {
                $migrations[] = $migration->db->insert('user_token_auth', array(
                    'login' => $user['login'],
                    'description' => 'Created by Psstats 4 migration',
                    'password' => $userModel->hashTokenAuth($user['token_auth']),
                    'date_created' => Date::now()->getDatetime(),
                    'hash_algo' => 'sha512'
                ));
            }
        }

        $migrations[] = $migration->db->dropColumn('user', 'token_auth');

        $updater = StaticContainer::get(Updater::class);
        $updater->executeMigrations(__FILE__, $migrations);
    }
}
