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
 * PermissionMap handles a permission map with structured methods.
 * The Permissions object can:
 * - build a permission map with structured methods
 * - store a map in the current Config
 *
 * Applications should not rely on the internal map format, and only
 * rely on the provided interfaces.
 *
 * At present the map is a simple array following this format:
 *
 * @code
 * array(
 * 	'Namespace1' => array(
 *  	'title'=> '(language constant - display title for permission form)',
 * 		'desc'=>  '(language constant - description for permission form)',
 * 		'items'=> array(
 * 			 'Name1'=>array('id'=>(unique id), 'name'=>'(language constant - permission label)')
 * 			,'Name2'=>array('id'=>(unique id), 'name'=>'(language constant - permission label)')
 * 		)
 * 	)
 *  ,'Namespace2' => array(
 *  	'title'=> '(language constant - display title for permission form)',
 * 		'desc'=>  '(language constant - description for permission form)',
 * 		'items'=> array(
 * 			 'Name1'=>array('id'=>(unique id), 'name'=>'(language constant - permission label)')
 * 			,'Name2'=>array('id'=>(unique id), 'name'=>'(language constant - permission label)')
 * 			,'Name3'=>array('id'=>(unique id), 'name'=>'(language constant - permission label)')
 * 		)
 * 	)
 * );
 * @endcode
 *
 * @category  Xmf\Xadr\Lib\PermissionMap
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class PermissionMap
{

    private $map=array();

    /**
     * Add a permission namespace and its items to the permission map
     *
     * @param string $namespace the namespace for the permissions
     * @param array  $perms     array of title, desc and items array describing permissions
     *
     * @return void
     */
    public function addNamespace($namespace, $perms)
    {
        $this->map[$namespace] = $perms;
    }

    /**
     * get the permission map array
     *
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * renderPermissionForm renders a permission form from a permission map
     *
     * @param array $map a permission map.
     *
     * @return string an HTML string containing group permission form(s)
     */
    public static function renderPermissionForm($map)
    {
        global $xoopsModule;

        $module_id = $xoopsModule->getVar('mid');

        $forms=array();
        $rendered=array();

        $xadr_permissions = $map;
        foreach ($xadr_permissions as $key => $perm) {
            $title_of_form
                = defined($perm['title'])
                ? constant($perm['title']) : $perm['title'];
            $perm_name = $key;
            $perm_desc
                = defined($perm['desc']) ? constant($perm['desc']) : $perm['desc'];

            $forms[$key] = new \Xoops\Form\GroupPermissionForm(
                $title_of_form,
                $module_id,
                $perm_name,
                $perm_desc,
                '',
                false
            );
            foreach ($perm['items'] as $item) {
                $forms[$key]->addItem(
                    $item['id'],
                    defined($item['name']) ? constant($item['name']) : $item['name']
                );
            }

            $rendered[$key]=$forms[$key]->render();
        }

        $return=implode("\n<br /><br />", $rendered);

        return $return;
    }
}
