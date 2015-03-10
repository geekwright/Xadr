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

use Xmf\Xadr\Catalog\Entry;
use Xmf\Xadr\Exceptions\InvalidCatalogEntryException;

/**
 * Catalog - a domain implementation providing a catalog of data definitions
 *
 * @category  Xmf\Xadr\Catalog
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class Catalog extends Domain implements \ArrayAccess, \IteratorAggregate, \Countable
{

    /**
     * Create a new entry, and add to catalog
     *
     * @param string $typeClass Fully Qualified Class Name for the new entry
     * @param string $name      Name of the new entry entry
     *
     * @return Entry the new entry
     *
     * @throws InvalidCatalogEntryException
     */
    public function newEntry($typeClass, $name)
    {
        $varArgs = func_get_args();
        array_shift($varArgs); // pull off $type
        array_shift($varArgs); // pull off $name

        if (!class_exists($typeClass)) {
            throw new InvalidCatalogEntryException(sprintf('Unknown class %s', $typeClass));
        }

        $count = count($varArgs);
        switch ($count) {
            case 0:
                $entry = new $typeClass($name);
                break;
            case 1:
                $entry = new $typeClass($name, $varArgs[0]);
                break;
            case 2:
                $entry = new $typeClass($name, $varArgs[0], $varArgs[1]);
                break;
            case 3:
                $entry = new $typeClass($name, $varArgs[0], $varArgs[1], $varArgs[2]);
                break;
            default:
                throw new InvalidCatalogEntryException('Failed to create catalog entry');
        }
        $this->addEntry($entry);
        return $entry;
    }

    /**
     * Retrieve an entry
     *
     * @param string $type Type of an entry
     * @param string $name Name of an entry
     *
     * @return Entry|null
     */
    public function getEntry($type, $name)
    {
        $entryName = $this->buildCatalogKey($type, $name);
        if ($this->offsetExists($entryName)) {
            return $this->offsetGet($entryName);
        } else {
            $default = null;
            return $default;
        }
    }

    /**
     * Retrieve a set of entries for a type
     *
     * @param string                $type  Type of an entry
     * @param \ArrayObject|string[] $names Name of an entry
     *
     * @return Entry[]
     */
    public function getEntries($type, $names)
    {
        $return = array();

        foreach ($names as $name) {
            $return[$name] = $this->getEntry($type, $name);
        }

        return $return;
    }

    /**
     * Set a catalog entry
     *
     * @param Entry   $entry   Catalog Entry object
     * @param boolean $replace Replace any existing object of this type
     *
     * @return void
     */
    public function addEntry(Entry $entry, $replace = false)
    {
        $entryName = $this->buildCatalogKey($entry->getEntryType(), $entry->getEntryName());
        if ($replace == false && $this->offsetExists($entryName)) {
            throw new InvalidCatalogEntryException(sprintf('Duplicate entry %s', $entryName));
        }
        $this->offsetSet($entryName, $entry);
    }

    /**
     * Determine if an entry exists.
     *
     * @param string $type Type of an entry
     * @param string $name An entry name.
     *
     * @return boolean TRUE if the named entry exists, otherwise FALSE.
     */
    public function hasEntry($type, $name)
    {
        $entryName = $this->buildCatalogKey($type, $name);
        return $this->offsetExists($entryName);
    }

    /**
     * Build a key for a type and name
     *
     * @param string $type Type of an entry
     * @param string $name An entry name.
     *
     * @return string internal catalog key
     */
    public function buildCatalogKey($type, $name)
    {
        $entryName = $type . '/' . $name;
        return $entryName;
    }

    /* DomainInterface */

    /**
     * initialize the domain - called automatically by DomainManger
     *
     * concrete implementations should build the catalog here
     *
     * @return bool true if domain has initialized, otherwise false
     */
    public function initialize()
    {
        return true;
    }

    /**
     * cleanup the domain - called automatically by DomainManger
     *
     * concrete implementations should cleanly close the domain
     *
     * @return bool true if domain has closed cleanly, otherwise false
     */
    public function cleanup()
    {
        return true;
    }

    /**
     * @var array $cataglog  where the catalog is stored
     */
    protected $state = null;

    /**
     * Access the DomainState object for this Domain
     *
     * @return DomainState object
     */
    public function state()
    {
        if ($this->state===null) {
            $this->state = new Catalog\State($this->context());
        }

        return $this->state;
    }

    /* ArrayAccess interface */

    /**
     * @var array $cataglog  where the catalog is stored
     */
    protected $catalog = array();

    /**
     * store value
     *
     * @param string $offset catalog entry name
     * @param Entry  $value  entry to store
     *
     * @return void
     *
     * @throws InvalidCatalogEntryException
     */
    public function offsetSet($offset, $value)
    {
        if (!(empty($offset)) && ($value instanceof \Xmf\Xadr\Catalog\Entry)) {
            $value->catalog($this);
            $this->catalog[$offset] = $value;
        } else {
            throw new InvalidCatalogEntryException('Attempt to store invalid entry in the catalog');
        }
    }

    /**
     * test if there is a value at an offset
     *
     * @param string $offset catalog entry name
     *
     * @return boolean true if offset issset, false otherwise
     */
    public function offsetExists($offset)
    {
        return isset($this->catalog[$offset]);
    }

    /**
     * remove an entry
     *
     * @param string $offset catalog entry name
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->catalog[$offset]);
    }

    /**
     * get value
     *
     * @param string $offset catalog entry name
     *
     * @return Entry|null
     */
    public function offsetGet($offset)
    {
        return isset($this->catalog[$offset]) ? $this->catalog[$offset] : null;
    }

    /* IteratorAggregate interface */

    /**
     * get an iterator for the catalog
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->catalog);
    }

    /* Countable interface */

    /**
     * get count of catalog entries
     *
     * @return integer
     */
    public function count()
    {
        return count($this->catalog);
    }
}
