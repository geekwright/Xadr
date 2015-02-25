<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

/**
 * ExecutionChain is a list of actions to be performed
 * The Controller establishes the ExecutionChain, while the
 * ExecutionFilter processes the chain.
 *
 * The Execution chain allows access to the state of all executed
 * actions resulting from a request.
 *
 * @category  Xmf\Xadr\ExecutionChain
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class ExecutionChain
{

    /**
     * An indexed array of executed actions.
     *
     * @var array
     */
    protected $chain;

    /**
     * Create a new ExecutionChain instance.
     */
    public function __construct()
    {
        $this->chain = array();
    }

    /**
     * Add an action request to the chain.
     *
     * @param string      $unitName A unit name.
     * @param string      $actName  An action name.
     * @param Action|null $action   An Action instance.
     *
     * @return void
     */
    public function addRequest($unitName, $actName, $action)
    {
        $this->chain[] = array('unit_name'   => $unitName,
                               'action_name' => $actName,
                               'action'      => $action,
                               'microtime'   => microtime(true));
    }

    /**
     * Retrieve the Action instance at the given index.
     *
     * @param int $index The index from which you're retrieving.
     *
     * @return Action An Action instance, if the given index exists and
     *                the action was executed, otherwise NULL.
     */
    public function getAction($index)
    {
        if (count($this->chain) > $index && $index > -1) {
            return $this->chain[$index]['action'];
        }
        $null=null;

        return $null;
    }

    /**
     * Retrieve the action name associated with the request at the given index.
     *
     * @param int $index The index from which you're retrieving.
     *
     * @return string An action name, if the given index exists, otherwise NULL.
     */
    public function getActionName($index)
    {

        if (count($this->chain) > $index && $index > -1) {
            return $this->chain[$index]['action_name'];
        }

        return null;
    }

    /**
     * Retrieve the unit name associated with the request at the given index.
     *
     * @param int $index The index from which you're retrieving.
     *
     * @return string A unit name if the given index exists, otherwise NULL.
     */
    public function getUnitName($index)
    {
        if (count($this->chain) > $index && $index > -1) {
            return $this->chain[$index]['unit_name'];
        }

        return null;
    }

    /**
     * Retrieve a request and its associated data.
     *
     * @param int $index The index from which you're retrieving.
     *
     * @return array An associative array of information about an action
     *               request if the given index exists, otherwise NULL.
     */
    public function & getRequest($index)
    {
        if (count($this->chain) > $index && $index > -1) {
            return $this->chain[$index];
        }
        $null=null;

        return $null;
    }

    /**
     * Retrieve all requests and their associated data.
     *
     * @return array An indexed array of action requests.
     */
    public function & getRequests()
    {
        return $this->chain;
    }

    /**
     * Retrieve the size of the chain.
     *
     * @return int The size of the chain.
     */
    public function getSize()
    {
        return count($this->chain);
    }
}
