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
 * Domain defines the framework that must be implemented by a class
 * to qualify as a Domain.
 *
 * A Domain defines a business process rule set consisting of
 * - business objects (one or more database objects and relating rules)
 * - presentation rules appropriate to input and display
 * - validation rules
 * - methods for specific actions unique to the business process
 * - retrieve rule sets approriate to a specific process step
 *   (i.e entry form, entry validation, etc.)
 * - triggers for announcing completion of specific process actions
 *   (i.e. new object created)
 * - workflow
 * - (more)
 *
 * Domains are loaded and tracked by the DomainManger, and thus they are
 * available to any ContextAware object.
 *
 * @category  Xmf\Xadr\Domain
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2015 XOOPS Project (http://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
abstract class Domain extends ContextAware
{

    /**
     * initialize the domain - called automatically by DomainManger
     *
     * implementations should establish the domain
     *
     * @return bool true if domain has initialized, otherwise false
     */
    abstract public function initialize();

    /**
     * cleanup the domain - called automatically by DomainManger
     *
     * concrete implementations should cleanly close the domain
     *
     * @return bool true if domain has closed cleanly, otherwise false
     */
    abstract public function cleanup();

    /**
     * Access the DomainState object for this Domain
     *
     * The DomainState object provides a transient storage mechanism for Domain related
     * current state data, such as result sets. Some DomainState implementations may
     * provide persistence features as well.
     *
     * @return DomainState object
     */
    abstract public function state();
}
