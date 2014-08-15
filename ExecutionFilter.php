<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

/**
 * ExecutionFilter is the main filter that does controls validation,
 * action execution and response rendering.
 *
 * @category  Xmf\Xadr\ExecutionFilter
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
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
    public function execute($filterChain)
    {
        // retrieve current action instance
        $execChain =  $this->controller()->getExecutionChain();
        $action    =  $execChain->getAction($execChain->getSize() - 1);
        $actName   =  $this->controller()->getCurrentAction();
        $unitName  =  $this->controller()->getCurrentUnit();

        // get current method
        $method = $this->request()->getMethod();

        // initialize the action
        if ($action->initialize()) {

            // does this action require authentication and authorization?
            if (!$this->checkAuthorization($action)) {
                return;
            }

            if (($action->getRequestMethods() & $method) != $method) {
                // this action doesn't handle the current request method,
                // use the default response
                $actResponse = $action->getDefaultResponse();
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
                    $actResponse = $action->handleError();
                } else {
                    // execute the action
                    $actResponse = $action->execute();
                }
            }

            $responseUnit = $unitName;
            $responseAct  = $actName;
            $responseName = $actResponse;

            if (is_array($actResponse)) {
                // use another action for response
                $responseUnit = $actResponse[0];
                $responseAct  = $actResponse[1];
                $responseName = $actResponse[2];
            }

            if ($responseName == Xadr::RESPONSE_NONE) {
                return; // nothing more to do
            }

            $responder = $this->controller()->getResponder($responseUnit, $responseAct, $responseName);

            if (!$responder) {
                $error = sprintf(
                    "%s\\%s does not have a responder for %s",
                    $responseUnit,
                    $responseAct,
                    $responseName
                );
                trigger_error($error, E_USER_ERROR);
                exit;
            }

            // execute, render and cleanup responder
            $responder->initialize();
            $renderer = $responder->execute();

            if ($renderer) {
                $renderer->execute();
                // add the renderer to the request
                $this->request()->attributes->setByRef('org.mojavi.renderer', $renderer);
            }

            $responder->cleanup();
        }
    }

    /**
     * checkAuthorization - establish that proper authority exists to execute an action
     *
     * @param Action $action action instance
     *
     * @return boolean true if authorized, false if not authorized, or cannot be determined
     */
    protected function checkAuthorization(Action $action)
    {
        // does this action require authentication and authorization?
        if ($action->isSecure()) {
            // get authorization handler and required privilege
            $authHandler = $this->controller()->getAuthorizationHandler();
            if ($authHandler === null) {
                // log invalid security notice
                trigger_error(
                    'Action requires security but no authorization ' .
                    'handler has been registered',
                    E_USER_NOTICE
                );
                return false;
            } elseif (!$authHandler->execute($action)) {
                // user doesn't have access
                return false;
            }
        }

        // user has authorization or no authorization is required
        return true;
    }
}
