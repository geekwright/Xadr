<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

use Xmf\Xadr\Exceptions\NoTemplateException;

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
class Renderer extends ContextAware
{

    /**
     * An attribute object for template attributes.
     *
     * @var Attributes
     */
    public $attributes;

    /**
     * An absolute file-system path where a template can be found.
     *
     * @var string
     */
    protected $dir = '';

    /**
     * The mode to be used for rendering, which is one of the following:
     *
     * - Xadr::RENDER_CLIENT - render to client
     * - Xadr::RENDER_VAR - render to variable
     *
     * @var int
     */
    protected $mode = Xadr::RENDER_CLIENT;

    /**
     * The result of a render when render mode is Xadr::RENDER_VAR.
     *
     * @var string|null
     */
    protected $result = null;

    /**
     * A file-system path to a template.
     *
     * @var string|null
     */
    protected $template = null;

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
     * Clear the rendered result.
     *
     * _This is only useful when render mode is_ Xadr::RENDER_VAR
     *
     * @return void
     */
    public function clearResult()
    {
        $this->result = null;
    }

    /**
     * Render the view.
     *
     *  _This method should never be called manually._
     *
     * @return void
     */
    public function execute()
    {
        $template = $this->template;
        if (empty($template)) {
            $error = 'A template has not been specified';
            throw new NoTemplateException($error);
        }

        $template_dir = $this->getTemplateDir();

        if (!$this->isPathAbsolute($template)) {
            $template_dir = $this->config()->get('TEMPLATE_DIR', 'templates');
            $template = $template_dir . $template;
        }

        if (!is_readable($template)) {
            $error = 'Template file ' . $template . ' does ' .
                     'not exist or is not readable';
            throw new NoTemplateException($error);
        }

        // make it easier to access data directly in the template
        $mojavi =& $this->controller()->getMojavi();
        $attributes =  $this->attributes->getAll();

        if ($this->mode == Xadr::RENDER_VAR
            || $this->controller()->getRenderMode() == Xadr::RENDER_VAR
        ) {
            ob_start();
            require $template;
            $this->result = ob_get_contents();
            ob_end_clean();
        } else {
            require $template;
        }
    }

    /**
     * Retrieve the rendered result when render mode is Xadr::RENDER_VAR.
     *
     * @return string|null the rendered view.
     */
    public function fetchResult()
    {
        if ($this->mode == Xadr::RENDER_VAR
            || $this->controller()->getRenderMode() == Xadr::RENDER_VAR
        ) {
            if ($this->result === null) {
                $this->execute();
            }

            return $this->result;
        }
        $null = null;

        return $null;

    }

    /**
     * Retrieve the render mode, which is one of the following:
     *
     * - Xadr::RENDER_CLIENT - render to client
     * - Xadr::RENDER_VAR    - render to variable
     *
     * @return int A render mode.
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Retrieve the file-system path to the template directory.
     *
     * @return string the template directory.
     */
    public function getTemplateDir()
    {
        if (empty($this->dir)) {
            $this->dir = (string) $this->config()->get('TEMPLATE_DIR', 'templates/');
        }
        return $this->dir;
    }

    /**
     * Determine if a file-system path is absolute.
     *
     * @param string $path A file-system path.
     *
     * @return bool True if path is absolute, false otherwise
     */
    public function isPathAbsolute($path)
    {
        if (strlen($path) >= 2) {
            if ($path{0} == '/' || $path{0} == "\\" || $path{1} == ':') {
                return true;
            }
        }

        return false;
    }


    /**
     * Set the render mode, which is one of the following:
     * - Xadr::RENDER_CLIENT - render to client
     * - Xadr::RENDER_VAR    - render to variable
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
     * Set the template.
     *
     * @param string $template A relative or absolute file-system path to a template.
     *
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Set the template directory.
     *
     * @param string $dir An absolute file-system path to the template directory.
     *
     * @return void
     */
    public function setTemplateDir($dir)
    {
        $this->dir = $dir;

        if (substr($dir, -1) != '/') {
            $this->dir .= '/';
        }
    }

    /**
     * Determine if a template exists.
     *
     * @param string      $template A relative or absolute path to atemplate.
     * @param string|null $dir      Absolute file-system path of template directory.
     *
     * @return bool TRUE if the template exists and is readable, otherwise FALSE.
     */
    public function templateExists($template, $dir = null)
    {
        if ($this->isPathAbsolute($template)) {
            $dir      = dirname($template) . '/';
            $template = basename($template);
        } elseif ($dir == null) {
            $dir = $this->dir;

            if (substr($dir, -1) != '/') {
                $dir .= '/';
            }
        }

        return (is_readable($dir . $template));
    }
}
