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
 * @link      http://xoops.org
 */
class Config extends XadrArray
{
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
