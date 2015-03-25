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
 * @category  Xmf\Xadr\Catalog\FieldValue
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class FieldValue
{

    /**
     * @var Field $field field object
     */
    protected $field = null;

    /**
     * @var mixed $value value for this field
     */
    protected $value = null;

    /**
     * @param Field $field Field object defining the field to which this value applies
     * @param mixed $value value for this field
     */
    public function __construct(Field $field, $value = null)
    {
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * set the value
     *
     * @param mixed $value value for this field
     *
     * @return void
     */
    public function set($value)
    {
        $this->value = $value;
    }

    /**
     * get the value
     *
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * get the field object
     *
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }
}
