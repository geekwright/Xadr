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
 * An Form describes the characteristics of an HTML Form, such as name and
 *
 * @category  Xmf\Xadr\Catalog\Form
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Form extends Entry
{

    /**
     * @var string type of this entry
     */
    protected $entryType = Entry::FORM;

    /**
     * @var string name of this form, set in __construct
     */
    protected $entryName = null;

    /**
     * @var FieldSet fields to include in this ValueSet
     */
    protected $formFieldSet = null;

    /**
     * @var string title
     */
    protected $formTitle = '';

    /**
     * @var string form action URL
     */
    protected $formAction = '';

    /**
     * @var string form method
     */
    protected $formMethod = 'post';

    /**
     * @var boolean true to add/check token for this form
     */
    protected $formUseToken = true;

    /**
     * @param FieldSet fields to include in this Form
     */
    public function formFieldSet(FieldSet $fieldSet)
    {
        $this->formFieldSet = $value;
        return $this;
    }

    /**
     * Set the form title
     *
     * @param string $value title for the form
     *
     * @return Form this object
     */
    public function formTitle($value)
    {
        $this->formTitle = $value;
        return $this;
    }

    /**
     * Set the form action
     *
     * @param string $value action (URL) for the form
     *
     * @return Form this object
     */
    public function formAction($value)
    {
        $this->formAction = $value;
        return $this;
    }

    /**
     * Set the form method
     *
     * @param string $value method for the form
     *
     * @return Form this object
     */
    public function formMethod($value)
    {
        $this->formMethod = (strtolower($value) == 'get') ? 'get' : 'post';
        return $this;
    }

    /**
     * Set the form method
     *
     * @param boolean $value true to require token, false to disable tokens
     *
     * @return Form this object
     */
    public function formUseToken($value)
    {
        $this->formUseToken = (boolean) $value;
        return $this;
    }


// hasAttribute
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
            case 'select':
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
