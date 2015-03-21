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

/**
 * An Field describes the characteristics of a data element, such as name and tyoe
 *
 * @category  Xmf\Xadr\Catalog\Field
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 *
 * @method Field variableType(string $value);
 * @method Field maxLength(integer $value);
 * @method Field defaultValue(mixed $value);
 * @method Field description(string $value);
 * @method Field title(string $value);
 * @method Field shortTitle(string $value);
 * @method Field cleanerType(string $value);
 * @method Field validateDescription(string $value);
 */
class Field extends Entry
{

    /**
     * @var string type of this entry
     */
    protected $entryType = Entry::FIELD;

    /**
     * @var string type of this entry
     */
    protected $entryName = null;

    /**
     * @var \ArrayObject field properties
     */
    protected $fieldProperties;


    /**
     * @var string $cleanerType Return/cleaning type for this field, one of:
     *                          (INTEGER, FLOAT, BOOLEAN, WORD, ALNUM, CMD, BASE64,
     *                          STRING, ARRAY, PATH, USERNAME, WEBURL, EMAIL, IP)
     *
     * @see \Xoops\Core\FilterInput::clean()
     */
    /**
     * @var string form field type for this entry
     */
    // protected formTypeEntry
    // protected formTypeReadOnly

    // protected formAttributesEntry
    // protected formAttributesReadOnly


    /**
     * @param string $entryName name of this fieldset
     */
    public function __construct($entryName)
    {
        parent::__construct($entryName);
        $this->fieldProperties = new \ArrayObject(array(
            'variableType' => 'string',    // string   - type of this field (integer, string, etc.)
            'maxLength' => null,           // integer  - maximum string length for this field
            'defaultValue' => null,        // mixed    - default value for this field
            'enumValues' => null,          // array    - set of valid values for this field
            'description' => null,         // string   - description of this field
            'title' => null,               // string   - title for this field
            'shortTitle' => null,          // string   - compact title for use in grids, tight layouts
            'cleanerType' => 'string',     // string   - cleaning type, \Xoops\Core\FilterInput::clean()
            'validate' => null,            // object   - \Respect\Vailidation\Validator chain
            'validateDescription' => null, // string   - rule description (i.e. "Must be 3 digit code")
            'displayTransform' => null,    // callable - transform to apply on display
            'storeTransform'  => null,     // callable - transform to apply on entry before storing
        ));
    }

    /**
     * Get the properties set for this Field. This returns everything as an array
     * instead of individual getters or more magic. Most consumers will need everything
     * anyway, and this is simple and efficent.
     *
     * @return \ArrayObject of properties for this Field objects, indexed by name
     */
    public function getFieldProperties()
    {
        return clone $this->fieldProperties;
    }

    /**
     * Get a validator object. Each unique set of validations needs its own object.
     *
     * @return Validator
     *
     * @link https://github.com/Respect/Validation
     */
    public function newValidator()
    {
        return new Validator;
    }

    /**
     * Field property setters
     *
     * For brevity, these methods are of the form propertyName($argument)
     * and return the current Field object, fluent sytle.
     */

    /**
     * Magic setter method following the form: propertyName($argument)
     *
     * @param string $name      called name, must be a defined property name
     * @param array  $arguments call arguments, only the first argument is used
     *
     * @return Field this object
     *
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if ($this->fieldProperties->offsetExists($name)) {
            $this->fieldProperties->offsetSet($name, reset($arguments));
            return $this;
        }
        throw new \BadMethodCallException("Method {$name} does not exist");
    }

    /**
     * Set a list of values representing the only permitted values for a field.
     *
     * @param array $value a enumerated set of the only valid values for this field
     *
     * @return Field this object
     *
     * @throws \InvalidArgumentException
     */
    public function enumValues($value)
    {
        if (is_array($value)) {
            $this->fieldProperties->offsetSet('enumValues', $value);
            return $this;
        }
        throw new \InvalidArgumentException("enumValues() requires an array");
    }


    /**
     * The displayTransform is used to convert an internal format value to display format.
     * An example is transforming a unix timestamp to a localized human readable date string.
     *
     * @param Validator $value a respect/validation validator
     *
     * @return Field this object
     *
     * @link https://github.com/Respect/Validation
     *
     * @throws \InvalidArgumentException
     */
    public function validate($value)
    {
        if ($value instanceof Validator) {
            $this->fieldProperties->offsetSet('validate', $value);
            return $this;
        }
        throw new \InvalidArgumentException('validate() requires a \Respect\Validation\Validator instance');
    }

    /**
     * The displayTransform is used to convert an internal format value to display format.
     * An example is transforming a unix timestamp to a localized human readable date string.
     *
     * @param callable $value callable transform to apply on field display
     *
     * @return Field this object
     *
     * @throws \InvalidArgumentException
     */
    public function displayTransform($value)
    {
        if (is_callable($value)) {
            $this->fieldProperties->offsetSet('displayTransform', $value);
            return $this;
        }
        throw new \InvalidArgumentException("displayTransform() requires a callable");
    }

    /**
     * The storeTransform is used to convert a human readable, editable format value to
     * the format used internally for processing or storage. An example would be transforming
     * a locale formatted date to a unix timestamp.
     *
     * @param callable $value callable transform to apply on field display
     *
     * @return Field this object
     *
     * @throws \InvalidArgumentException
     */
    public function storeTransform($value)
    {
        if (is_callable($value)) {
            $this->fieldProperties->offsetSet('storeTransform', $value);
            return $this;
        }
        throw new \InvalidArgumentException("storeTransform() requires a callable");
    }
/*
variableType
maxLength
defaultValue
enumValues
description
title
shortTitle
cleanerType
validate
validateDescription
displayTransform
storeTransform
*/
}
