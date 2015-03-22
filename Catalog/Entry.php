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
    /** entryType constants */
    const FIELD      = 'Field';
    const FIELDSET   = 'FieldSet';
    const NAMEMAP    = 'NameMap';
    const PERMISSION = 'Permission';

    /**
     * @var string type of this entry
     */
    protected $entryType = null;

    /**
     * @var string type of this entry
     */
    protected $entryName = null;

    /**
     * @var \Xmf\Xadr\Catalog|null catalog this entry belongs to
     */
    protected $sourceCatalog = null;

    /**
     * @param string $entryName the name of the entry being constructed
     */
    public function __construct($entryName)
    {
        $this->entryName = $entryName;
    }

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
     * @param \Xmf\Xadr\Catalog|null $catalog Catalog to inject, omit to return current catalog
     *
     * @return \Xmf\Xadr\Catalog
     *
     * @throws InvalidCatalogException
     */
    public function catalog($catalog = null)
    {
        if (($catalog === null)) {
            if ($this->sourceCatalog === null) {
                throw new InvalidCatalogException('Entry is not part of a catalog');
            }
            return $this->sourceCatalog;
        }
        if (!($catalog instanceof \Xmf\Xadr\Catalog)) {
            throw new InvalidCatalogException('Invalid catalog');
        }
        $this->sourceCatalog = $catalog;
        return $this->sourceCatalog;
    }
}
