<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

use Xmf\Module\Permission;

/**
 * XoopsUser implements a User object using the XOOPS user for
 * authentication and XOOPS group permissions for privileges.
 * It implements a hasPrivilege() method consistent with the
 * PrivilegeUser object, but nothing else from that class.
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
     * @var array|null array of last privilege checked, null if not checked
     */
    protected $privilege_checked;

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
        $this->secure            = array();
        $this->permissions       = array();
        $this->privilege_checked = null;
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
        return $this->privilege_checked;
    }

    /**
     * Determine if the user has a privilege.
     *
     * @param string $name      Privilege name.
     * @param string $namespace Privilege namespace.
     *
     * @return boolean TRUE, if the user has the given privilege, otherwise FALSE.
     */
    public function hasPrivilege($name, $namespace)
    {
        // reserved permission name, check admin status
        if ($name=='isAdmin') {
            return \Xoops::getInstance()->isAdmin();
        }

        $this->privilege_checked=array($name, $namespace);

        $permission = new Permission;

        $privilege = false;

        if (isset($this->permissions[$namespace]['items'][$name]['id'])) {
            $perm_id=$this->permissions[$namespace]['items'][$name]['id'];
            $privilege = $permission->checkPermission($namespace, $perm_id);
        } else {
            // this could be a per item permission
            if (is_numeric($name)) {
                $privilege = $permission->checkPermission($namespace, $name);
            }
            if (is_object($this->xoopsuser)) {
                $privilege = $this->xoopsuser->isAdmin();
            }
        }

        return $privilege;

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
