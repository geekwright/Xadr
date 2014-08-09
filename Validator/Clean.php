<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr\Validator;

/**
 * Clean validator cleans parameter of various nastiness and conforms
 * it to a specified type
 *
 * @category  Xmf\Xadr\Validator\Clean
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 * @since     1.0
 */
class Clean extends AbstractValidator
{

    /**
     * Execute this validator.
     *
     * @param string &$value A user submitted parameter value.
     * @param string &$error The error message variable to be set if an error occurs.
     *
     * @return bool always returns TRUE
     *
     * @since  1.0
     */
    public function execute (&$value, &$error)
    {
        $value = trim($value);
        $value = \Xmf\FilterInput::clean($value, $this->params['type']);

        return true;
    }

    /**
     * getDefaultParameters
     *
     * All expected parameters should be listed here and be given a default value
     *
     * Initialization Parameters:
     *
     * Name    | Type   | Default | Required | Description
     * ------- | ------ | ------- | -------- | -----------
     * type    | string | default | no       | type for Xmf\FilterInput::clean()
     *
     * Error Messages:
     *
     * _none_ - this validator cannot fail
     *
     * @return array of default parameters
     */
    public function getDefaultParams()
    {
        $defaults = array(
            'type' => 'default',
        );
        return $defaults;
    }
}
