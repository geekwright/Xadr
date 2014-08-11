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

use Xmf\Language;
use Xmf\Xadr\Controller;

/**
 * Form provides form support using instructions found in model.
 *
 * @category  Xmf\Xadr\Lib\Form
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 */
class Form extends \Xmf\Xadr\ContextAware
{

    /**
     * initContextAware - called by ContextAware::__construct
     *
     * @return void
     */
    protected function initContextAware()
    {
        Language::load('form', 'xmf');
    }


    /**
     * build a form from a definition
     *
     * @param string $form_attribute name of Request attribute containing definition
     *
     * @return Xoops\Form\ThemeForm
     */
    protected function buildForm($form_attribute)
    {
        $errors = $this->Request()->getErrors();

        $form_definition=$this->Request()->attributes->get($form_attribute);

        $formdef=empty($form_definition['form'])? array() : $form_definition['form'];
        if (empty($formdef['name'])) {
            $formdef['name'] = 'form';
        }
        if (empty($formdef['title'])) {
            $formdef['title'] = '(Untitled)';
        }
        if (empty($formdef['action'])) {
            $formdef['action'] = '';
        }
        if (empty($formdef['method'])) {
            $formdef['method'] = 'post';
        }
        if (empty($formdef['addtoken'])) {
            $formdef['addtoken'] = true;
        }

        $fields=$form_definition['fields'];
        $elements=array();

        $form = new \Xoops\Form\ThemeForm(
            $formdef['title'],
            $formdef['name'],
            $formdef['action'],
            $formdef['method'],
            $formdef['addtoken']
        );

        foreach ($fields as $fieldname => $fielddef) {
            $value = $this->Request()->attributes->get($fieldname);
            $size=$fielddef['length'];
            $size=($size>35?30:$size);
            if ($value==null) {
                $value = $this->Request()->getParameter($fieldname, $fielddef['default']);
            }
            $value=htmlentities($value, ENT_QUOTES);
            $caption = $fielddef['description'];
            if (!empty($errors[$fieldname])) {
                $caption .= '<br /> - <span style="color:red;">'.$errors[$fieldname].'</span>';
            }
            switch ($fielddef['input']['form']) {
                case 'text':
                    $form->addElement(
                        new \Xoops\Form\Text($caption, $fieldname, $size, $fielddef['length'], $value),
                        $fielddef['required']
                    );
                    break;
                case 'editor':
                    $form->addElement(
                        new \Xoops\Form\DhtmlTextArea(
                            $caption,
                            $fieldname,
                            $value,
                            $fielddef['input']['height'],
                            $fielddef['input']['width']
                        ),
                        $fielddef['required']
                    );
                    /*
                    $form->addElement(
                        new \Xoops\Form\Editor(
                            $caption,
                            $fieldname,
                            array(
                                'editor' => 'dhtmltextarea',
                                'value' => $value,
                                'width' => $fielddef['input']['width'],
                                'height' => $fielddef['input']['height'],
                            )
                        ),
                        $fielddef['required']
                    );
                    */
                    break;
                case 'textarea':
                    $form->addElement(
                        new \Xoops\Form\TextArea(
                            $caption,
                            $fieldname,
                            $value,
                            $fielddef['input']['height'],
                            $fielddef['input']['width']
                        ),
                        $fielddef['required']
                    );
                    break;
                case 'password':
                    $form->addElement(
                        new \Xoops\Form\Password($caption, $fieldname, $size, $fielddef['length'], $value),
                        $fielddef['required']
                    );
                    break;
                case 'select':
                    $elements[$fieldname] = new \Xoops\Form\Select($caption, $fieldname, $value);
                    $elements[$fieldname] -> addOptionArray($fielddef['input']['options']);
                    $form->addElement($elements[$fieldname], $fielddef['required']);
                    break;
                case 'hidden':
                    $form->addElement(new \Xoops\Form\Hidden($fieldname, $value));
                    break;
            }
        }

        $form->addElement(
            new \Xoops\Form\Button('', 'submit', _FORM_XMF_SUBMIT, 'submit')
        );

        return $form;
    }

    /**
     * render a form
     *
     * @param string $form_attribute name of Request attribute contain definition
     *
     * @return string rendered form
     */
    public function renderForm($form_attribute)
    {
        $form=$this->buildForm($form_attribute);

        return $form->render();
    }

    /**
     * assign form to smarty template
     *
     * @param string $form_attribute name of Request attribute contain definition
     *
     * @return void
     */
    public function assignForm($form_attribute)
    {
        global $xoopsTpl;

        $form=$this->buildForm($form_attribute);
        $form->assign($xoopsTpl);
    }
}
