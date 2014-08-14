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
 * XoopsController implements a controller with with specific
 * characteristics optimized for the XOOPS environment, including:
 * - XOOPS specific User and AuthorizationHandler
 * - XOOPS module helper
 * - XOOPS module appropriate configuration, defaults and autoloading
 *
 * @category  Xmf\Xadr\XoopsController
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class XoopsController extends Controller
{

    /**
     *  @var string XOOPS Module directory name
     */
    protected $dirname;

    /**
     *  @var object XOOPS Module helper
     */
    protected $moduleHelper;

    /**
     * @var XoopsUser instance
     */
    protected $user;

    /**
     * XOOPS specific controller constructor, sets user and
     * authorization handler to XOOPS specific onjects.
     *
     * @param object|string|null $externalCom ExternalCom object
     */
    protected function __construct($externalCom = null)
    {
        parent::__construct();
        $xoops = \Xoops::getInstance();
        $this->externalCom = $externalCom;
        if (is_object($externalCom) && method_exists($externalCom, 'getDirname')) {
            $this->dirname = $externalCom->getDirname();
        } else {
            $this->dirname = $xoops->isModule() ? $xoops->module->getVar('dirname') : 'system';
        }
        $this->moduleHelper = $xoops->getModuleHelper($this->dirname);
        //$this->moduleHelper->setDebug(true);
        $this->nameSpace = (string) $this->modGetInfo('xadr_namespace');

        // this will quietly ignore a missing config file
        $classname = $this->nameSpace . "\\Configuration";
        if (class_exists($classname)) {
            new $classname($this);
        }

        // set some reasonable defaults if config is empty
        if (!$this->config->get('DEFAULT_UNIT', false)) {
            $pathname=$xoops->path('modules/'.$this->dirname.'/');
            //$this->config->set('UNITS_DIR', $pathname.'class/xadr/');
            $this->config->set('SCRIPT_PATH', $xoops->url('modules/'.$this->dirname.'/index.php'));
            $this->config->set('UNIT_ACCESSOR', 'unit');
            $this->config->set('ACTION_ACCESSOR', 'action');
            $this->config->set('DEFAULT_UNIT', 'App');
            $this->config->set('DEFAULT_ACTION', 'Index');
            $this->config->set('ERROR_404_UNIT', 'App');
            $this->config->set('ERROR_404_ACTION', 'PageNotFound');
            $this->config->set('SECURE_UNIT', 'App');
            $this->config->set('SECURE_ACTION', 'NoPermission');
        }

        $this->user                 =  new XoopsUser($this);
        $this->authorizationHandler =  new XoopsAuthHandler($this);
        $this->user->setXoopsPermissionMap($this->config->get('PermissionMap', array()));
    }

    // These methods provide quick access to some XOOPS objects.
    // The controller already is module aware and has a module
    // helper established. Share that.

    /**
     * getHandler - get XoopsObjectHandler
     *
     * @param string $name name of an object handler
     *
     * @return bool|XoopsObjectHandler|XoopsPersistableObjectHandler
     */
    public function getHandler($name)
    {
        return $this->moduleHelper->getHandler($name);
    }

    /**
     * moduleHelper - get module helper
     *
     * @return object Module Helper
     */
    public function moduleHelper()
    {
        return $this->moduleHelper;
    }

    /**
     * modGetVar - get varaible from XoopsModule
     *
     * @param string $name name of module variable
     *
     * @return mixed module getVar return
     */
    public function modGetVar($name)
    {
        return $this->moduleHelper->getModule()->getVar($name);
    }

    /**
     * modGetInfo - get modversion item
     *
     * @param string $name name of module info variable
     *
     * @return mixed module getInfo return
     */
    public function modGetInfo($name)
    {
        return $this->moduleHelper->getModule()->getInfo($name);
    }

    /**
     * modGetConfig - get a module configuration value
     *
     * @param string $name name of module configuration
     *
     * @return mixed module helper getConfig return
     */
    public function modGetConfig($name)
    {
        return $this->moduleHelper->getConfig($name);
    }

    /**
     * getDirname
     *
     * @return string
     */
    public function getDirname()
    {
        return $this->dirname;
    }
}
