<?php
namespace Xmf\Xadr\Exceptions;

/**
 * RecursiveForwardException - encountered a forward to a previously forwarded action
 *
 * @category  Xmf\Xadr\Exceptions
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 XOOPS Project (http://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class RecursiveForwardException extends \LogicException
{
}
