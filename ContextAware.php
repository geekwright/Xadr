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
 * Extending ContextAware makes shared context, such as controller, config,
 * request and domain objects readily available in a class
 *
 * @category  Xmf\Xadr\ContextAware
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class ContextAware
{

    protected $context = null;

    /**
     * ContextAware __construct
     *
     * @param Controller $context - context object
     */
    final public function __construct(Controller $context)
    {
        $this->context = $context;
        $this->initContextAware();
    }

    /**
     * init - called by __construct. Sub classes can put any contructor code
     * here, rather than overriding __construct
     *
     * @return void
     */
    protected function initContextAware()
    {
        return;
    }

    /**
     * Instance of the Config object
     *
     * @return Config configuration object
     */
    public function config()
    {
        return $this->context->getConfig();
    }

    /**
     * Instance of the full context. At present this is the controller
     *
     * @return object shared context
     */
    public function context()
    {
        return $this->context;
    }

    /**
     * Get the controller context
     *
     * @return Controller instance
     */
    public function controller()
    {
        return $this->context;
    }

    /**
     * Get the request context
     *
     * @return Request instance
     */
    public function request()
    {
        return $this->context->getRequest();
    }

    /**
     * Get the response context
     *
     * @return Response instance
     */
    public function response()
    {
        return $this->context->getResponse();
    }

    /**
     * Get the user context
     *
     * @return User instance
     */
    public function user()
    {
        return $this->context-> getUser();
    }

    /**
     * Get the DomainManager instance
     *
     * @return DomainManager instance
     */
    public function domain()
    {
        return $this->context->getDomainManager();
    }
}
