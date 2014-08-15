<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
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
 * @link      http://xoops.org
 */
class Clean extends AbstractValidator
{

    /**
     * Execute this validator.
     *
     * @param string &$value parameter value - can be changed by reference.
     *
     * @return bool always returns TRUE
     */
    public function execute (&$value)
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
