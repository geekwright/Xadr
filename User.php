<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr;

/**
 * A User object provides an interface to data representing an individual
 * user, allowing for access and managment of attributes and security
 * related data.
 *
 * @category  Xmf\Xadr\User
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 */
class User extends ContextAware
{

    /**
     * The authenticated status of the user.
     *
     * @var boolean
     */
    protected $authenticated = false;

    /**
     * Security related data
     *
     * @var array()
     */
    protected $secure = array();

    /**
     * Clear all user data.
     *
     * @return void
     */
    public function clearAll()
    {
        $this->authenticated = false;
        //$this->attributes    = array();
        $this->secure        = array();
    }

    /**
     * Determine the authenticated status of the user.
     *
     * @return bool TRUE if the user is authenticated, otherwise FALSE.
     */
    public function isAuthenticated()
    {
        return ($this->authenticated === true) ? true : false;
    }

    /**
     * Set the authenticated status of the user.
     *
     * @param bool $status The authentication status.
     *
     * @return void
     */
    public function setAuthenticated($status)
    {
        $this->authenticated = $status;
    }
}
