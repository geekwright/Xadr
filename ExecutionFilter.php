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
                } elseif (!$authHandler->execute($action)) {
                    // user doesn't have access
                    return;
                }

                // user has access or no authorization handler has been set

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

            if (is_array($actResponse)) {
                // use another action for response
                $responseUnit = $actResponse[0];
                $responseAct  = $actResponse[1];
                $responseName = $actResponse[2];
            } else {
                // use current action for response
                $responseUnit = $unitName;
                $responseAct  = $actName;
                $responseName = $actResponse;
            }

            if ($responseName != Xadr::RESPONSE_NONE) {
                if (!$this->controller()->responseExists($responseUnit, $responseAct, $responseName)) {
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
                $responder
                    = $this->controller()->getResponder($responseUnit, $responseAct, $responseName);
                $responder->initialize();
                $renderer = $responder->execute();

                if ($renderer) {
                    $renderer->execute();
                }
                $responder->cleanup();

                // add the renderer to the request
                $this->request()->attributes->setByRef('org.mojavi.renderer', $renderer);

            }

        }

    }
}
