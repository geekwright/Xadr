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

use Xmf\Xadr\Exceptions\InvalidCatalogException;

/**
 * Manage Permission definition, symbolic names and GroupPermissionForm as a Catalog Entry
 *
 * @category  Xmf\Xadr\Catalog\Permission
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Permission extends Entry
{

    /**
     * @var string type of this entry
     */
    protected $entryType = Entry::PERMISSION;

    /**
     * @var integer[] item ids indexed by name
     */
    protected $itemId = array();

    /**
     * @var string[] item labels indexed by name
     */
    protected $itemLabel = array();

    /**
     * @var string title used for permission form
     */
    protected $formTitle = '';

    /**
     * @var string description used for permission form
     */
    protected $formDescription = '';

    /**
     * @param string $entryName       namespace of this permission
     * @param string $formTitle       title for form
     * @param string $formDescription description for form
     */
    public function __construct($entryName, $formTitle, $formDescription = '')
    {
        parent::__construct($entryName);
        $this->formTitle = $formTitle;
        $this->formDescription = $formDescription;
    }

    /**
     * Add an item to the permission
     *
     * @param int    $id    unique (in the namespace defined by entryName) integer id
     * @param string $name  symbolic name of the permission
     * @param string $label label for this permission on a form
     *
     * @return void
     */
    public function addItem($id, $name, $label)
    {
        if (false !== array_search($id, $this->itemId)) {
            throw new InvalidCatalogException(
                sprintf('Duplicate permission id: %s:%s:%s', $id, $this->entryName, $name)
            );
        }
        $this->itemId[$name]=$id;
        $this->itemLabel[$name]=$label;

        return $this;
    }

    /**
     * Translate symbolic name to the numeric gperm_itemid as expected in
     * Xoops group permission checks.
     *
     * @param string $name an item name
     *
     * @return integer|false the translated id, or false if not found in map
     */
    public function translateNameToItemId($name)
    {
        $perm_id = false;
        if (isset($this->itemId[$name])) {
            $perm_id=$this->itemId[$name];
        }
        return $perm_id;

    }

    /**
     * renders a permission form from this entry
     *
     * @param integer $module_id a module id
     *
     * @return string group permission form in HTML format
     */
    public function renderPermissionForm($module_id = 0)
    {
        if ($module_id == 0) {
            $xoopsModule = \Xoops::getInstance()->module;
            $module_id = $xoopsModule->getVar('mid');
        }

        $form = new \Xoops\Form\GroupPermissionForm(
            $this->formTitle,
            $module_id,
            $this->entryName,
            $this->formDescription,
            '',
            false
        );
        foreach ($this->itemId as $name => $id) {
            $form->addItem($id, $this->itemLabel[$name]);
        }

        return $form->render();
    }
}
