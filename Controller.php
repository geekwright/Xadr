<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

use Xmf\Xadr\Exceptions\InvalidConfigurationException;
use Xmf\Xadr\Exceptions\RecursiveForwardException;

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
     * @var AuthorizationHandler instance
     */
    protected $authorizationHandler;

    /**
     * @var Config a Config instance
     */
    protected $config;

    /**
     *  @var object|string|null External communication block object
     */
    protected $externalCom;

    /**
     * @var string currently processing action.
     */
    protected $currentAction;

    /**
     * @var string currently processing unit.
     */
    protected $currentUnit;

    /**
     * @var ExecutionChain instance
     */
    protected $execChain;

    /**
     * @var DomainManager instance
     */
    protected $domainManager;

    /**
     * @var array an associative array of template-ready data.
     */
    protected $mojavi;

    /**
     * @var string PHP Namespace of Xadr application
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
     * @var Request instance
     */
    protected $request;

    /**
     * @var string originally requested action
     */
    protected $requestAction;

    /**
     * @var string originally requested unit
     */
    protected $requestUnit;

    /**
     * @var User instance
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
        $this->externalCom   =  $externalCom;
        $this->currentAction =  null;
        $this->currentUnit   =  null;
        $this->execChain     =  new ExecutionChain;
        $this->renderMode    =  Xadr::RENDER_CLIENT;
        $this->requestAction =  null;
        $this->requestUnit   =  null;

        // init Controller objects
        $this->authorizationHandler =  null;
        $this->request              =  new Request($this->getParameters());
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
     * @param string $actionName  Action name
     * @param string $actResponse Responder suffix (success, error, input, etc.)
     *
     * @return string|null file name or null on error
     */
    protected function getComponentName($compType, $unitName, $actionName, $actResponse)
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
            $class = $this->nameSpace . "\\{$unitName}\\{$c['dir']}\\{$actionName}{$c['suffix']}";
        }
        return $class;

    }

    /**
     * Determine if an action exists.
     *
     * @param string $unitName   A unit name.
     * @param string $actionName An action name.
     *
     * @return bool TRUE if the given unit has the given action,
     *              otherwise FALSE.
     */
    public function actionExists($unitName, $actionName)
    {
        $classname = $this->getComponentName('action', $unitName, $actionName, '');

        return (class_exists($classname));
    }

    /**
     * Set a variable if it is currently empty
     *
     * @param string|null $variable variable to set
     * @param string|null $value    value
     *
     * @return void
     */
    protected function setIfEmpty(&$variable, $value)
    {
        if (empty($variable)) {
            $variable = $value;
        }
    }

    /**
     * Normalize unit and action
     *
     * @param string|null $unitName   A unit name.
     * @param string|null $actionName An action name.
     *
     * @return void ($unitName and $actionName are references)
     */
    protected function normalizeUnitAction(&$unitName, &$actionName)
    {
        $unitParameter = $this->config->get('UNIT_ACCESSOR', 'unit');
        $actionParameter = $this->config->get('ACTION_ACCESSOR', 'action');
        $unitDefault = $this->config->get('DEFAULT_UNIT', 'App');
        $actionDefault = $this->config->get('DEFAULT_ACTION', 'Index');
        // use default unit and action only if both have not been specified
        if (empty($unitName) && empty($actionName)
            && !$this->request->hasParameter($unitParameter)
            && !$this->request->hasParameter($actionParameter)
        ) {
            $unitName = $unitDefault;
            $actionName = $actionDefault;
        } else {
            // has a unit been specified via dispatch()?
            $this->setIfEmpty($unitName, $this->request->getParameter($unitParameter));
            $this->setIfEmpty($unitName, $unitDefault);

            // has an action been specified via dispatch()?
            $this->setIfEmpty($actionName, $this->request->getParameter($actionParameter));
            $this->setIfEmpty($actionName, $actionDefault);
        }
    }

    /**
     * Dispatch a request.
     *
     * _Optional parameters for unit and action exist if you wish to
     * use a page controller pattern._
     *
     * @param string|null $unitName   A unit name.
     * @param string|null $actionName An action name.
     *
     * @return void
     */
    public function dispatch($unitName = null, $actionName = null)
    {

        if ($this->user === null) {
            // no user type has been set, use the default no privilege user
            $this->user = new User($this);
        }

        // unit and action are by reference
        $this->normalizeUnitAction($unitName, $actionName);

        // set request unit and action
        $this->requestAction      = $actionName;
        $this->requestUnit        = $unitName;
        $this->mojavi['request_action'] = $actionName;
        $this->mojavi['request_unit']   = $unitName;

        // paths
        $this->mojavi['controller_path']     = $this->getControllerPath();
        $this->mojavi['current_action_path']
            = $this->getControllerPath($unitName, $actionName);
        $this->mojavi['current_unit_path'] = $this->getControllerPath($unitName);
        $this->mojavi['request_action_path']
            = $this->getControllerPath($unitName, $actionName);
        $this->mojavi['request_unit_path'] = $this->getControllerPath($unitName);

        // process the originally requested action
        $this->forward($unitName, $actionName);

        // shutdown DomainManager
        $this->domainManager->shutdown();

    }

    /**
     * Forward the request to an action.
     *
     * @param string $unitName   A unit name.
     * @param string $actionName An action name.
     *
     * @return void
     */
    public function forward($unitName, $actionName)
    {
        if ($this->currentUnit == $unitName
            && $this->currentAction == $actionName
        ) {
            $error = 'Recursive forward on unit ' . $unitName
                . ', action ' . $actionName;

            throw new RecursiveForwardException($error);
        }

        if ($this->actionExists($unitName, $actionName)) {
            // create the action instance
            $action = $this->getAction($unitName, $actionName);
        } else {
            // the requested action doesn't exist
            $action = null;
        }

        // track old unit/action
        $oldAction = $this->currentAction;
        $oldUnit = $this->currentUnit;

        // add unit and action to execution chain, and update current vars
        $this->execChain->addRequest($unitName, $actionName, $action);
        $this->updateCurrentVars($unitName, $actionName);

        if ($action === null) {
            // requested action doesn't exist, get configured recovery action
            $actionName = $this->config->get('ERROR_404_ACTION', 'PageNotFound');
            $unitName = $this->config->get('ERROR_404_UNIT', 'App');

            if (!$this->actionExists($unitName, $actionName)) {
                // cannot find error 404 unit/action
                $error = 'Invalid recovery action for not found: ' .
                        $this->nameSpace . ' - ' .
                        'ERROR_404_UNIT (' . $unitName . '), ' .
                        'ERROR_404_ACTION (' . $actionName . ')';

                throw new InvalidConfigurationException($error);
            }

            // add another request since the action is non-existent
            $action = $this->getAction($unitName, $actionName);

            $this->execChain->addRequest($unitName, $actionName, $action);
            $this->updateCurrentVars($unitName, $actionName);

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
     * Retrieve an action implementation instance.
     *
     * @param string $unitName   A unit name.
     * @param string $actionName An action name.
     *
     * @return Action An Action instance, if the action exists, otherwise
     *                an error will be reported.
     */
    public function getAction($unitName, $actionName)
    {
        $classname = $this->getComponentName('action', $unitName, $actionName, '');

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
     * Retrieve an absolute web path to the public controller file.
     *
     * @param string|null $unitName   A unit name.
     * @param string|null $actionName An action name.
     *
     * @return string An absolute web path representing the controller file,
     *                which also includes unit and action names.
     */
    public function getControllerPath($unitName = null, $actionName = null)
    {

        $path = $this->config->get('SCRIPT_PATH');

        $varsep = '?';

        if (!(empty($unitName)
            || $unitName==$this->config->get('DEFAULT_UNIT', 'App'))
        ) {
            $path .= $varsep.$this->config->get('UNIT_ACCESSOR', 'unit')."=$unitName";
            $varsep = '&';
        }
        if (!empty($actionName)) {
            $path .= $varsep.$this->config->get('ACTION_ACCESSOR', 'action')."=$actionName";
        }

        return $path;

    }

    /**
     * Generate a URL for a given unit, action and parameters
     *
     * @param string $unitName   a unit name
     * @param string $actionName an action name
     * @param array  $params     an associative array of additional URL parameters
     *
     * @return string A URL to a Mojavi resource.
     */
    public function getControllerPathWithParams($unitName, $actionName, $params)
    {

        $url=$this->getControllerPath($unitName, $actionName);
        $divider = (strpos($url, '?')===false) ? '?' : '&';

        foreach ($params as $k => $v) {
            $url .= $divider . urlencode($k) . '=' .  urlencode($v);
            $divider  = '&'; // from here on we append
        }

        return $url;

    }

    /**
     * Generate a formatted Xadr URL.
     *
     * @param array $params An associative array of URL parameters.
     *
     * @return string A URL
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
     * @param string $actionName   An Action name
     * @param string $responseName A Response name
     *
     * @return object|null Responder instance, or null if responder does not exist
     */
    public function getResponder($unitName, $actionName, $responseName)
    {
        $classname = $this->getComponentName('responder', $unitName, $actionName, $responseName);

        return class_exists($classname) ? new $classname($this) : null;
    }

    /**
     * Map a filter
     *
     * @param FilterChain $filterChain A FilterChain instance.
     * @param string      $className   Class name of a FilterList
     *
     * @return void
     */
    protected function mapFilter($filterChain, $className)
    {
        static $cache = array();

        if (!isset($cache[$className])) {
            $cache[$className] = null;
            if (class_exists($className)) {
                $object = new $className($this);
                if ($object instanceof FilterList) {
                    $cache[$className] = $object;
                    $cache[$className]->registerFilters($filterChain);
                }
            }
        } else {
            if ($cache[$className]) {
                $cache[$className]->registerFilters($filterChain);
            }
        }
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
        $className = $this->nameSpace . "\\GlobalFilterList";
        $this->mapFilter($filterChain, $className);
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
        $listName = $unitName . 'FilterList';
        $className = $this->getComponentName(
            'filterlist',
            $unitName,
            $listName,
            ''
        );
        $this->mapFilter($filterChain, $className);
    }

    /**
     * get parameters.
     *
     * @return array An associative array of parameters.
     */
    protected function getParameters()
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

        // load GET params into $values array
        $values = \Xmf\Request::get('GET', 0);

        // merge POST params into $values array
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
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Update current unit and action data.
     *
     * @param string $unitName   A unit name.
     * @param string $actionName An action name.
     *
     * @return void
     */
    protected function updateCurrentVars($unitName, $actionName)
    {

        // alias objects for easy access
        $mojavi =& $this->mojavi;

        $this->currentUnit = $unitName;
        $this->currentAction = $actionName;

        // names
        $mojavi['current_action'] = $actionName;
        $mojavi['current_unit'] = $unitName;

        // paths
        $mojavi['current_action_path']
            = $this->getControllerPath($unitName, $actionName);
        $mojavi['current_unit_path'] = $this->getControllerPath($unitName);

    }

    /**
     * Determine if a response exists.
     *
     * @param string $unitName     A unit name.
     * @param string $actionName   An action name.
     * @param string $responseName A response name.
     *
     * @return bool TRUE if the response class exists, otherwise FALSE.
     */
    public function responseExists($unitName, $actionName, $responseName)
    {

        $classname = $this->getComponentName('responder', $unitName, $actionName, $responseName);

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
     * getExternalCom - get the ExternalCom object
     *
     * @return object|string|null
     */
    public function getExternalCom()
    {
        return $this->externalCom;
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
     * @param string $unitName - A unit name
     *
     * @return object|null
     */
    public function getDomain($name, $unitName)
    {
        $classname = $this->getComponentName('domain', $unitName, $name, '');
        return class_exists($classname) ?  new $classname($this) : null;
    }
}
