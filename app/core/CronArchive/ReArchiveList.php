<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\CronArchive;

use Piwik\Concurrency\DistributedList;
use Psr\Log\LoggerInterface;

class ReArchiveList extends DistributedList
{
    const OPTION_NAME = 'ReArchiveList';

    public function __construct(LoggerInterface $logger = null)
    {
        parent::__construct(self::OPTION_NAME, $logger);
    }
}