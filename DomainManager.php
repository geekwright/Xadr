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
 * A DomainManager manages the loading, start up and shut down of models.
 *
 * @category  Xmf\Xadr\DomainManager
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 * @since     1.0
 */
class DomainManager extends ContextAware
{
    /**
     * An associative array of attributes.
     *
     * @type   array
     */
    protected $attributes;

    /**
     * An associative array of domain/model objects, keyed by unit and name
     *
     * @type   array
     */
    protected $models=array();

    /**
     * An indexed array of loaded domains, each element as array('unit' => $unit,'name' => $name))
     *
     * @type   array
     */
    protected $modelorder=array();

    /**
     * Return a domain instance.
     *
     * @param string $name     - A model name.
     * @param string $unitName - A unit name, defaults to current unit
     *
     * @return Domain|null
     */
    public function loadDomain($name, $unitName = '')
    {

        if (!isset($this->models[$unitName][$name])) {
            $model = $this->Controller()->getComponentName('domain', $unitName, $name, '');

            $this->models[$unitName][$name]=$this->Controller()->getDomain($name, $unitName);

            // add to head of list - will shutdown in reverse order of adding
            array_unshift($this->modelorder, array('unit' => $unitName, 'name' => $name));

            $this->models[$unitName][$name]->initialize();

        }

        return $this->models[$unitName][$name];

    }

    /**
     * Shutdown the ModelManager
     *
     * @return void
     */
    public function shutdown()
    {
        foreach ($this->modelorder as $model) {
            $this->models[$model['unit']][$model['name']]->cleanup();
        }

    }

    /**
     * Retrieve an attribute.
     *
     * @param string $name An attribute name.
     *
     * @return mixed An attribute value, if the given attribute exists, otherwise NULL.
     */
    public function getAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return null;
    }

    /**
     * Set an attribute.
     *
     * @param string $name  An attribute name.
     * @param mixed  $value An attribute value.
     *
     * @return void
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }
}
