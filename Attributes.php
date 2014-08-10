<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr;

/**
 * Provide a standard storage and access mechanism for attributes,
 * a a runtime registry for key/value pairs.
 *
 * @category  Xmf\Xadr\Attributes
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 */
class Attributes extends \ArrayObject
{
    /**
     * Retrieve an attribute value.
     *
     * @param string $name    Name of an attribute
     * @param mixed  $default A default value returned if the requested
     *                        named attribute is not set.
     *
     * @return  mixed  The value of the attribute, or null if not set.
     */
    public function get($name, $default = null)
    {
        if ($this->offsetExists($name)) {
            return $this->offsetGet($name);
        } else {
            return $default;
        }
    }

    /**
     * Set an attribute value.
     *
     * @param string $name  Name of the attribute option
     * @param mixed  $value Value of the attribute option
     *
     * @return void
     */
    public function set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * Get a copy of all attributes
     *
     * @return array An array of attributes
     */
    public function getAll()
    {
        return $this->getArrayCopy();
    }

    /**
     * Get a list of all attribute names
     *
     * @return string[] An array of attribute names
     */
    public function getNames()
    {
        return array_keys((array) $this);
    }

    /**
     * Determine if an attribute exists.
     *
     * @param string $name An attribute name.
     *
     * @return boolean TRUE if the given attribute exists, otherwise FALSE.
     */
    public function hasName($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * Remove an attribute.
     *
     * @param string $name An attribute name.
     *
     * @return mixed An attribute value, if the named attribute existed and
     *               has been removed, otherwise NULL.
     *
     * @since  1.0
     */
    public function remove($name)
    {
        $value = null;
        if ($this->offsetExists($name)) {
            $value = $this->offsetGet($name);
            $this->offsetUnset($name);
        }

        return $value;
    }

    /**
     * Set an attribute by reference.
     *
     * @param string $name   Name of the attribute option
     * @param mixed  &$value Value of the attribute option
     *
     * @return void
     */
    public function setByRef($name, &$value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * Replace all attribute with new set
     *
     * @param mixed $values array (or object) of new attributes
     *
     * @return array old values
     */
    public function setAll($values)
    {
        $oldValues = $this->exchangeArray($value);
        return $oldValues;
    }

    /**
     * Set multiple attributes by using an associative array
     *
     * @param array $values array of new attributes
     *
     * @return void
     */
    public function setMerge($values)
    {
        $oldValues = $this->getArrayCopy();
        $this->exchangeArray(array_merge($oldValues, $values));
    }

    /**
     * Set an element attribute array
     *
     * This allows an attribute which is an array to be built one
     * element at a time.
     *
     * @param string $stem  An attribute array name.
     * @param string $name  An attribute array item name. If empty, the
     *                      value will be appended to the end of the
     *                      array rather than added with the key $name.
     * @param mixed  $value An attribute array item value.
     *
     * @return void
     */
    public function setArrayItem($stem, $name, $value)
    {
        $newValue = array();
        if ($this->offsetExists($stem)) {
            $newValue = $this->offsetGet($stem);
            if (!is_array($newValue)) {
                $newValue = array();
            }
        }
        if (empty($name)) {
            $newValue[] = $value;
        } else {
            $newValue[$name] = $value;
        }
        $this->offsetSet($stem, $newValue);
    }
}
