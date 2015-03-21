<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Xmf\Xadr\Catalog;

/**
 * Map names by adding a prefix
 *
 * @category  Xmf\Xadr\Catalog\NullMap
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class NullMap extends NameMap
{

    /**
     * map a name
     *
     * @param string $name the name to map
     *
     * @return string the mapped version of $name
     */
    public function mapName($name)
    {
        return $name;
    }
}
