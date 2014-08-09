<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr;

/**
 * All Action implementations must extend this class. An Action implementation
 * is used to execute business logic, which should be encapsulated in a model. A
 * model is a class that provides methods to manipulate data that is linked to
 * something, such as a database.
 *
 * @category  Xmf\Xadr\Action
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
abstract class Action extends ContextAware
{

    /**
     * Execute all business logic.
     *
     *  _This method should never be called manually._
     *
     * @return mixed A single string value describing the response
     *   (Xadr::RESPONSE_INPUT, Xadr::RESPONSE_SUCCESS, Xadr::RESPONSE_ERROR, etc.)
     *  or
     *   an indexed array coinciding with the following list:
     *    - *1st* index - unit name
     *    - *2nd* index - action name
     *    - *3rd* index - response
     */
    abstract public function execute();

    /**
     * Retrieve the default response.
     *
     * @return mixed see execute()
     */
    public function getDefaultResponse()
    {
        return Xadr::RESPONSE_INPUT;

    }

    /**
     * Retrieve the privilege required to access this action.
     *
     * @return array An indexed array coinciding with the following list:
     *                  - *1st* index - privilege name
     *                  - *2nd* index - privilege namespace (optional)
     *
     * @see    isSecure()
     */
    public function getPrivilege()
    {
        return null;

    }

    /**
     * Retrieve the HTTP request method(s) this action will serve.
     *
     * @return int A request method that is one of, or a logical OR (|)
     *             combination of the following:
     *                 - Xadr::REQUEST_GET  - serve GET requests
     *                 - Xadr::REQUEST_POST - serve POST requests
     */
    public function getRequestMethods()
    {
        return Xadr::REQUEST_GET | Xadr::REQUEST_POST;
    }

    /**
     * Handle a validation error.
     *
     * @return mixed see execute()
     */
    public function handleError()
    {
        return Xadr::RESPONSE_ERROR;
    }

    /**
     * Execute action initialization procedure.
     *
     * @return bool TRUE if action initializes successfully, otherwise FALSE.
     */
    public function initialize()
    {
        return true;
    }

    /**
     * Determine if this action requires authentication.
     *
     * @return bool TRUE if this action requires authentication, otherwise FALSE.
     */
    public function isSecure()
    {
        return false;
    }

    /**
     * Register individual parameter validators.
     *
     *  _This method should never be called manually._
     *
     * @param ValidatorManager $validatorManager A ValidatorManager instance.
     *
     * @return void
     */
    public function registerValidators(ValidatorManager $validatorManager)
    {

    }

    /**
     * Validate the request as a whole.
     *
     *  _This method should never be called manually._
     *
     * @return bool true if validation completes successfully, otherwise false.
     */
    public function validate()
    {
        return true;
    }
}
