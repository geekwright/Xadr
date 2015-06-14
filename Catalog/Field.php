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
 * An Field describes the characteristics of a data element, such as name and type
 *
 * @category  Xmf\Xadr\Catalog\Field
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 XOOPS Project (http://xoops.org)
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
     * @param string $entryName name of this field
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
            'inputTransform'  => null,     // callable - transform to apply on input entry
            'formClassname'  => null,      // string   - Xoops\Form class name
            'formAttributes'  => array(),  // array    - html attributes to apply to form element
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
     * @param Validator|callable $value a respect/validation validator, or a callable expecting
     *                                  to be called as function ($fieldname, ValueSet $object)
     *
     * @return Field this object
     *
     * @see \Xmf\Xadr\Catalog\ValueSet::validateInput()
     * @link https://github.com/Respect/Validation
     *
     * @throws \InvalidArgumentException
     */
    public function validate($value)
    {
        if (($value instanceof Validator) || is_callable($value)) {
            $this->fieldProperties->offsetSet('validate', $value);
            return $this;
        }
        throw new \InvalidArgumentException(
            'validate() requires a \Respect\Validation\Validator instance or a callable'
        );
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
     * The inputTransform is used to convert a human readable, editable format value to
     * the format used internally for processing or storage. An example would be transforming
     * a locale formatted date to a unix timestamp.
     *
     * @param callable $value callable transform to apply on field display
     *
     * @return Field this object
     *
     * @throws \InvalidArgumentException
     */
    public function inputTransform($value)
    {
        if (is_callable($value)) {
            $this->fieldProperties->offsetSet('inputTransform', $value);
            return $this;
        }
        throw new \InvalidArgumentException("inputTransform() requires a callable");
    }

    /**
     * Set Html attributes to apply to a form Entry for this field
     *
     * @param array $value associative array in form attributeName => attributeValue
     *
     * @return Field this object
     *
     * @throws \InvalidArgumentException
     */
    public function formClassname($value)
    {
        if (class_exists('\Xoops\Form\\' . $value)) {
            $this->fieldProperties->offsetSet('formClassname', $value);
            return $this;
        }
        throw new \InvalidArgumentException("formClassname() requires an Form Element class name");
    }

    /**
     * Set Html attributes to apply to a form Entry for this field
     *
     * @param array $value associative array in form attributeName => attributeValue
     *
     * @return Field this object
     *
     * @throws \InvalidArgumentException
     */
    public function formAttributes($value)
    {
        if (is_array($value)) {
            $this->fieldProperties->offsetSet('formAttributes', $value);
            return $this;
        }
        throw new \InvalidArgumentException("formAttributes() requires an array");
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
inputTransform
formClass
formAttributes
*/

    /**
     * Build a Xoops\Form\Element for this field
     *
     * @param mixed  $fieldValue   value for this field
     * @param string $errorMessage any error message to be included with this form
     *
     * @return \Xoops\Form\Element|null form element, or null if element could not be built
     */
    public function buildFormElement($fieldValue, $errorMessage)
    {
        $element = null;
        $value=htmlentities($fieldValue, ENT_QUOTES);
        $caption = $this->fieldProperties['title'];
        if (!empty($errorMessage)) {
            $caption .= '<br /> - <span style="color:red;">' . $errorMessage . '</span>';
        }
        if (!isset($this->fieldProperties['formAttributes']['title'])
            && isset($this->fieldProperties['validateDescription'])
        ) {
            $this->fieldProperties['formAttributes']['title'] = $this->fieldProperties['validateDescription'];
        }
        switch ($this->fieldProperties['formClassname']) {
            case 'Text':
                $element = new \Xoops\Form\Text(
                    $caption,
                    $this->entryName,
                    2, // size
                    $this->fieldProperties['maxLength'],
                    $value
                );
                $element->addAttributes($this->fieldProperties['formAttributes']);
                break;
            case 'Editor':
                $element = new \Xoops\Form\DhtmlTextArea(
                    $caption,
                    $this->entryName,
                    $value
                );
                $element->addAttributes($this->fieldProperties['formAttributes']);
                break;
            case 'TextArea':
                $element = new \Xoops\Form\TextArea(
                    $caption,
                    $this->entryName,
                    $value
                );
                $element->addAttributes($this->fieldProperties['formAttributes']);
                break;
            case 'Password':
                $element = new \Xoops\Form\Password(
                    $caption,
                    $this->entryName,
                    2,
                    $this->fieldProperties['maxLength'],
                    $value
                );
                $element->addAttributes($this->fieldProperties['formAttributes']);
                break;
            case 'Select':
                $element = new \Xoops\Form\Select($caption, $this->entryName, $value);
                $element->addOptionArray($this->fieldProperties['enumValues']);
                $element->addAttributes($this->fieldProperties['formAttributes']);
                break;
            case 'Hidden':
                $element = new \Xoops\Form\Hidden($this->entryName, $value);
                break;
        }
        return $element;
    }
}
