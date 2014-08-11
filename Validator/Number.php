<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr\Validator;

/**
 * NumberValidator verifies a parameter contains only numeric characters and can
 * be constrained with minimum and maximum values.
 *
 * @category  Xmf\Xadr\Validator\Number
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 */
class Number extends AbstractValidator
{

    /**
     * Execute this validator.
     *
     * @param string &$value A user submitted parameter value.
     * @param string &$error The error message variable to be set if an error occurs.
     *
     * @return bool TRUE if the validator completes successfully, otherwise FALSE.
     */
    public function execute (&$value, &$error)
    {

        if ($this->params['strip']) {
            $value = preg_replace('/[^0-9\.\-]*/', '', $value);
            if ($value!='') {
                $value = \Xmf\FilterInput::clean($value, 'float') . '';
            }
        }

        if (!is_numeric($value)) {
            $error = $this->params['number_error'];

            return false;
        }

        if ($this->params['min'] > -1 && $value < $this->params['min']) {
            $error = $this->params['min_error'];

            return false;
        }

        if ($this->params['max'] > -1 && $value > $this->params['max']) {
            $error = $this->params['max_error'];

            return false;
        }

        return true;
    }

    /**
     * getDefaultParameters
     *
     * All expected parameters should be listed here and be given a default value
     *
     * Initialization Parameters:
     *
     * Name  | Type    | Default | Required | Description
     * ----- | ------- | ------- | -------- | --------------------
     * max   | int     | n/a     | no       | a maximum value
     * min   | int     | n/a     | no       | a minimum value
     * strip | boolean | true    | no       | strip non-numeric characters
     *
     * Error Messages:
     *
     * Name         | Default
     * ------------ | --------------
     * max_error    | Value is too high</td>
     * min_error    | Value is too low</td>
     * number_error | Value is not numeric</td>
     *
     * @return array of default parameters
     */
    public function getDefaultParams()
    {
        $defaults = array(
            'max'          => -1,
            'max_error'    => 'Value is too high',
            'min'          => -1,
            'min_error'    => 'Value is too low',
            'number_error' => 'Value is not numeric',
            'strip'        => true,
        );
        return $defaults;
    }
}
