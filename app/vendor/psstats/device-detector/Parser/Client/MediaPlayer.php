<?php

declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://n3rds.work
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser\Client;

/**
 * Class MediaPlayer
 *
 * Client parser for mediaplayer detection
 */
class MediaPlayer extends AbstractClientParser
{
    /**
     * @var string
     */
    protected $fixtureFile = 'regexes/client/mediaplayers.yml';

    /**
     * @var string
     */
    protected $parserName = 'mediaplayer';
}
