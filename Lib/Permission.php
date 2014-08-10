<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Xmf\Xadr\Lib;

/**
 * Permission handles a single permission namespace in a permission map
 *
 * @category  Xmf\Xadr\Lib\Permission
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 * @since     1.0
*/
class Permission
{

    private $map=array();

    private $namespace = null;

    /**
     * Initialize a permission namespace
     *
     * @param string $namespace        the namespace being defined
     * @param string $lang_title       language constant for permission form title
     * @param string $lang_description a languge constant for form description
     *
     * @return Permission object
     */
    public static function initNamespace($namespace, $lang_title, $lang_description)
    {
        $instance = new Permission;
        $instance->namespace = $namespace;
        $instance->map=array(
            'title'=> $lang_title,
            'desc'=>  $lang_description,
            'items'=> array(),
        );

        return $instance;
    }

    /**
     * Add an item to the permission
     *
     * @param int    $id         a unique (in the namespace) integer id
     * @param string $name       the symbolic name of the permission
     * @param string $lang_label a languge constant to be use as a
     *                           label for this permission on a form
     *
     * @return Permission object
     */
    public function addItem($id, $name, $lang_label)
    {
        foreach ($this->map['items'] as $items) {
            if ($items['id']==$id) {
                trigger_error(
                    'Duplicate permission id: '.$id.':'.$this->namespace.':'.$name
                );
                return null;
            }
        }
        $this->map['items'][$name]['id']=$id;
        $this->map['items'][$name]['name']=$lang_label;

        return $this;
    }

    /**
     * getMap - return a PermissionMap namespace item
     *
     * @param PermissionMap $pmap object
     *
     * @return Permission
     */
    public function addToMap(PermissionMap $pmap)
    {
        $pmap->addNamespace($this->namespace, $this->map);
        return $this;
    }
}
