<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
 */

namespace Xmf\Xadr;

/**
 * The Controller dispatches requests to the proper action and controls
 * application flow.
 *
 * @category  Xmf\Xadr\Controller
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Controller
{

    /**
     * A developer supplied authorization handler.
     *
     * @var AuthorizationHandler
     */
    protected $authorizationHandler;

    /**
     * A Config instance
     *
     * @var Config
     */
    protected $config;

    /**
     * A user requested content type.
     *
     * @var mixed
     */
    protected $contentType;

    /**
     * Currently processing action.
     *
     * @var string
     */
    protected $currentAction;

    /**
     * Currently processing unit.
     *
     * @var string
     */
    protected $currentUnit;

    /**
     * ExecutionChain instance.
     *
     * @var ExecutionChain
     */
    protected $execChain;

    /**
     * A DomainManager instance
     *
     * @var DomainManager
     */
    protected $domainManager;

    /**
     * An associative array of template-ready data.
     *
     * @var array
     */
    protected $mojavi;

    /**
     * PHP Namespace of Xadr application
     *
     * @var string
     */
    protected $nameSpace;

    /**
     * Determines how a Responder should be rendered.
     *
     * Possible render modes:
     * - Xadr::RENDER_CLIENT - render to the client
     * - Xadr::RENDER_VAR    - render to variable
     *
     * @var integer
     */
    protected $renderMode;

    /**
     * A Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Originally requested action.
     *
     * @var string
     */
    protected $requestAction;

    /**
     * Originally requested unit.
     *
     * @var string
     */
    protected $requestUnit;

    /**
     * A User instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Create a new Controller instance.
     *
     * _This should never be called manually._
     * Use static getInstance() method.
     *
     * @param object|string|null $externalCom ExternalCom object
     */
    protected function __construct($externalCom = null)
    {
        $this->contentType   =  $externalCom===null?'html':$externalCom;
        $this->currentAction =  null;
        $this->currentUnit   =  null;
        $this->execChain     =  new ExecutionChain;
        $this->renderMode    =  Xadr::RENDER_CLIENT;
        $this->requestAction =  null;
        $this->requestUnit   =  null;

        // init Controller objects
        $this->authorizationHandler =  null;
        $this->request              =  new Request($this->parseParameters());
        $this->mojavi               =  array();
        $this->user                 =  null;

        $this->domainManager        =  new DomainManager($this);
        $this->config = new Config;
    }

    /**
     * Retrieve an new instance of the Controller.
     *
     * @param object|string|null $externalCom ExternalCom object
     *
     * @return Controller A Controller instance.
     */
    public static function getNew($externalCom = null)
    {
        $controllerClass = get_called_class(); // not available PHP<5.3
        $instance = new $controllerClass($externalCom);

        return $instance;
    }

    /**
     * getComponentName - build class name of action, responder, etc.
     *
     * @param string $compType    type (action, responder, etc.)
     * @param string $unitName    Unit name
     * @param string $actName     Action name
     * @param string $actResponse Responder suffix (success, error, input, etc.)
     *
     * @return string|null file name or null on error
     */
    protected function getComponentName($compType, $unitName, $actName, $actResponse)
    {
        $actResponse = ucfirst(strtolower($actResponse));

        $cTypes=array(
            'action'     => array('dir'=>'Actions',    'suffix'=>'Action'),
            'filter'     => array('dir'=>'Filters',    'suffix'=>'Filter'),
            'filterlist' => array('dir'=>'Filters',    'suffix'=>''),
            'responder'  => array('dir'=>'Responders', 'suffix'=>"Responder{$actResponse}"),
            'domain'     => array('dir'=>'Domain',     'suffix'=>''),
        );

        $class=null;
        if (isset($cTypes[$compType])) {
            $c=$cTypes[$compType];
            $class = $this->nameSpace . "\\{$unitName}\\{$c['dir']}\\{$actName}{$c['suffix']}";
        }
        return $class;

    }

    /**
     * Determine if an action exists.
     *
     * @param string $unitName A unit name.
     * @param string $actName  An action name.
     *
     * @return bool TRUE if the given unit has the given action,
     *              otherwise FALSE.
     */
    public function actionExists($unitName, $actName)
    {
        $classname = $this->getComponentName('action', $unitName, $actName, '');

        return (class_exists($classname));

    }

    /**
     * Dispatch a request.
     *
     * _Optional parameters for unit and action exist if you wish to
     * use a page controller pattern._
     *
     * @param string|null $unitName A unit name.
     * @param string|null $actName  An action name.
     *
     * @return void
     */
    public function dispatch($unitName = null, $actName = null)
    {

        if ($this->user === null) {
            // no user type has been set, use the default no privilege user
            $this->user = new User($this);
        }

        // alias mojavi and request objects for easy access
        $mojavi  = $this->mojavi;
        $request = $this->request;

        // use default unit and action only if both have not been specified
        if ($unitName == null
            && !$request->hasParameter($this->config->get('UNIT_ACCESSOR', 'unit'))
            && $actName == null
            && !$request->hasParameter($this->config->get('ACTION_ACCESSOR', 'action'))
        ) {
            $actName = $this->config->get('DEFAULT_ACTION', 'Index');
            $unitName = $this->config->get('DEFAULT_UNIT', 'App');
        } else {
            // has a unit been specified via dispatch()?
            if ($unitName == null) {
                // unit not specified via dispatch(), check parameters
                $unitName = $request->getParameter(
                    $this->config->get('UNIT_ACCESSOR', 'unit')
                );
                if (empty($unitName)) {
                    $unitName = $this->config->get('DEFAULT_UNIT', 'App');
                }
            }

            // has an action been specified via dispatch()?
            if ($actName == null) {
                // an action hasn't been specified via dispatch(), let's check
                // the parameters
                $actName = $request->getParameter(
                    $this->config->get('ACTION_ACCESSOR', 'action')
                );

                if ($actName == null) {
                    // does an Index action exist for this unit?
                    if ($this->actionExists($unitName, 'Index')) {
                        // ok, we found the Index action
                        $actName = 'Index';
                    }
                    if (empty($actName)) {
                        $actName = $this->config->get('DEFAULT_ACTION', 'Index');
                    }
                }
            }
        }

        // if $unitName or $actName equal NULL, we don't set them. we'll let
        // ERROR_404_ACTION do it's thing inside forward()

        // replace unwanted characters
        $actName = (string) preg_replace('/[^a-z0-9_]+/i', '', $actName);
        $unitName = (string) preg_replace('/[^a-z0-9_]+/i', '', $unitName);

        // set request unit and action
        $this->requestAction      = $actName;
        $this->requestUnit        = $unitName;
        $mojavi['request_action'] = $actName;
        $mojavi['request_unit']   = $unitName;

        // paths
        $mojavi['controller_path']     = $this->getControllerPath();
        $mojavi['current_action_path']
            = $this->getControllerPath($unitName, $actName);
        $mojavi['current_unit_path'] = $this->getControllerPath($unitName);
        $mojavi['request_action_path']
            = $this->getControllerPath($unitName, $actName);
        $mojavi['request_unit_path'] = $this->getControllerPath($unitName);

        // process our originally request action
        $this->forward($unitName, $actName);

        // shutdown DomainManager
        $this->domainManager->shutdown();

    }

    /**
     * loadRequired - load a file, die if not found
     *
     * @param string $filename name of file to load
     *
     * @return void
     */
    protected function loadRequired($filename)
    {
        if (!\Xmf\Loader::loadFile($filename)) {
            die (sprintf('Failed to load %s', $filename));
        }
    }

    /**
     * ifExistsRequire - load a file if it exists
     *
     * @param string $filename name of file to load
     *
     * @return void
     */
    protected function ifExistsRequire($filename)
    {
        return \Xmf\Loader::loadFile($filename);
    }

    /**
     * Forward the request to an action.
     *
     * @param string $unitName A unit name.
     * @param string $actName  An action name.
     *
     * @return void
     */
    public function forward($unitName, $actName)
    {
        if ($this->currentUnit == $unitName
            && $this->currentAction == $actName
        ) {
            $error = 'Recursive forward on unit ' . $unitName
                . ', action ' . $actName;

            trigger_error($error, E_USER_ERROR);

            exit;
        }

        // execute unit configuration, if it exists
        //$this->ifExistsRequire($this->config->get('UNITS_DIR') . $unitName . '/config.php');

        if ($this->actionExists($unitName, $actName)) {
            // create the action instance
            $action = $this->getAction($unitName, $actName);
        } else {
            // the requested action doesn't exist
            $action = null;
        }

        // track old unit/action
        $oldAction = $this->currentAction;
        $oldUnit = $this->currentUnit;

        // add unit and action to execution chain, and update current vars
        $this->execChain->addRequest($unitName, $actName, $action);
        $this->updateCurrentVars($unitName, $actName);

        if ($action === null) {

            // requested action doesn't exist
            $actName = $this->config->get('ERROR_404_ACTION', 'PageNotFound');
            $unitName = $this->config->get('ERROR_404_UNIT', 'App');

            if (!$this->actionExists($unitName, $actName)) {

                // cannot find error 404 unit/action
                $error = 'Invalid configuration setting(s): ' .
                        $this->nameSpace . ' - ' .
                        'ERROR_404_UNIT (' . $unitName . ') or ' .
                        'ERROR_404_ACTION (' . $actName . ')';

                trigger_error($error, E_USER_ERROR);

                exit;

            }

            // add another request since the action is non-existent
            $action = $this->getAction($unitName, $actName);

            $this->execChain->addRequest($unitName, $actName, $action);
            $this->updateCurrentVars($unitName, $actName);

        }

        $filterChain = new FilterChain;

        // map filters
        $this->mapGlobalFilters($filterChain);
        $this->mapUnitFilters($filterChain, $unitName);

        // and last but not least, the main execution filter
        $filterChain->register(new ExecutionFilter($this));

        // execute filters
        $filterChain->execute();

        // update current vars
        $this->updateCurrentVars($oldUnit, $oldAction);

    }

    /**
     * Generate a formatted Mojavi URL.
     *
     * @param array $params An associative array of URL parameters.
     *
     * @return string A URL to a Mojavi resource.
     */
    public function genURL($params)
    {

        $url = $this->config->get('SCRIPT_PATH');

        $divider  = '&';
        $equals   = '=';
        $url     .= '?';
        $separator = '';

        foreach ($params as $key => $value) {
            $url .= $separator . urlencode($key) . $equals .  urlencode($value);
            $separator = $divider;
        }

        return $url;

    }

    /**
     * Generate a URL for a given unit, action and parameters
     *
     * @param string $unitName a unit name
     * @param string $actName  an action name
     * @param array  $params   an associative array of additional URL parameters
     *
     * @return string A URL to a Mojavi resource.
     */
    public function getControllerPathWithParams($unitName, $actName, $params)
    {

        $url=$this->getControllerPath($unitName, $actName);
        $divider = (strpos($url, '?')===false) ? '?' : '&';

        foreach ($params as $k => $v) {
            $url .= $divider . urlencode($k) . '=' .  urlencode($v);
            $divider  = '&'; // from here on we append
        }

        return $url;

    }

    /**
     * Retrieve an action implementation instance.
     *
     * @param string $unitName A unit name.
     * @param string $actName  An action name.
     *
     * @return Action An Action instance, if the action exists, otherwise
     *                an error will be reported.
     */
    public function getAction($unitName, $actName)
    {
        $classname = $this->getComponentName('action', $unitName, $actName, '');

        return new $classname($this);
    }

    /**
     * Retrieve the developer supplied authorization handler.
     *
     * @return AuthorizationHandler An AuthorizationHandler instance, if an
     *                              authorization handler has been set,
     *                              otherwise NULL.
     */
    public function getAuthorizationHandler()
    {
        return $this->authorizationHandler;
    }

    /**
     * Retrieve the Config object
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Retrieve the user supplied content type.
     *
     * @return string content type
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Retrieve an absolute web path to the public controller file.
     *
     * @param string|null $unitName A unit name.
     * @param string|null $actName  An action name.
     *
     * @return string An absolute web path representing the controller file,
     *                which also includes unit and action names.
     */
    public function getControllerPath($unitName = null, $actName = null)
    {

        $path = $this->config->get('SCRIPT_PATH');
        //$path = $_SERVER['SCRIPT_NAME'];

        $varsep = '?';

        if (!(empty($unitName)
            || $unitName==$this->config->get('DEFAULT_UNIT', 'App'))
        ) {
            $path .= $varsep.$this->config->get('UNIT_ACCESSOR', 'unit')."=$unitName";
            $varsep = '&';
        }
        if (!empty($actName)) {
            $path .= $varsep.$this->config->get('ACTION_ACCESSOR', 'action')."=$actName";
        }

        return $path;

    }

    /**
     * Retrieve the name of the currently processing action.
     *
     * If the request has not been forwarded, this will return the
     * originally requested action.
     *
     * @return string
     */
    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    /**
     * Retrieve the name of the currently processing unit.
     *
     * If the request has not been forwarded, this will return the
     * originally requested unit
     *
     * @return string current unit
     */
    public function getCurrentUnit()
    {
        return $this->currentUnit;

    }

    /**
     * Retrieve the execution chain.
     *
     * @return ExecutionChain An ExecutionChain instance.
     */
    public function getExecutionChain()
    {
        return $this->execChain;

    }

    /**
     * Retrieve an absolute file-system path home directory of the currently
     * processing unit.
     *
     *  _ If the request has been forwarded, this will return the directory of
     *    the forwarded unit._
     *
     * @return string A unit directory.
     */
    public function getUnitDir()
    {
        return ($this->config->get('UNITS_DIR') . $this->currentUnit . '/');

    }

    /**
     * Retrieve the Mojavi associative array.
     *
     * @return array An associative array of template-ready data.
     */
    public function & getMojavi()
    {
        return $this->mojavi;

    }

    /**
     * Retrieve the global render mode.
     *
     * @return int One of two possible render modes:
     * - Xadr::RENDER_CLIENT  - render to the client
     * - Xadr::RENDER_VAR     - render to variable
     */
    public function getRenderMode()
    {
        return $this->renderMode;

    }

    /**
     * Retrieve the request instance.
     *
     * @return Request A Request instance.
     */
    public function getRequest()
    {
        return $this->request;

    }

    /**
     * Retrieve the name of the originally requested action.
     *
     * @return string An action name.
     */
    public function getRequestAction()
    {
        return $this->requestAction;

    }

    /**
     * Retrieve the name of the originally requested unit.
     *
     * @return string A unit name.
     */
    public function getRequestUnit()
    {
        return $this->requestUnit;

    }

    /**
     * Retrieve the currently requesting user.
     *
     * @return User a User instance.
     */
    public function getUser()
    {
        return $this->user;

    }

    /**
     * Retrieve a Responder implementation instance.
     *
     * @param string $unitName     A Unit name
     * @param string $actName      An Action name
     * @param string $responseName A Response name
     *
     * @return Responder instance.
     */
    public function getResponder($unitName, $actName, $responseName)
    {
        $classname = $this->getComponentName('responder', $unitName, $actName, $responseName);

        return new $classname($this);
    }

    /**
     * Map global filters.
     *
     * @param FilterChain $filterChain A FilterChain instance.
     *
     * @return void
     */
    public function mapGlobalFilters($filterChain)
    {
        static $list;

        if (!isset($list)) {
            $classname = $this->nameSpace . "\\GlobalFilterList";
            if (class_exists($classname)) {
                $list = new $classname($this);
                $list->registerFilters(
                    $filterChain,
                    $this,
                    $this->request,
                    $this->user
                );
            }
        } else {
            $list->registerFilters(
                $filterChain,
                $this,
                $this->request,
                $this->user
            );
        }
    }

    /**
     * Map all filters for a given unit.
     *
     * @param FilterChain $filterChain A FilterChain instance.
     * @param string      $unitName    A unit name.
     *
     * @return void
     */
    public function mapUnitFilters($filterChain, $unitName)
    {
        static $cache;

        if (!isset($cache)) {
            $cache = array();
        }

        $listName = $unitName . 'FilterList';

        if (!isset($cache[$listName])) {
            $classname = $this->getComponentName(
                'filterlist',
                $unitName,
                $listName,
                ''
            );

            if (class_exists($classname)) {
                $list             = new $classname($this);
                $cache[$listName] = $list;
                // register filters
                $list->registerFilters(
                    $filterChain,
                    $this,
                    $this->request,
                    $this->user
                );
            }
        } else {
            $cache[$listName]->registerFilters(
                $filterChain,
                $this,
                $this->request,
                $this->user
            );
        }
    }

    /**
     * Parse and retrieve all PATH/REQUEST parameters.
     *
     * @return array An associative array of parameters.
     */
    protected function & parseParameters()
    {
        /**
         * \Xmf\Request::get($hash = 'default', $mask = 0)
         * bitmask values for $mask are:
         *   -  \Xmf\Request::NOTRIM    (1)  set to skip trim() on values
         *   -  \Xmf\Request::ALLOWRAW  (2)  set to disable html check
         *   -  \Xmf\Request::ALLOWHTML (4)  set to allow all html,
         *      clear for 'safe' only
         *
         * We will clean agressively. Raw values are not overwritten, so
         * code can go back and get directly with different options if
         * needed. Cleaning also handles magic_quotes_gpc clean up.
         *
         */

        $values = array();

        // load GET params into $values array
        $values = array_merge($values, \Xmf\Request::get('GET', 0));

        // load POST params into $values array
        $values = array_merge($values, \Xmf\Request::get('POST', 0));

        return $values;

    }

    /**
     * Redirect the request to another location.
     *
     * @param string $url A URL.
     *
     * @return void
     */
    public function redirect($url)
    {
        header('Location: ' . $url);
    }

    /**
     * Set the developer supplied authorization handler.
     *
     * @param Authorizationhandler $handler An AuthorizationHandler instance.
     *
     * @return void
     */
    public function setAuthorizationHandler($handler)
    {
        $this->authorizationHandler = $handler;
    }

    /**
     * Set the content type.
     *
     * @param string $contentType A user supplied content type.
     *
     * @return void
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Set the global render mode.
     *
     * @param int $mode Global render mode, which is one of the following two:
     * - Xadr::RENDER_CLIENT - render to the client
     * - Xadr::RENDER_VAR    - render to variable
     *
     * @return void
     */
    public function setRenderMode($mode)
    {
        $this->renderMode = $mode;
    }

    /**
     * Set the user type.
     *
     * @param User $user A User instance.
     *
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Update current unit and action data.
     *
     * @param string $unitName A unit name.
     * @param string $actName  An action name.
     *
     * @return void
     */
    protected function updateCurrentVars($unitName, $actName)
    {

        // alias objects for easy access
        $mojavi =& $this->mojavi;

        $this->currentUnit = $unitName;
        $this->currentAction = $actName;

        // names
        $mojavi['current_action'] = $actName;
        $mojavi['current_unit'] = $unitName;

        // directories
        //$mojavi['unit_dir']   = $this->config->get('UNITS_DIR');
        //$mojavi['template_dir']
        //    = $this->config->get('UNITS_DIR') . $unitName . '/templates/';

        // paths
        $mojavi['current_action_path']
            = $this->getControllerPath($unitName, $actName);
        $mojavi['current_unit_path'] = $this->getControllerPath($unitName);

    }

    /**
     * Determine if a response exists.
     *
     * @param string $unitName     A unit name.
     * @param string $actName      An action name.
     * @param string $responseName A response name.
     *
     * @return bool TRUE if the response class exists, otherwise FALSE.
     */
    public function responseExists($unitName, $actName, $responseName)
    {

        $classname = $this->getComponentName('responder', $unitName, $actName, $responseName);

        return (class_exists($classname));

    }

    /**
     * Retrieve a filter implementation instance.
     *
     * @param string $name     - A filter name.
     * @param string $unitName - A unit name, defaults to current unit
     *
     * @return a Filter instance.
     */
    public function getFilter($name, $unitName = '')
    {
        if (empty($unitName)) {
            $unitName = $this->currentUnit;
        }
        $classname = $this->getComponentName('filter', $unitName, $name, '');

        return new $classname($this);
    }

    /**
     * Retrieve the DomainManager instance.
     *
     * @return object DomainManager
     */
    public function getDomainManager()
    {
        return $this->domainManager;
    }

    /**
     * Retrieve a domain implementation instance.
     *
     * @param string $name     - A domain name.
     * @param string $unitName - A unit name, defaults to current unit
     *
     * @return object|null
     */
    public function getDomain($name, $unitName = '')
    {
        if (empty($unitName)) {
            $unitName = $this->currentUnit;
        }
        $classname = $this->getComponentName('domain', $unitName, $name, '');
        return class_exists($classname) ?  new $classname($this) : null;
    }
}
