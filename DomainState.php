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
 * The DomainState object provides a transient storage mechanism for Domain related
 * current state data, such as result sets.
 *
 * Some DomainState implementations may optinally provide persistence features as well.
 *
 * No persitence mechanism is provided, as it will vary by Domain. For most, it is not
 * required, or simple, such as storing in a session variable. More complex situations,
 * such as the state of a Workflow case that needs to remain active for an indefinite
 * period or be passed between multiple actors, may require database backing. All such
 * details are left to the implementations of Domain and save(), fetch() and expire().
 *
 * @category  Xmf\Xadr\DomainState
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class DomainState extends ContextAware
{

    /**
     * @var \ArrayObject|null $state domain state attribute store
     */
    protected $state = null;


    /**
     * initContextAware - called by ContextAware::__construct
     *
     * @return void
     */
    protected function initContextAware()
    {
        $this->state = new \ArrayObject;
    }

    /**
     * Retrieve a named state attribute
     *
     * @param string $name    name of a state attribute
     * @param mixed  $default default value returned if the requested state attribute is not set
     *
     * @return mixed current value of the named state attribute, or $default if not set
     */
    public function get($name, $default = null)
    {
        if ($this->state->offsetExists($name)) {
            return $this->state->offsetGet($name);
        }
        return $default;
    }

    /**
     * Set a named state attribute
     *
     * @param string $name  name of a state attribute
     * @param mixed  $value value to be set for the state
     *
     * @return void
     */
    public function set($name, $value)
    {
        $this->state->offsetSet($name, $value);
    }

    /**
     * Delete a named state attribute
     *
     * @param string $name name of a state attribute
     *
     * @return mixed value of the now deleted state attribute, or null if there was no prior value
     */
    public function remove($name)
    {
        $value = null;
        if ($this->state->offsetExists($name)) {
            $value = $this->state->offsetGet($name);
            $this->state->offsetUnset($name);
        }

        return $value;
    }

    /**
     * Save a named state attribute to a persistent store. This method does not
     * set the current state value, it only saves the value that is already set.
     *
     * @param string $name name of a state attribute
     *
     * @return void
     */
    public function save($name)
    {
        return;
    }

    /**
     * Fetch a named state attribute from the persistent store, and set as current
     * state value for the named attribute. This method does not return the value,
     * only the status of the fetch.
     *
     * @param string $name name of a state attribute
     *
     * @return boolean true if state attribute was restored, otherwise false
     */
    public function fetch($name)
    {
        return false;
    }

    /**
     * Delete a named state attribute from the persistent store.
     *
     * @param string $name name of a state attribute
     *
     * @return boolean true if state attribute was deleted, otherwise false
     */
    public function expire($name)
    {
        return false;
    }
}
