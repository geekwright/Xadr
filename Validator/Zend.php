<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr\Validator;

/**
 * Xmf\Xadr\Validator\Zend invokes a Zend framework validator
 *
 *  _This is a POC example only, and is not part of the xadr specification_
 *
 *  Adding this to the xmf composer.json and updating makes this possible:
 *
 * "require": {
 *     ...
 *     "zendframework/zend-validator" : "~2.2",
 *     "zendframework/zend-i18n" : "~2.2",
 *     "zendframework/zend-uri" : "~2.2"
 * }
 *
 * Then the $params to initialize could be specified like this to enable,
 * for example, a credit card validator:
 *
 * array('validator' => 'CreditCard')
 *
 * @category  Xmf\Xadr\Validator\Zend
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2015 XOOPS Project (http://xoops.org)
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Zend extends AbstractValidator
{

    private $zvalidator = '';

    /**
     * Execute this validator.
     *
     * @param string &$value parameter value - can be changed by reference.
     *
     * @return bool TRUE if the validator completes successfully, otherwise FALSE.
     */
    public function execute (&$value)
    {
        $class = "Zend\\Validator\\" . $this->zvalidator;
        if (class_exists($class, true)) {
            $validator = new $class($this->params);
            if (is_object($validator)) {
                if ($validator->isValid($value)) {
                    return true;
                } else {
                    $messages = $validator->getMessages();
                    $this->setErrorMessage(current($messages));
                    return false;
                }
            }
        }
        $this->setErrorMessage('Validator not found');

        return false;
    }

    /**
     * Initialize the validator. This is only required to override
     * the default error messages.
     *
     * Initialization Parameters:
     *
     * Name      | Type   | Default | Required | Description
     * --------- | ------ | ------- | -------- | ------------
     * validator | string | n/a     | yes      | Zend Validator to use
     * (key)     | mixed  | n/a     | n/a      | 'key'=>'value'
     *
     * Error Messages:
     *
     * Name        | Default
     * ----------- | --------
     * n/a         | as returned by validator
     *
     * @param array $params An associative array of initialization parameters.
     *
     * @return void
     */
    public function initialize ($params)
    {
        $this->zvalidator = '';
        $this->params = array();
        foreach ($params as $key => $value) {
            if (strcasecmp($key, 'validator')===0) {
                $this->zvalidator = $value;
            } else {
                $this->params[$key]=$value;
            }
        }
    }

    /**
     * getDefaultParameters
     *
     * All expected parameters should be listed here and be given a default value
     *
     * @return array of default parameters
     */
    public function getDefaultParams()
    {
        $defaults = array(
        );
        return $defaults;
    }
}
