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
 * An collection of fields
 *
 * @category  Xmf\Xadr\Catalog\FieldSet
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 XOOPS Project (http://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class FieldSet extends Entry
{

    /**
     * @var string type of this entry
     */
    protected $entryType = Entry::FIELDSET;

    /**
     * @var string type of this entry
     */
    protected $entryName = null;

    /**
     * @var \ArrayObject list of fields to include in this entry
     */
    protected $fieldNames = null;

    /**
     * @param string   $entryName  name of this fieldset
     * @param string[] $fieldNames list of fields to include in this fieldset
     */
    public function __construct($entryName, $fieldNames)
    {
        parent::__construct($entryName);
        $this->fieldNames = new \ArrayObject($fieldNames);
    }

    /**
     * Get the field names included in this list
     *
     * @return array
     */
    public function getFieldNames()
    {
        return $this->fieldNames->getArrayCopy();
    }

    /**
     * Get the set of Fields
     *
     * @return array of Field objects, indexed by name
     */
    public function getFields()
    {
        return $this->catalog()->getEntries(Entry::FIELD, $this->fieldNames);
    }
}
