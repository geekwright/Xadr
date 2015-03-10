<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

/**
 * An ActionChain allows execution of multiple actions and retrieving
 * the rendered results from that execution. Potential uses include
 * incoporating information from external Action implementations.
 *
 * @category  Xmf\Xadr\ActionChain
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class ActionChain extends ContextAware
{

    /**
     * An associative array of actions.
     *
     * @var array
     */
    protected $actions = array();

    /**
     * Whether or not to preserve request parameters while actions are being
     * executed.
     *
     * @var boolean
     */
    protected $preserve = false;


    /**
     * Execute all registered actions.
     *
     * _This method should never be called manually._
     *
     * @return void
     */
    public function execute()
    {
        $keys  = array_keys($this->actions);
        $count = count($keys);

        // retrieve current render mode
        $renderMode = $this->controller()->getRenderMode();

        // force all actions at this point to render to variable
        $this->controller()->setRenderMode(Xadr::RENDER_VARIABLE);

        for ($i = 0; $i < $count; $i++) {
            $action =& $this->actions[$keys[$i]];

            if ($this->preserve && $action['params'] != null) {
                // make a copy of the current variables if they exist
                $params   = array();
                $subKeys  = array_keys($action['params']);
                $subCount = count($subKeys);

                for ($x = 0; $x < $subCount; $x++) {
                    if ($this->request()->hasParameter($subKeys[$x])) {
                        // do not use a reference here
                        $params[$subKeys[$x]]
                            = $this->request()->getParameter($subKeys[$x]);
                    }
                }
            }

            if ($action['params'] != null) {
                // add replacement parameters to the request
                $subKeys  = array_keys($action['params']);
                $subCount = count($subKeys);

                for ($x = 0; $x < $subCount; $x++) {
                    $this->request()->setParameterByRef(
                        $subKeys[$x],
                        $action['params'][$subKeys[$x]]
                    );
                }
            }

            // execute/forward the action and retrieve rendered result
            $this->controller()->forward($action['unit'], $action['action']);

            // retrieve renderer for action
            $renderer =& $this->request()->attributes->get('org.mojavi.renderer');

            // did the action render a view?
            if ($renderer !== null) {
                // retrieve rendered result
                $action['result'] = $renderer->fetchResult();
                // clear rendered result
                $renderer->clearResult();
                // remove renderer
                $this->request()->attributes->remove('org.mojavi.renderer');
            }

            if (isset($params)) {
                // put copies of parameters back
                $subKeys  = array_keys($params);
                $subCount = count($subKeys);
                for ($x = 0; $x < $subCount; $x++) {
                    $this->request()->setParameterByRef(
                        $subKeys[$x],
                        $params[$subKeys[$x]]
                    );
                }
                unset($params);
            }
        }

        // put the old rendermode back
        $this->controller()->setRenderMode($renderMode);
    }

    /**
     * Fetch the result of an executed action.
     *
     * @param string $regName An action registration name.
     *
     * @return string A rendered view, if the given action exists and did render
     *                a view, otherwise NULL.
     */
    public function & fetchResult($regName)
    {
        if (isset($this->actions[$regName]['result'])) {
            return $this->actions[$regName]['result'];

        }
        $null = null;

        return $null;
    }

    /**
     * Register an action with the chain.
     *
     * @param string     $regName  An action registration name.
     * @param string     $unitName A unit name.
     * @param string     $actName  An action name.
     * @param array|null $params   Associative array of temporary request
     *                              parameters.
     *
     * @return void
     */
    public function register($regName, $unitName, $actName, $params = null)
    {
        $this->actions[$regName]['action'] = $actName;
        $this->actions[$regName]['unit'] = $unitName;
        $this->actions[$regName]['params'] = $params;
    }

    /**
     * Set the parameter preservation status.
     *
     * @param bool $preserve A preservation status.
     *
     * @return void
     */
    public function setPreserve($preserve)
    {
        $this->preserve = $preserve;
    }
}
