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
 * @link      http://xoops.org
 */
class DomainManager extends ContextAware
{
    /**
     * An attributes object
     *
     * @var Attributes
     */
    protected $attributes;

    /**
     * An associative array of domain/model objects, keyed by unit and name
     *
     * @var array
     */
    protected $models=array();

    /**
     * An indexed array of loaded domains, each element as array('unit' => $unit,'name' => $name))
     *
     * @var array
     */
    protected $modelorder=array();

    /**
     * initContextAware - called by ContextAware::__construct
     *
     * @return void
     */
    protected function initContextAware()
    {
        $this->attributes = new Attributes;
    }

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
            $this->models[$unitName][$name]=$this->controller()->getDomain($name, $unitName);

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
}
