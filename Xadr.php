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
 * Xadr provides static contstants used in other Xadr classes
 *
 * @category  Xmf\Xadr\Xadr
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Xadr
{

    const RENDER_CLIENT     = 1;
    const RENDER_VAR        = 2;

    // REQUEST_* are bitmasks
    const REQUEST_NONE      = 1;
    const REQUEST_GET       = 2;
    const REQUEST_POST      = 4;
    const REQUEST_ALL       = 6;

    const RESPONSE_ALERT    = 'alert';
    const RESPONSE_CONFIRM  = 'confirm';
    const RESPONSE_ERROR    = 'error';
    const RESPONSE_INDEX    = 'index';
    const RESPONSE_INPUT    = 'input';
    const RESPONSE_NONE     =  null;
    const RESPONSE_SUCCESS  = 'success';
}
