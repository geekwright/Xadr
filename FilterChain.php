<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

/**
 * FilterChain controls the sequence of Filter execution.
 *
 * @category  Xmf\Xadr\FilterChain
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class FilterChain
{

    /**
     * The current index at which the chain is processing.
     *
     * @var integer
     */
    protected $index;

    /**
     * An indexed array of filters.
     *
     * @var array
     */
    protected $filters;

    /**
     * Create a new FilterChain instance.
     */
    public function __construct()
    {
        $this->index = -1;
        $this->filters = array();
    }

    /**
     * Execute the next filter in the chain.
     *
     *  _This method should never be called manually._
     *
     * @return void
     */
    public function execute()
    {
        if (++$this->index < count($this->filters)) {
            $this->filters[$this->index]->execute($this);
        }
    }

    /**
     * Register a filter.
     *
     * @param Filter $filter A Filter instance.
     *
     * @return void
     */
    public function register(Filter $filter)
    {
        $this->filters[] = $filter;
    }
}
