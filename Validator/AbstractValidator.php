<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr\Validator;

use Xmf\Xadr\ContextAware;
use Xoops\Form\Element;

/**
 * A Validator is an object which validates a user submitted parameter
 * conforms to specific rules. It can also modify parameter values,
 * providing input filtering capabilities.
 *
 * @category  Xmf\Xadr\Validator\AbstractValidator
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class AbstractValidator extends ContextAware
{

    /**
     * The error message for any occuring error.
     *
     * @var string
     */
    protected $message = null;

    /**
     * An associative array of initialization parameters.
     *
     * @var array
     */
    protected $params = array();

    /**
     * Execute the validator.
     *
     *  _This method should never be called manually._
     *
     * @param string &$value parameter value - can be changed by reference.
     *
     * @return bool TRUE if the validator completes successfully, otherwise FALSE.
     */
    abstract public function execute(&$value);

    /**
     * Retrieve the default error message.
     *
     * This will return NULL unless an error message has been
     * specified with setErrorMessage()
     *
     * @return string An error message.
     */
    public function getErrorMessage()
    {
        return $this->message;
    }

    /**
     * Retrieve a parameter.
     *
     * @param string $name A parameter name.
     *
     * @return mixed parameter value if parameter exists, otherwise NULL
     */
    public function & getParameter($name)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        $ret = null;
        return $ret;
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
        $this->params = $this->getDefaultParams();
        $this->params = array_merge($this->params, $params);
    }

    /**
     * getDefaultParameters
     *
     * All expected parameters should be listed here and be given a default value
     *
     * @return array of default parameters
     */
    abstract public function getDefaultParams();

    /**
     * Set the default error message for any occuring error.
     *
     * @param string $message An error message.
     *
     * @return void
     */
    public function setErrorMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Set a validator parameter.
     *
     * @param string $name  A parameter name.
     * @param mixed  $value A parameter value.
     *
     * @return void
     */
    public function setParameter($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * Set a validator parameter by reference.
     *
     * @param string $name   A parameter name.
     * @param mixed  &$value A parameter value.
     *
     * @return void
     */
    public function setParameterByRef($name, &$value)
    {
        $this->params[$name] =& $value;
    }

    /**
     * Add client side validation to a Xoops Form Element
     *
     * This method is called as the form is being built, and can customomize attributes
     * such as pattern or add javascript if needed. The UX can be improved by screening
     * data as entered, built from the exact same parameters and rules that are used on
     * the back end.
     *
     * @param Element $element form element instance
     * @param array   $params  associative array of validation parameters.
     *
     * @return void
     */
    public function addClientSideValidation(Element $element, $params)
    {
        return;
    }
}
