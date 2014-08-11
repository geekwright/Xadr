<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr\Validator;

/**
 * Confirm Validator provides a constraint on a parameter by ensuring
 * the value is equal to another parameters value. This is useful for
 * double entry confirmation for email addresses, account numbers, etc.
 *
 * @category  Xmf\Xadr\Validator\Confirm
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 */
class Confirm extends AbstractValidator
{

    /**
     * Execute this validator.
     *
     * @param string &$value A user submitted parameter value.
     * @param string &$error The error message variable to be set if an error occurs.
     *
     * @return bool TRUE if the validator completes successfully, otherwise FALSE.
     */
    public function execute(&$value, &$error)
    {
        $confirm = $this->Request()->getParameter($this->params['confirm']);

        if ($this->params['sensitive']) {
            $confirmed=(strcmp($value, $confirm)===0);
        } else {
            $confirmed=(strcmp(mb_strtolower($value, 'UTF-8'), mb_strtolower($confirm, 'UTF-8'))===0);
        }

        if (!$confirmed) {
            $error = $this->params['confirm_error'];
        }

        return $confirmed;
    }

    /**
     * getDefaultParameters
     *
     * All expected parameters should be listed here and be given a default value
     *
     * Initialization Parameters:
     *
     * Name          | Type   | Default | Required | Description
     * ------------- | ------ | ------- | -------- | -----------
     * confirm       | string | _n/a_   | yes      | name of parameter to match
     * sensitive     | string | TRUE    | yes      | If true, comparison is case sensitive
     *
     * Error Messages:
     *
     * Name          | Default
     * ------------- | -------
     * confirm_error | Does not match
     *
     * @return array of default parameters
     */
    public function getDefaultParams()
    {
        $defaults = array(
            'confirm'       => '',
            'confirm_error' => 'Does not match',
            'sensitive'     => true,
        );
        return $defaults;
    }
}
