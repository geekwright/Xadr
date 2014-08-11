<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr;

/**
 * A Responder object is the presentation layer associated with an Action.
 *
 * @category  Xmf\Xadr\Responder
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class Responder extends ContextAware
{

    /**
     * Cleanup temporary responder data.
     *
     * _This method should never be called manually._
     *
     * @return void
     */
    public function cleanup()
    {

    }

    /**
     * Initialize responder
     *
     * _This method should never be called manually._
     *
     * @return void
     */
    public function initialize()
    {

    }

    /**
     * Render the presentation.
     *
     * _This method should never be called manually._
     *
     * @return Renderer A Renderer instance.
     */
    abstract public function execute();
}
