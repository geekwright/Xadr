<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr;

/**
 * Provide communications with programs outside standard web interface.
 *
 * This interface provides parameter and attribute methods similar
 * to request. This object is intended to be passed to the controller
 * with getInstance. From there, Actions and Responders can use these
 * methods to get parameters (input) and set attributes (output.)
 * Also communicates a XOOPS module directory.
 *
 * The primary envisioned use is to allow Xadr to function in a XOOPS
 * block capacity, i.e.
 * -  $externalCom->setParameterArray($options);
 * -  Xmf\Xadr\XoopsController::getInstance($externalCom) -> dispatch(unit,action);
 * -  $block = $externalCom->getAttributes();
 * but other uses are possible.
 *
 * Borrows heavily from Xmf\Xadr\Request
 *
 * @category  Xmf\Xadr\ExternalCom
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
class ExternalCom extends Request
{
    /**
     * @var string XOOPS module name (dirname)
     */
    protected $moduleName = null;

    /**
     * Create a new ExternalCom instance.
     *
     * @param string|null $moduleName name of Xoops module
     *
     * @since  1.0
     */
    public function __construct($moduleName = null)
    {
        $this->attributes =  new Attributes;
        $this->errors     =  array();
        $this->params     =  array();
        $this->setDirname($moduleName);
    }

    /**
     * Retrieve the dirname
     *
     * @return string content of $this->dirname
     *
     * @since  1.0
     */
    public function getDirname()
    {
        return $this->moduleName;
    }

    /**
     * Set the dirname
     *
     * @param string $name XOOPS module dirname
     *
     * @return void
     * @since  1.0
     */
    public function setDirname($name)
    {
        $this->moduleName = $name;
    }
}
