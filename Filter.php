<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr;

/**
 * A Filter provides a mechanism to perform additional processing in
 * response to a request, beyond the requested Action. It will be
 * invoked both before and after executuion as part of the FilterChain.
 *
 * A Filter's execute method is invoked by the FilterChain and must
 * invoke the FilterChain's execute method to advance the chain. When
 * that method returns, the filter will continue executing.
 *
 * The Controller will always add the ExecutionFilter to the end of
 * the FilterChain. This way all filters in the chain get a chance to
 * pre-process and post-process any Action.
 *
 * @category  Xmf\Xadr\Filter
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 * @since     1.0
 */
abstract class Filter extends ContextAware
{

    /**
     * An associative array of initialization parameters.
     *
     * @since  1.0
     * @type   array
     */
    protected $params = array();

    /**
     * Execute the filter.
     *
     *  _This method should never be called manually._
     *
     * All filters must include this line to advance the FilterChain:
     *
     *     $filterChain->execute();
     *
     * @param FilterChain $filterChain the filter chain object
     *
     * @return void
     * @since  1.0
     */
    abstract public function execute($filterChain);

    /**
     * Initialize the filter.
     *
     * @param array $params An associative array of initialization parameters.
     *
     * @return void
     * @since  1.0
     *
     * @todo **This does not appear to be used anywhere.** Remove
     */
    public function initialize ($params)
    {
        $this->params = array_merge($this->params, $params);
    }
}
