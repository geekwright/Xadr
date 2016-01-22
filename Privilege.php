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
 * Privilege defines a privilege needed to complete an action
 *
 * A Privilege contains two items
 * - a privilege name
 * - an item the privilege applies to, this can be a symbolic name, or an
 *   integer (usually representing the id of a protected asset)
 *
 * @category  Xmf\Xadr\Privilege
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 XOOPS Project (http://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Privilege
{

    /**
     * @var string $privilegeName the name of the privilege
     */
    protected $privilegeName = null;

    /**
     * @var string|integer|false $privilegeItem symbolic name or numeric id privilege applies to
     */
    protected $privilegeItem = null;


    /**
     * @param string $privilegeName the name of the entry being constructed
     * @param string $privilegeItem the item of the entry being constructed
     */
    public function __construct($privilegeName, $privilegeItem)
    {
        $this->privilegeName = $privilegeName;
        $this->privilegeItem = $privilegeItem;
    }

    /**
     * get the privilege name
     *
     * @return string privilege name
     */
    public function getPrivilegeName()
    {
        return $this->privilegeName;
    }

    /**
     * get the privilege item
     *
     * @return string|integer|false privilege item
     */
    public function getPrivilegeItem()
    {
        return $this->privilegeItem;
    }

    /**
     * return a privilegeItem in a normal form
     *
     * @return integer|false
     */
    public function getNormalizedPrivilegeItem()
    {
        return ((int) $this->privilegeItem > 0) ? (int) $this->privilegeItem : false;
    }
}
