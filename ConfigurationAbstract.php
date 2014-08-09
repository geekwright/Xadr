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
 * ConfigurationAbstract provides a model for Configuration
 *
 * @category  Xmf\Xadr\Config
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 * @since     1.0
 */
abstract class ConfigurationAbstract extends ContextAware
{
    /**
     * ConfigurationAbstract
     *
     * @param Controller $context - context object
     */
    public function __construct(Controller $context)
    {
        parent::__construct($context);
        $this->initialize();
    }

    /**
     * initialize - set configurations
     *
     *  _This method should never be called manually._
     *
     * @return void
     */
    abstract protected function initialize();
}
