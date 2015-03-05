<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Xmf\Xadr\Catalog;

use Xmf\Xadr\Exceptions\InvalidCatalogException;

/**
 * An abstract entry for a Catalog
 *
 * @category  Xmf\Xadr\Catalog\Entry
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class Entry
{

    /**
     * @var string type of this entry
     */
    protected $entryType = null;

    /**
     * @var string type of this entry
     */
    protected $entryName = null;

    /**
     * Get entryType
     *
     * @return  string|null
     */
    public function getEntryType()
    {
        return $this->entryType;
    }

    /**
     * Get entryName
     *
     * @return  string|null
     */
    public function getEntryName()
    {
        return $this->entryName;
    }

    /**
     * Get/Set catalog
     *
     * @param Catalog|null $catalog Catalog to inject, omit to return current catalog
     *
     * @return Catalog|null
     *
     * @throws InvalidCatalogException
     */
    public function catalog($catalog = null)
    {
        static $sourceCatalog = null;

        if (($catalog === null)) {
            return $sourceCatalog;
        }
        if (!($catalog instanceof \Xmf\Xadr\Catalog)) {
            throw new InvalidCatalogException('Invalid catalog');
        }
        $sourceCatalog = $catalog;
        return $sourceCatalog;
    }
}
