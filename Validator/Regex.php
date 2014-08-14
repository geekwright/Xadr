<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr\Validator;

/**
 * RegexValidator provides a constraint on a parameter by making sure
 * the value is or is not matched by the supplied regular expression
 *
 * @category  Xmf\Xadr\Validator\Regex
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Regex extends AbstractValidator
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
        $match = preg_match($this->params['pattern'], $value);

        if ($this->params['match'] && !$match) {
            // pattern doesn't match
            $error = $this->params['pattern_error'];

            return false;
        } elseif (!$this->params['match'] && $match) {
            // pattern matches
            $error = $this->params['pattern_error'];

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
     * Name    | Type   | Default | Required | Description
     * ------- | ------ | ------- | -------- | ------------
     * match   | bool   | TRUE    | no       | true must match, false must not match
     * pattern | string | n/a     | yes      | pattern for preg_match
     *
     * Error Messages:
     *
     * Name          | Default
     * ------------- | ----------------------
     * pattern_error | Pattern does not match
     *
     * @return array of default parameters
     */
    public function getDefaultParams()
    {
        $defaults = array(
            'match'         => true,
            'pattern'       => null,
            'pattern_error' => 'Pattern does not match',
        );
        return $defaults;
    }
}
