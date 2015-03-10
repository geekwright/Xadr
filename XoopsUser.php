<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

/**
 * XoopsUser implements a User object using the XOOPS user for authentication
 * and XOOPS group permissions for privileges.
 *
 * Xmf\Xadr\XoopsUser is intended for use with Xmf\Xadr\XoopsAuthHandler.
 *
 * @category  Xmf\Xadr\XoopsUser
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class XoopsUser extends User
{
    /**
     * @var array of permissions that map mojavie namespace and name
     *            xoops group permission name and id
     */
    protected $permissions;

    /**
     * @var Privilege|null last checked privilege object, null if none checked
     */
    protected $privilegeChecked;

    /**
     * @var object \Xoops
     */
    protected $xoops;

    /**
     * @var object xoopsuser
     */
    protected $xoopsuser;

    /**
     * initContextAware - called by ContextAware::__construct
     *
     * @return void
     */
    protected function initContextAware()
    {
        $this->xoops = \Xoops::getInstance();

        $this->authenticated = false;
        $this->xoopsuser = null;

        if (is_object($this->xoops->user)) {
            $this->authenticated = true;
            $this->xoopsuser = $this->xoops->user;
        }
        $this->permissions       = array();
        $this->privilegeChecked = null;
    }

    /**
     * Determine the authenticated status of the user.
     *
     * @return bool TRUE if the user is authenticated, otherwise FALSE
     */
    public function isAuthenticated()
    {
        if (is_object($this->xoops->user)) {
            $this->authenticated = true;
            $this->xoopsuser = $this->xoops->user;
        }

        return $this->authenticated;
    }

    /**
     * return privilege checked on last call to hasPrivilege
     *
     * @return array|null
     */
    public function lastPrivilegeChecked()
    {
        return $this->privilegeChecked;
    }

    /**
     * Determine if the user has a privilege.
     *
     * @param Privilege $privilege a privilege object describing a required privilege
     *
     * @return boolean true if the user has the given privilege, otherwise false
     */
    public function hasPrivilege($privilege)
    {
        $this->privilegeChecked = $privilege;

        // reserved permission name, check admin status
        if ($privilege->getPrivilegeName() == 'isAdmin') {
            return \Xoops::getInstance()->isAdmin();
        }

        $modDirname = null;
        if (method_exists($this->controller(), 'getDirname')) {
            $modDirname = $this->controller()->getDirname();
        }
        $permissionHelper = new \Xmf\Module\Permission($modDirname);

        return $permissionHelper->checkPermission(
            $privilege->getPrivilegeName(),
            $privilege->getNormalizedPrivilegeItem()
        );
    }

    /**
     * Set the permission map to give symbolic names to global permissions
     *
     * @param array $permissions permission map
     *
     * @return void
     */
    public function setXoopsPermissionMap($permissions)
    {
        $this->permissions=$permissions;
    }

    // mimic a few common $xoopsUser calls for code brevity

    /**
     * get the current users id
     *
     * @return string user name
     */
    public function id()
    {
        if ($this->xoopsuser) {
            return $this->xoopsuser->id();
        }

        return 0;
    }

    /**
     * get the current users user name
     *
     * @return string user name
     */
    public function uname()
    {
        if ($this->xoopsuser) {
            return $this->xoopsuser->uname();
        }

        return $this->xoops->getConfig('anonymous');
    }
}
