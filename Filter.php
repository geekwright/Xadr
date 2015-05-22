<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
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
 * @copyright 2013-2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class Filter extends ContextAware
{
    /**
     * Execute any filter logic that is to be performed before the current Action.
     *
     * @return void
     */
    public function executePreAction()
    {
    }

    /**
     * Execute any filter logic that is to be performed after the current Action.
     *
     * @return void
     */
    public function executePostAction()
    {
    }

    /**
     * Execute the filter. This is called automatically by the FilterChain.
     *
     * @param FilterChain $filterChain the filter chain object
     *
     * @return void
     */
    final public function execute(FilterChain $filterChain)
    {
        $this->executePreAction();
        $filterChain->execute();
        $this->executePostAction();
    }
}
