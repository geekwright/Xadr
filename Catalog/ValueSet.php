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
 * A set of values for a fieldset
 *
 * @category  Xmf\Xadr\Catalog\Field
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class ValueSet extends FieldSet
{

    /**
     * @var string type of this entry
     */
    protected $entryType = Entry::VALUESET;

    /**
     * @var string type of this entry
     */
    protected $entryName = null;

    /**
     * @var array list of fields to include in this entry
     */
    protected $valueSource = null;

    /**
     * @param string   $entryName   name of this fieldset
     * @param string[] $fieldNames  list of fields to include in this fieldset
     * @param array    $valueSource array of values to be considered
     */
    public function __construct($entryName, $fieldNames, $valueSource)
    {
        parent::__construct($entryName, $fieldNames);
        $this->valueSource = $valueSource;
    }
}
