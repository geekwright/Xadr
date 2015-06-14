<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

/**
 * All Action implementations must extend this class. An Action implementation
 * is used to execute business logic, which should be encapsulated in a model. A
 * model is a class that provides methods to manipulate data that is linked to
 * something, such as a database.
 *
 * The methods declared here are invoked by the ExecutionFilter and are not intended
 * to be called manually.
 *
 * @category  Xmf\Xadr\Action
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2015 XOOPS Project (http://xoops.org)
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class Action extends ContextAware
{
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
     *
     * @see getRequiredPrivilege()
     */
    public function isLoginRequired()
    {
        return false;
    }

    /**
     * Retrieve the privilege required to access this action.
     *
     * Note that this will only be called if isLoginRequired() returns true.
     *
     * @return Privilege|null A Privilege object describing the required permission or
     *                        null if no specific permission is required
     *
     * @see isSecure()
     */
    public function getRequiredPrivilege()
    {
        return null;
    }

    /**
     * Establish which HTTP request methods this action will serve.
     *
     * @return integer one of the defined Xadr::REQUEST_ constants.
     */
    public function getRequestMethods()
    {
        return Xadr::REQUEST_ALL;
    }

    /**
     * Retrieve the default response, used if this action does not handle the
     * current request method.
     *
     * @return ResponseSelector object describing the appropriate responder.
     *
     * @see Xmf\Xadr\ResponseSelector
     * @see getRequestMethods()
     */
    public function getDefaultResponse()
    {
        return new ResponseSelector(Xadr::RESPONSE_INPUT);
    }

    /**
     * Register individual parameter validators.
     *
     * @param ValidatorManager $validatorManager A ValidatorManager instance.
     *
     * @return void
     */
    public function registerValidators(ValidatorManager $validatorManager)
    {

    }

    /**
     * Validate the request
     *
     * @return bool true if validation completes successfully, otherwise false.
     */
    abstract public function validate();

    /**
     * Get response type to be used if validation() returns false, indicating an error
     *
     * @return ResponseSelector object describing the appropriate responder.
     *
     * @see Xmf\Xadr\ResponseSelector
     */
    public function getErrorResponse()
    {
        return new ResponseSelector(Xadr::RESPONSE_ERROR);
    }

    /**
     * Execute all business logic.
     *
     * @return ResponseSelector object describing the appropriate responder.
     *
     * @see Xmf\Xadr\ResponseSelector
     */
    abstract public function execute();
}
