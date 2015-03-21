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

/**
 * Map names by adding a prefix
 *
 * @category  Xmf\Xadr\Catalog\PrefixMap
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class PrefixMap extends NameMap
{

    /**
     * @var string prefix to apply to mapped names
     */
    protected $fieldPrefix = null;

    /**
     * @param string $entryName   name of this fieldset
     * @param string $fieldPrefix prefix to apply to names
     */
    public function __construct($entryName, $fieldPrefix)
    {
        parent::__construct($entryName);
        $this->fieldPrefix = $fieldPrefix;
    }

    /**
     * map a name
     *
     * @param string $name the name to map
     *
     * @return string the mapped version of $name
     */
    public function mapName($name)
    {
        return $this->fieldPrefix . $name;
    }
}
