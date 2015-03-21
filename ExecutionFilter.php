<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

use Xmf\Xadr\Exceptions\MissingResponderException;
use Xmf\Xadr\Exceptions\InvalidConfigurationException;

/**
 * ExecutionFilter is the main filter that controls validation,
 * action execution and response rendering.
 *
 * @category  Xmf\Xadr\ExecutionFilter
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class ExecutionFilter extends Filter
{

    /**
     * Execute this filter.
     *
     *  _This method should never be called manually._
     *
     * @param FilterChain $filterChain A FilterChain instance.
     *
     * @return void
     */
    public function execute(FilterChain $filterChain)
    {
        // retrieve current action instance
        $execChain  =  $this->controller()->getExecutionChain();
        $action     =  $execChain->getAction($execChain->getSize() - 1);
        $actionName =  $this->controller()->getCurrentAction();
        $unitName   =  $this->controller()->getCurrentUnit();

        // get current method
        $method = $this->request()->getMethod();

        // initialize the action
        if (!$action->initialize()) {
            return;
        }

        // does this action require authentication and authorization?
        if (!$this->checkAuthorization($action)) {
            return;
        }

        if (($action->getRequestMethods() & $method) != $method) {
            // This action doesn't handle the current request method, use the default
            // response. Can force this by specifying Xadr::REQUEST_NONE in getRequestMethods()
            $responseSelected = $action->getDefaultResponse();
        } else {
            // create a ValidatorManager instance
            $validManager = new ValidatorManager($this->context);

            // register individual validators
            $action->registerValidators($validManager);

            // check individual validators, and if they succeed,
            // validate entire request
            if (!$validManager->execute()
                || !$action->validate()
            ) {
                // one or more individual validators failed or
                // request validation failed
                $responseSelected = $action->getErrorResponse();
            } else {
                // execute the action
                $responseSelected = $action->execute();
            }
        }

        $responseSelected->setDefaultAction($unitName, $actionName);

        if (Xadr::RESPONSE_NONE === $responseSelected->getResponseCode()) {
            return; // nothing more to do
        }

        $this->processResponse($responseSelected);
    }

    /**
     * checkAuthorization - establish that proper authority exists to execute an action
     *
     * @param Action $action action instance
     *
     * @return boolean true if authorized, false if not authorized
     *
     * @throws Xmf\Xadr\Exceptions\InvalidConfigurationException;
     */
    protected function checkAuthorization(Action $action)
    {
        // does this action require authentication and authorization?
        if ($action->isLoginRequired()) {
            // get authorization handler and required privilege
            $authHandler = $this->controller()->getAuthorizationHandler();
            if ($authHandler === null) {
                $actionName = get_class($action);
                throw new InvalidConfigurationException(
                    "Action {$actionName} requires security but no authorization handler is set"
                );
            } elseif (!$authHandler->execute($action)) {
                // user doesn't have access
                return false;
            }
        }

        // user has authorization or no authorization is required
        return true;
    }

    /**
     * processResponder
     *
     * @param ResponseSelector $responseSelected object describing the appropriate responder.
     *
     * @return void
     *
     * @throws Xmf\Xadr\Exceptions\MissingResponderException;
     */
    protected function processResponse(ResponseSelector $responseSelected)
    {

        $responder = $this->controller()->getResponder($responseSelected);

        if ($responder === null) {
            $error = sprintf(
                "%s\\%s does not have a responder for %s",
                $responseSelected->getResponseUnit(),
                $responseSelected->getResponseAction(),
                $responseSelected->getResponseCode()
            );
            throw new MissingResponderException($error);
        }

        // execute, render and cleanup responder
        $responder->initialize();
        $renderer = $responder->execute();

        if ($renderer) {
            $renderer->execute();
            // add the renderer to the request
            $this->request()->attributes()->set('org.mojavi.renderer', $renderer);
        }

        $responder->cleanup();
    }
}
