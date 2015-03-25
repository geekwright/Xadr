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

use Respect\Validation\Validator;
use Xoops\Core\FilterInput;

/**
 * A set of values for a fieldset
 *
 * @category  Catalog\ValueSet
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class ValueSet
{

    /**
     * @var FieldValue[] $values array of FieldValue objects index by field name
     */
    protected $fieldValues = null;

    /**
     * @var \Xmf\Xadr\Catalog $catalog the Catalog the FieldSet came from
     */
    protected $catalog = null;

    /**
     * @param FieldSet $fieldSet fields to include in this ValueSet
     */
    public function __construct(FieldSet $fieldSet)
    {
        $this->catalog = $fieldSet->catalog();
        $fields = $fieldSet->getFields();
        foreach ($fields as $name => $field) {
            $this->fieldValues[$name] = new FieldValue($field);
        }
    }

    /**
     * Gather input values for all fields, returning validation status.
     *
     * @param \ArrayObject|null $source source values to use to populate this value set
     * @param \ArrayObject|null $errors associative array of error messages keyed by Field::$entryName
     * @param NameMap|null      $map    name conversion map from $fieldSet names to $source names
     *
     * @return boolean true if input is valid, false if not
     */
    public function gatherInput(\ArrayObject $source = null, \ArrayObject $errors = null, NameMap $map = null)
    {
        $source = ($source === null) ? $this->catalog->request()->parameters() : $source;
        $errors = ($errors === null) ? $this->catalog->request()->getErrors() : $errors;
        $map = ($map === null) ? new NullMap('') : $map;

        $this->gatherCleanInput($source, $errors, $map);
        return $this->validateInput($source, $errors, $map);
    }

    /**
     * Obtain input values from source, cleaning and cropping as specified for each Field.
     * If field does not have a value, set to default.
     *
     * @param \ArrayObject $source source values to use to populate this value set
     * @param \ArrayObject $errors associative array of error messages keyed by Field::$entryName
     * @param NameMap      $map    name conversion map from $fieldSet names to $source names
     *
     * @return void
     */
    public function gatherCleanInput(\ArrayObject $source, \ArrayObject $errors, NameMap $map)
    {
        $filterInput = FilterInput::getInstance();
        foreach ($this->fieldValues as $name => $fieldValue) {
            $value = null;
            $fieldDef = $fieldValue->getField()->getFieldProperties();
            if ($source->offsetExists($map->mapName($name))) {
                $value = $filterInput->clean(
                    trim($source->offsetGet($map->mapName($name))),
                    $fieldDef['cleanerType']
                );
                if (is_int($fieldDef['maxLength'])) {
                    $value = trim(mb_substr($value, 0, $fieldDef['maxLength']));
                }
            }
            // apply default if no value, or value is empty string
            if ($value === null || $value === '') {
                $value = $fieldDef['defaultValue'];
            }
            $fieldValue->set($value);
        }
    }

    /**
     * Obtain input values from source, cleaning and cropping as specified for each Field.
     * If field does not have a value, set to default.
     *
     * @param \ArrayObject $source source values to use to populate this value set
     * @param \ArrayObject $errors associative array of error messages keyed by Field::$entryName
     * @param NameMap      $map    name conversion map from $fieldSet names to $source names
     *
     * @return boolean true if input is valid, false if not
     */
    public function validateInput(\ArrayObject $source, \ArrayObject $errors, NameMap $map)
    {
        $errorCount = 0;
        foreach ($this->fieldValues as $name => $fieldValue) {
            $fieldDef = $fieldValue->getField()->getFieldProperties();
            $validation = $fieldDef['validate'];
            $valid = false;
            if ($validation === null) {
                $valid = true;
            } elseif ($validation instanceof Validator) {
                $valid = $validation->validate($fieldValue->get());
            } elseif (is_callable($validation)) {
                $valid = $validation($name, $this);
            }
            if (!$valid) {
                ++$errorCount;
                $errors[$map->mapName($name)] = $fieldDef['validateDescription'];
            }
        }
        return ($errorCount === 0);
    }
}
