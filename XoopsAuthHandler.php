<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr;

/**
 * The XoopsAuthHandler implements an AuthorizationHandler that
 * uses XOOPS for user authentication.
 *
 * If a user has not signed in and attempts access to a secure Action,
 * the session will redirect to the system login with the xoops_redirect
 * option set to return to reattempt the secure Action.
 *
 * @category  Xmf\Xadr\XoopsAuthHandler
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class XoopsAuthHandler extends AuthorizationHandler
{

    /**
     * Determine the user authorization status for an action request by
     * verifying against a required privilege.
     *
     *  _This should never be called manually._
     *
     * @param Action $action An Action instance.
     *
     * @return bool|null true if authorized, false otherwise
     */
    public function execute(Action $action)
    {

        if (!$this->user()->isAuthenticated() || !($this->user() instanceof XoopsUser)) {
            // restore_error_handler(); error_reporting(-1); // tough to debug
            // if we need to authenticate, do XOOPS login rather than
            // using AUTH_UNIT AUTH_ACTION conventions

            $url=$this->controller()->getControllerPath();
            if (isset($_SERVER['QUERY_STRING'])) {
                $query = \Xmf\Request::getString('QUERY_STRING', '', 'server');
                $url = $this->controller()->getControllerPath()
                    . '?' . urlencode($query);
            }
            $parts=parse_url($url);
            $url=$parts['path'].(empty($parts['query'])?'':'?'.$parts['query']);

            redirect_header(XOOPS_URL.'/user.php?xoops_redirect='.$url, 2, \XoopsLocale::E_NO_ACTION_PERMISSION);

        }

        $privilege = $action->getPrivilege();

        if (is_array($privilege) && !isset($privilege[1])) {
            // use secure unit as default namespace
            $privilege[1] = $this->Config()->get('SECURE_UNIT', 'App');
        }

        if ($privilege != null
            && !$this->user()->hasPrivilege($privilege[0], $privilege[1])
        ) {
            $secure_unit=$this->Config()->get('SECURE_UNIT', 'App');
            $secure_action=$this->Config()->get('SECURE_ACTION', 'NoPermission');
            // user doesn't have privilege to access
            if ($this->controller()->actionExists($secure_unit, $secure_action)) {
                $this->controller()->forward($secure_unit, $secure_action);

                return false;
            }

            // TODO: find a more graceful solution to this situation
            // cannot find secure action
            $error = 'Invalid configuration setting(s): ' .
                     'SECURE_UNIT (' . $secure_unit . ') or ' .
                     'SECURE_ACTION (' . $secure_action . ')';

            trigger_error($error, E_USER_ERROR);

            exit;

        }

        // user is authenticated, and has the required privilege or a privilege
        // is not required
        return true;

    }
}
