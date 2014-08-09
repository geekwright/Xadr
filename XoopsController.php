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
 * @version   Release: 1.0
 * @link      http://xoops.org
 * @since     1.0
 */
class XoopsController extends Controller
{

    /**
     *  @var External communication block object
     */
    protected $externalCom;

    /**
     *  @var XOOPS Module directory name
     */
    protected $dirname;

    /**
     *  @var XOOPS Module helper
     */
    protected $modhelper;

    /**
     * XOOPS specific controller constructor, sets user and
     * authorization handler to XOOPS specific onjects.
     *
     * @param object $externalCom ExternalCom object
     *
     * @since  1.0
     */
    protected function __construct($externalCom = null)
    {
        parent::__construct();
        $this->externalCom = $externalCom;
        if (is_object($externalCom) && method_exists($externalCom, 'getDirname')) {
            $this->dirname = $externalCom->getDirname();
        } else {
            //$this->dirname = $GLOBALS['xoopsModule']->getVar('dirname');
            $xoops = \Xoops::getInstance();
            $this->dirname = $xoops->isModule() ? $xoops->module->getVar('dirname') : 'system';
        }
        $this->modhelper = \Xmf\Module\Helper::getHelper($this->dirname);
        //$this->modhelper->setDebug(true);
        $this->nameSpace = $this->modGetInfo('xadr_namespace');

        // this will quietly ignore a missing config file
        $classname = $this->nameSpace . "\\Configuration";
        if (class_exists($classname)) {
            $list = new $classname($this);
        }

        // set some reasonable defaults if config is empty
        if (!$this->config->get('DEFAULT_UNIT', false)) {
            $pathname=XOOPS_ROOT_PATH .'/modules/'.$this->dirname.'/';
            $this->config->set('UNITS_DIR', $pathname.'class/xadr/');
            $this->config->set('SCRIPT_PATH', XOOPS_URL .'/modules/'.$this->dirname.'/index.php');
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

    /**
     * getExternalCom - get the ExternalCom object
     *
     * TODO - should this be in parent instead?
     *
     * @return object ExternalCom
     *
     * @since  1.0
     */
    public function getExternalCom()
    {
        return $this->externalCom;
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
     *
     * @since  1.0
     */
    public function getHandler($name)
    {
        return $this->modhelper->getHandler($name);
    }

    /**
     * modHelper - get module helper
     *
     * @param string $name a XOOPS module dirname
     *
     * @return object Module Helper
     *
     * @since  1.0
     */
    public function modHelper($name)
    {
        return $this->modhelper;
    }

    /**
     * modGetVar - get varaible from XoopsModule
     *
     * @param string $name name of module variable
     *
     * @return mixed module getVar return
     *
     * @since  1.0
     */
    public function modGetVar($name)
    {
        return $this->modhelper->getModule()->getVar($name);
    }

    /**
     * modGetInfo - get modversion item
     *
     * @param string $name name of module info variable
     *
     * @return mixed module getInfo return
     *
     * @since  1.0
     */
    public function modGetInfo($name)
    {
        return $this->modhelper->getModule()->getInfo($name);
    }

    /**
     * modGetConfig - get a module configuration value
     *
     * @param string $name name of module configuration
     *
     * @return mixed module helper getConfig return
     *
     * @since  1.0
     */
    public function modGetConfig($name)
    {
        return $this->modhelper->getConfig($name);
    }

    public function getDirname()
    {
        return $this->dirname;
    }
}
