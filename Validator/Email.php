<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr\Validator;

/**
 * Email Validator verifies an email address has a correct format.
 *
 * @category  Xmf\Xadr\Validator\Email
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Email extends AbstractValidator
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
        $value=trim($value);

        if (!\Xoops::getInstance()->checkEmail($value)) { // use XOOPS function

            $error = $this->params['email_error'];

            return false;

        }

        $length = strlen($value);

        if ($this->params['min'] > -1 && $length < $this->params['min']) {

            $error = $this->params['min_error'];

            return false;

        }

        if ($this->params['max'] > -1 && $length > $this->params['max']) {

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
     * Name | Type | Default | Required | Description
     * ---- | ---- | ------- | -------- | ------------
     * max  | int  | n/a     | no       | a maximum length
     * min  | int  | n/a     | no       | a minimum length
     *
     * Error Messages:
     *
     * Name        | Default
     * ----------- | --------
     * email_error | Invalid email address
     * max_error   | Email address is too long
     * min_error   | Email address is too short
     *
     * @return array of default parameters
     */
    public function getDefaultParams()
    {
        $defaults = array(
            'email_error' => 'Invalid email address',
            'max'         => -1,
            'max_error'   => 'Email address is too long',
            'min'         => -1,
            'min_error'   => 'Email address is too short',
        );
        return $defaults;
    }
}
