<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Xmf\Xadr;

/**
 * XoopsView provides specific customization to a Responder object to
 * facilitate use in a XOOPS environment. Specifically:
 * - A XoopsSmartyRenderer is automatically instantiated as Renderer()
 * - (more to come)
 *
 * @category  Xmf\Xadr\XoopsResponder
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 */
abstract class XoopsResponder extends Responder
{

    protected static $renderer = null;
    protected static $form = null;

    /**
     * Renderer - obtain the renderer object
     *
     * @return object a Renderer
     */
    public function Renderer()
    {
        if (is_null(self::$renderer)) {
            self::$renderer = new XoopsSmartyRenderer($this->Context());
        }

        return self::$renderer;
    }

    /**
     * Form - obtain a Form object
     *
     * @return object a Renderer
     */
    public function Form()
    {
        if (is_null(self::$form)) {
            self::$form = new Lib\Form($this->Context());
        }

        return self::$form;
    }
}
