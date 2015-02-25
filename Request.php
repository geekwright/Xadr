<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

/**
 * The Request object hold data related to a request including the
 * parameters (user/web input) established by the Controller as well
 * as attributes and error messages established by the action as the
 * request is proccessed. Request also provides methods for accessing
 * and managing the request data.
 *
 * @category  Xmf\Xadr\Request
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Request
{

    /**
     * An attributes object.
     *
     * @var Attributes
     */
    public $attributes;

    /**
     * An associative array of errors.
     *
     * @var array
     */
    protected $errors;

    /**
     * The request method (REQUEST_GET, REQUEST_GET) used for this request.
     *
     * @var int
     */
    protected $method;

    /**
     * An associative array of user submitted parameters.
     *
     * @var array
     */
    protected $params;

    /**
     * Create a new Request instance.
     *
     * @param array $params A parsed array of user submitted parameters.
     */
    public function __construct($params)
    {
        $this->attributes =  new Attributes;
        $this->errors     =  array();
        $this->method     = ($_SERVER['REQUEST_METHOD'] == 'POST')
                            ? Xadr::REQUEST_POST : Xadr::REQUEST_GET;
        $this->params     = $params;
    }

    /**
     * Retrieve a cookie.
     *
     * @param string $name A cookie name.
     *
     * @return string A cookie value, if the cookie exists, otherwise NULL.
     */
    public function getCookie($name)
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        $null=null;

        return $null;
    }

    /**
     * Retrieve an indexed array of cookie names.
     *
     * @return array An array of cookie names.
     */
    public function getCookieNames()
    {
        return array_keys($_COOKIE);
    }

    /**
     * Retrieve an associative array of cookies.
     *
     * @return array An array of cookies.
     */
    public function & getCookies()
    {
        return $_COOKIE;
    }

    /**
     * Retrieve an error message.
     *
     * @param string $name The name under which the message has been
     *                     registered. If the error is validation related,
     *                     it will be registered under a parameter name.
     *
     * @return string An error message if a validation error occured for
     *                      a parameter or was manually set, otherwise NULL.
     */
    public function getError($name)
    {
        return (isset($this->errors[$name])) ? $this->errors[$name] : null;
    }

    /**
     * Retrieve an associative array of errors.
     *
     * @return array An array of errors, if any errors occured during validation
     *               or were manually set by the developer, otherwise NULL.
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Retrieve errors as an HTML string
     *
     * @param string $name_like restrict output to only errors with a name
     *                          starting with this string
     *
     * @return string HTML representation of errors
     */
    public function & getErrorsAsHtml($name_like = '')
    {
        $erroroutput = null;
        if ($this->hasErrors()) {
            if (empty($name_like)) {
                $errors = $this->getErrors();
            } else {
                $rawerrors = $this->getErrors();
                $errors = array();
                foreach ($rawerrors as $k => $v) {
                    if (substr($k, 0, strlen($name_like))==$name_like) {
                        $errors[$k]=$v;
                    }
                }
            }
            if (!empty($errors)) {
                $erroroutput = \Xoops::getInstance()->alert('error', $errors, 'Error');
            }
        }
        return $erroroutput;
    }

    /**
     * Retrieve the request method used for this request.
     *
     * @return int A request method that is one of the following:
     * - Xadr::REQUEST_GET  - serve GET requests
     * - Xadr::REQUEST_POST - serve POST requests
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Retrieve a user submitted parameter.
     *
     * @param string $name  A parameter name.
     * @param mixed  $value A default value.
     *
     * @return mixed A parameter value, if the given parameter exists,
     *               otherwise NULL.
     */
    public function getParameter($name, $value = null)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];

        } else {
            return $value;
        }
    }

    /**
     * Retrieve an indexed array of user submitted parameter names.
     *
     * @return array An array of parameter names.
     */
    public function getParameterNames()
    {
        return array_keys($this->params);
    }

    /**
     * Retrieve an associative array of user submitted parameters.
     *
     * @return array An array of parameters.
     */
    public function & getParameters()
    {
        return $this->params;
    }

    /**
     * Determine if a cookie exists.
     *
     * @param string $name A cookie name.
     *
     * @return bool TRUE if the given cookie exists, otherwise FALSE.
     */
    public function hasCookie($name)
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * Determine if an error has been set.
     *
     * @param string $name The name under which the message has been registered.
     *                      If the error is validation related, it will be
     *                      registered under a parameter name.
     *
     * @return bool TRUE if an error is set for the key, otherwise FALSE.
     */
    public function hasError($name)
    {
        return isset($this->errors[$name]);
    }

    /**
     * Determine if any error has been set.
     *
     * @return bool TRUE if any error has been set, otherwise FALSE.
     */
    public function hasErrors()
    {
        return (count($this->errors) > 0);
    }

    /**
     * Determine if the request has a parameter.
     *
     * @param string $name A parameter name.
     *
     * @return bool TRUE if the given parameter exists, otherwise FALSE.
     */
    public function hasParameter($name)
    {
        return isset($this->params[$name]);
    }

    /**
     * Remove a parameter.
     *
     * @param string $name A parameter name.
     *
     * @return mixed A parameter value, if the given parameter exists and has
     *               been removed, otherwise NULL.
     */
    public function removeParameter($name)
    {
        if (isset($this->params[$name])) {
            $value = $this->params[$name];
            unset($this->params[$name]);

            return $value;
        }
    }

    /**
     * Set an error message.
     *
     * @param string $name    The name under which to register the message.
     * @param string $message An error message.
     *
     * @return void
     */
    public function setError($name, $message)
    {
        $this->errors[$name] = $message;
    }

    /**
     * Set multiple error messages.
     *
     * @param array $errors An associative array of error messages.
     *
     * @return void
     */
    public function setErrors($errors)
    {
        $keys  = array_keys($errors);
        $count = count($keys);

        for ($i = 0; $i < $count; $i++) {
            $this->errors[$keys[$i]] = $errors[$keys[$i]];
        }
    }

    /**
     * Set the request method.
     *
     * @param int $method A request method that is one of the following:
     * - Xadr::REQUEST_GET  - serve GET requests
     * - Xadr::REQUEST_POST - serve POST requests
     *
     * @return void
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Manually set a parameter.
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
     * Manually set a parameter by reference.
     *
     * @param string $name  A parameter name.
     * @param mixed  $value A parameter value.
     *
     * @return void
     */
    public function setParameterByRef($name, &$value)
    {
        $this->params[$name] =& $value;
    }

    /**
     * Manually set all parameters at once by overwriting with array.
     *
     * @param array $value A parameter array
     *
     * @return void
     */
    public function setParameterArray(&$value)
    {
        $this->params = $value;
    }
}
