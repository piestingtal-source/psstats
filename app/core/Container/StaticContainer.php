<?php
/**
 * PS Stats - kostenlose/freie Analyseplattform
 *
 * @link https://n3rds.work
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Container;

use DI\Container;

/**
 * This class provides a static access to the container.
 *
 * @internal
 * This class is introduced only to keep BC with the current static architecture. It might be removed in a future version.
 *     - it is global state (that class makes the container a global variable)
 *     - using the container directly is the "service locator" anti-pattern (which is not dependency injection)
 */
class StaticContainer
{
    /**
     * @var Container[]
     */
    private static $containerStack = array();

    /**
     * Definitions to register in the container.
     *
     * @var array[]
     */
    private static $definitions = array();

    /**
     * @return Container
     */
    public static function getContainer()
    {
        if (empty(self::$containerStack)) {
            throw new ContainerDoesNotExistException("Der Root-Container wurde noch nicht erstellt.");
        }

        return end(self::$containerStack);
    }

    public static function clearContainer()
    {
        self::pop();
    }

    /**
     * Only use this in tests.
     *
     * @param Container $container
     */
    public static function push(Container $container)
    {
        self::$containerStack[] = $container;
    }

    public static function pop()
    {
        array_pop(self::$containerStack);
    }

    public static function addDefinitions(array $definitions)
    {
        self::$definitions[] = $definitions;
    }

    /**
     * Proxy to Container::get()
     *
     * @param string $name Container entry name.
     * @return mixed
     * @throws \DI\NotFoundException|\DI\DependencyException
     */
    public static function get($name)
    {
        return self::getContainer()->get($name);
    }

    public static function getDefinitions()
    {
        return self::$definitions;
    }
}
