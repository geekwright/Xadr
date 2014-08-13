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
 * CatalogEntry - an entry for a Catalog object
 *
 * @category  Xmf\Xadr\CatalogEntry
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class CatalogEntry extends \ArrayObject
{
    /**
     * Retrieve an entry
     *
     * @param string $name Name of an entry
     *
     * @return  CatalogEntry|null
     */
    public function getEntry($name)
    {
        if ($this->offsetExists($name)) {
            return $this->offsetGet($name);
        } else {
            $default = null;
            return $default;
        }
    }

    /**
     * Set an entry
     *
     * @param string       $name  Name of entry
     * @param CatalogEntry $value CatalogEntry object
     *
     * @return void
     */
    public function setEntry($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * Determine if an entry exists.
     *
     * @param string $name An entry name.
     *
     * @return boolean TRUE if the named entry exists, otherwise FALSE.
     */
    public function hasEntry($name)
    {
        return $this->offsetExists($name);
    }
}
