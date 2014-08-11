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
 * XoopsTplRender is used by the XoopsSmartyRenderer if a render
 * mode of Xadr::RENDER_VAR (render to variable) is requested.
 *
 * @category  Xmf\Xadr\XoopsTplRender
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 */
class XoopsTplRender extends \Xmf\Template\AbstractTemplate
{
    /**
     * initialize
     *
     * @return void
     */
    protected function init()
    {

    }

    /**
     * Render the feed and display it directly
     *
     * @return void
     */
    protected function render()
    {

    }

    /**
     * Assign a template variable
     *
     * @param string $name  attribute name
     * @param string $value attribute value
     *
     * @return void
     */
    public function setAttribute($name, $value)
    {
        $this->tpl->assign($name, $value);
    }
}
