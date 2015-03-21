<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

/**
 * Renderer implements a renderer object using template files
 * consisting of PHP code.
 *
 * @category  Xmf\Xadr\Renderer
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class Renderer extends ContextAware
{

    /**
     * An attribute object for template attributes.
     *
     * @var XadrArray
     */
    public $attributes;

    /**
     * An absolute file-system path where a template can be found.
     *
     * @var string
     */
    protected $dir = null;

    /**
     * The mode to be used for rendering, which is one of the following:
     *
     * - Xadr::RENDER_CLIENT - render to client
     * - Xadr::RENDER_VARIABLE - render to variable
     *
     * @var int
     */
    protected $mode = Xadr::RENDER_CLIENT;

    /**
     * A file-system path to a template.
     *
     * @var string|null
     */
    protected $template = null;

    /**
     * Render results for mode Xadr::RENDER_VARIABLE
     *
     * @var string
     */
    protected $result = '';

    /**
     * initContextAware - called by ContextAware::__construct
     *
     * @return void
     */
    protected function initContextAware()
    {
        $this->attributes = new XadrArray;
    }

    /**
     * Render the view.
     *
     * @return void
     */
    abstract public function execute();

    /**
     * Retrieve the rendered result when render mode is Xadr::RENDER_VARIABLE.
     *
     * @return string the rendered view.
     */
    public function fetchResult()
    {
        return $this->result;
    }

    /**
     * Clear any rendered result.
     *
     * @return void
     */
    public function clearResult()
    {
        $this->result = '';
    }

    /**
     * Retrieve the render mode, which is one of the following:
     *
     * - Xadr::RENDER_CLIENT - render to client
     * - Xadr::RENDER_VARIABLE    - render to variable
     *
     * @return int A render mode.
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set the render mode, which is one of the following:
     * - Xadr::RENDER_CLIENT   - echo to output
     * - Xadr::RENDER_VARIABLE - render to variable
     *
     * @param int $mode render mode.
     *
     * @return void
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Get the current template.
     *
     * @return mixed $template The template if previously set or null
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set the template.
     *
     * @param mixed $template A template
     *
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Retrieve the path to the template directory.
     *
     * @return string the template directory.
     */
    public function getTemplateDir()
    {
        return $this->dir;
    }

    /**
     * Set the template directory.
     *
     * @param string $dir A path to the templates
     *
     * @return void
     */
    public function setTemplateDir($dir)
    {
        $this->dir = $dir;
    }
}
