<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr\Validator;

/**
 * StripValidator strips characters from a parameter.
 *
 * @category  Xmf\Xadr\Validator\Strip
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 */
class Strip extends AbstractValidator
{
    /**
     * Execute this validator.
     *
     * @param string &$value A user submitted parameter value.
     * @param string &$error The error message variable to be set if an error occurs.
     *
     * @return bool always returns TRUE
     */
    public function execute (&$value, &$error)
    {
        $length = mb_strlen($value, 'UTF-8');
        $newval = '';

        for ($i = 0; $i < $length; $i++) {
            $tmp = mb_substr($value, $i, 1, 'UTF-8');
            if (!in_array($tmp, $this->params['chars'])) {
                $newval .= $tmp;
            }
        }
        $value = $newval;

        return true;
    }

    /**
     * getDefaultParameters
     *
     * All expected parameters should be listed here and be given a default value
     *
     * Initialization Parameters:
     *
     * Name    | Type  | Default | Required | Description
     * ------- | ----- | ------- | -------- | -----------
     * chars   | array | n/a     | yes      | indexed array of characters to strip
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
            'chars' => array(),
        );
        return $defaults;
    }
}
