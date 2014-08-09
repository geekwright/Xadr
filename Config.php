<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Xmf\Xadr;

/**
 * Config provides a runtime registry for configuration options.
 *
 * Inspired by David Zülke's work in Agavi.
 *
 * @category  Xmf\Xadr\Config
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 * @since     1.0
 */
class Config extends \ArrayObject
{

    /**
     * Get a configuration value.
     *
     * @param string $name    Name of a configuration option
     * @param mixed  $default A default value returned used if the
     *                        requested named option is not set.
     *
     * @return  mixed  The value of the directive, or null if not set.
     */
    public function get($name, $default = null)
    {
        if ($this->offsetExists($name)) {
            return $this->offsetGet($name);
        } else {
            return $default;
        }
    }

    /**
     * Set a configuration value.
     *
     * @param string $name  Name of the configuration option
     * @param mixed  $value Value of the configuration option
     *
     * @return void
     */
    public function set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * Get a list of configuration values.
     *
     * @return  array  An array of confguration values
     */
    public function getConfigs()
    {
        return $this->getArrayCopy();
    }
}
