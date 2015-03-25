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

use Xmf\Xadr\Exceptions\DomainFailureException;
use Xmf\Xadr\Exceptions\InvalidDomainException;

/**
 * A DomainManager manages the loading, start up and shut down of models.
 *
 * @category  Xmf\Xadr\DomainManager
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class DomainManager extends ContextAware
{
    /**
     * An associative array of Domain objects, keyed by unit and name
     *
     * @var array
     */
    protected $domains = array();

    /**
     * An indexed array of loaded domains, each element as array('unit' => $unit,'name' => $name))
     *
     * @var array
     */
    protected $domainOrder=array();

    /**
     * Return a domain instance.
     *
     * @param string      $domainName A Domain name.
     * @param string|null $unitName   A unit name, defaults to current unit
     *
     * @return Domain
     *
     * @throws Xmf\Xadr\Exceptions\DomainFailureException
     * @throws Xmf\Xadr\Exceptions\InvalidDomainException
     */
    public function getDomain($domainName, $unitName = null)
    {
        if ($unitName === null) {
            $unitName = $this->controller()->getCurrentUnit();
        }

        if (!isset($this->domains[$unitName][$domainName])) {
            $domain = $this->controller()->getDomainComponent($domainName, $unitName);
            if (!($domain instanceof Domain)) {
                throw new InvalidDomainException($this->notDomainMessage($domainName));
            }
            $initStatus = false;
            try {
                $initStatus = $domain->initialize();
            } catch (\InvalidArgumentException $e) {
                throw new DomainFailureException($this->notInitializedDomainMessage($domainName), 0, $e);
            }
            if (!$initStatus) {
                throw new DomainFailureException($this->notInitializedDomainMessage($domainName));
            }
            $this->domains[$unitName][$domainName]=$domain;

            // add to head of list - will shutdown in reverse order of adding
            array_unshift($this->domainOrder, array('unit' => $unitName, 'name' => $domainName));
        }

        return $this->domains[$unitName][$domainName];
    }

    /**
     * Shutdown the DomainManager
     *
     * @return boolean true if all domains reported clean shutdown otherwise false
     */
    public function shutdown()
    {
        $return = true;
        foreach ($this->domainOrder as $domain) {
            $return &= $this->domains[$domain['unit']][$domain['name']]->cleanup();
        }
        return (boolean) $return;
    }

    /**
     * Prepare message for not a domain object exception
     *
     * @param string $domainName domain name
     *
     * @return string translated message with name inserted
     */
    private function notDomainMessage($domainName)
    {
        return sprintf('%s is not a Domain object', $domainName);
    }

    /**
     * Prepare message for a domain initialize failure
     *
     * @param string $domainName domain name
     *
     * @return string translated message with name inserted
     */
    private function notInitializedDomainMessage($domainName)
    {
        return sprintf('Domain $s did not initialize', $domainName);
    }
}
