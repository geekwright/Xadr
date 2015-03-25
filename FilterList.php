<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

/**
 * A FilterList provides for registering a sequence of filters in a form that
 * can be added to the FilterChain. The Controller will look for classes to
 * instantiate both a global and a per-unit filter list using specific classes
 * which extend FilterList:
 *
 *  - NS\GlobalFilterList
 *  - NS\UNIT\Filters\UNITFilterList
 *
 * where NS = declared namespace, and UNIT = unit name
 *
 * The lists established in these classes will be used to create the FilterChain.
 *
 * @category  Xmf\Xadr\FilterList
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class FilterList extends ContextAware
{

    /**
     * An associative array of filters.
     *
     * @var    array
     */
    protected $filters = array();

    /**
     * initContextAware - called by ContextAware::__construct
     *
     * @return void
     */
    protected function initContextAware()
    {
        $this->initialize();
    }

    /**
     * initialize
     *
     * set filters in $this->filters, i.e.:
     *
     *   $this->filters['Name'] = $this->controller()->getFilter('Name');
     *
     * @return void
     */
    abstract protected function initialize();

    /**
     * Register filters.
     *
     *  _This method should never be called manually._
     *
     * @param FilterChain $filterChain a FilterChain instance
     *
     * @return void
     */
    public function registerFilters(FilterChain $filterChain)
    {
        // loop through filters array and register them
        foreach ($this->filters as $key => $value) {
            $filterChain->register($value);
        }
    }
}
