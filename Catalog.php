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
 * Catalog - a domain implementation providing a catalog of data definitions
 *
 * @category  Xmf\Xadr\Catalog
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class Catalog extends \ArrayObject implements DomainInterface
{

    /**
     * initialize the domain - called automatically by DomainManger
     *
     * @param DomainManager $domainManager controlling DomainManager instance
     *
     * @return bool true if domain has initialized, otherwise false
     */
    public function initalize($domainManager)
    {
        return true;
    }

    /**
     * cleanup the domain - called automatically by DomainManger
     *
     * concrete implementations should cleanly close the domain
     *
     * @param DomainManager $domainManager controlling DomainManager instance
     *
     * @return bool true if domain has closed cleanly, otherwise false
     */
    public function cleanup($domainManager)
    {
        return true;
    }
}
