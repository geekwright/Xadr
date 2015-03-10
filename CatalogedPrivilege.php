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

use Xmf\Xadr\Catalog\Entry;

/**
 * Privilege defines a privilege needed to complete an action
 *
 * A Privilege contains three items
 * - a privilege name
 * - an item the privilege applies to, this can be a symbolic name, or an
 *   integer (usually representing the id of a protected asset)
 * - a Catalog that holds the privilege details. The catalog should have a
 *   privilege entry with the specified privilege name
 *
 * @category  Xmf\Xadr\Privilege
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class CatalogedPrivilege extends Privilege
{

    /**
     * @var Catalog $privilegeCatalog catalog used for privilege lookups
     */
    protected $privilegeCatalog = null;


    /**
     * @param string         $privilegeName    the name of the entry being constructed
     * @param string|integer $privilegeItem    the item of the entry being constructed
     * @param Catalog        $privilegeCatalog the Catalog defining this privilege
     */
    public function __construct($privilegeName, $privilegeItem, Catalog $privilegeCatalog)
    {
        parent::__construct($privilegeName, $privilegeItem);
        $this->privilegeCatalog = $privilegeCatalog;
    }

    /**
     * return a privilegeItem in a normal form
     *
     * @return integer|false id to use in Xoops privilege lookup
     */
    public function getNormalizedPrivilegeItem()
    {
        $id = false;
        if (0 < (int) $this->privilegeItem) {
            return (int) $this->privilegeItem;
        }
        $permission = $this->privilegeCatalog->getEntry(Entry::PERMISSION, $this->privilegeName);
        if ($permission instanceof \Xmf\Xadr\Catalog\Permission) {
            $id = $permission->translateNameToItemId($this->privilegeItem);
        }
        return $id;
    }
}
