<?php

declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://n3rds.work
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser\Device;

/**
 * Class PortableMediaPlayer
 *
 * Device parser for portable media player detection
 */
class PortableMediaPlayer extends AbstractDeviceParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/device/portable_media_player.yml';

    /**
     * @var string
     */
    protected $parserName = 'portablemediaplayer';

    /**
     * @inheritdoc
     */
    public function parse(): ?array
    {
        if (!$this->preMatchOverall()) {
            return null;
        }

        return parent::parse();
    }
}
