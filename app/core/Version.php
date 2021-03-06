<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik;

/**
 * Psstats version information.
 *
 * @api
 */
final class Version
{
    /**
     * The current Psstats version.
     * @var string
     */
    const VERSION = '4.3.6-rc1';
    const MAJOR_VERSION = 4;

    public function isStableVersion($version)
    {
        return (bool) preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $version);
    }

    public function isVersionNumber($version)
    {
        return $this->isStableVersion($version) || $this->isNonStableVersion($version);
    }

    private function isNonStableVersion($version)
    {
        return (bool) preg_match('/^(\d+)\.(\d+)\.(\d+)-.{1,4}(\d+)$/', $version);
    }
}
