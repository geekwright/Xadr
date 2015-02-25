<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr\Validator;

/**
 * ChoiceValidator provides a constraint on a parameter by making sure
 * the value is or is not allowed in a list of choices.
 *
 * @category  Xmf\Xadr\Validator\Choice
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Choice extends AbstractValidator
{

    /**
     * Execute this validator.
     *
     * @param string $value parameter value - can be changed by reference.
     *
     * @return bool TRUE if the validator completes successfully, otherwise FALSE.
     */
    public function execute(&$value)
    {
        $found = false;

        if (!$this->params['sensitive']) {
            $newValue = mb_strtolower($value, 'UTF-8');
        } else {
            $newValue =& $value;
        }

        // is the value in our choices list?
        if (in_array($newValue, $this->params['choices'])) {
            $found = true;
        }

        if (($this->params['valid'] && !$found)
            || (!$this->params['valid'] && $found)
        ) {
            $this->setErrorMessage($this->params['choices_error']);
            return false;
        }

        return true;
    }

    /**
     * Initialize the validator.
     *
     * @param array $params An associative array of initialization parameters.
     *
     * @return void
     */
    public function initialize($params)
    {
        parent::initialize($params);

        if ($this->params['sensitive'] == false) {
            // strtolower all choices
            $count = count($this->params['choices']);

            for ($i = 0; $i < $count; $i++) {
                $this->params['choices'][$i] = mb_strtolower($this->params['choices'][$i], 'UTF-8');

            }

        }

    }

    /**
     * getDefaultParameters
     *
     * All expected parameters should be listed here and be given a default value
     *
     * Initialization Parameters:
     *
     * Name      | Type  | Default | Required | Description
     * --------- | ----- | ------- | -------- | -----------
     * choices   | array | n/a     | yes      | an indexed array choices
     * sensitive | bool  | FALSE   | no       | whether or not the choices are case-sensitive
     * valid     | bool  | TRUE    | no       | whether or not list of choices contains valid or invalid values
     *
     * Error Messages:
     *
     * Name          | Default
     * ------------- | -------
     * choices_error | Invalid value
     *
     * @return array of default parameters
     */
    public function getDefaultParams()
    {
        $defaults = array(
            'choices'       => array(),
            'choices_error' => 'Invalid value',
            'sensitive'     => false,
            'valid'         => true,
        );
        return $defaults;
    }
}
