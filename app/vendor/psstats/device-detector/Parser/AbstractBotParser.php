<?php

declare(strict_types=1);

/**
 * Device Detector - The Universal Device Detection library for parsing User Agents
 *
 * @link https://n3rds.work
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL v3 or later
 */

namespace DeviceDetector\Parser;

/**
 * Class AbstractBotParser
 *
 * Abstract class for all bot parsers
 */
abstract class AbstractBotParser extends AbstractParser
{
    /**
     * Enables information discarding
     */
    abstract public function discardDetails(): void;
}
