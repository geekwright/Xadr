<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr;

/**
 * A ValidatorManager provides the mechanism to register specific
 * validator objects, the contolling properties for those objects and
 * the input parameters to be validated. The ValidatorManager also
 * provides the method to execute the registered validations.
 *
 * The ExecutionFilter establishes a ValidatorManager, and invokes the
 * Action::registerValidators() method to establish the validations
 * to be performed.
 *
 * @category  Xmf\Xadr\ValidatorManager
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class ValidatorManager extends ContextAware
{

    /**
     * An associative array of parameter validators.
     *
     * @var array
     */
    protected $validators = array();


    /**
     * Execute all validators.
     *
     * _This method should never be called manually._
     *
     * @return bool true if all validations passed, otherwise false
     */
    public function execute()
    {
        $success = true;

        foreach ($this->validators as $param => $validatorDefinition) {
            $value    =  $this->request()->getParameter($param);
            $required =  $validatorDefinition['required'];

            if (isset($validatorDefinition['validators'])) {
                foreach ($validatorDefinition['validators'] as $validator) {
                    if (!$validator->execute($value)) {
                        $this->request()->setError($param, $validator->getErrorMessage());
                        $success = false;
                        break;
                    }
                }
            }

            if ($required && empty($value) && $value !== 0) {
                // param is required but doesn't exist
                $message = $this->validators[$param]['message'];
                $this->request()->setError($param, $message);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Register a validator.
     *
     * @param string $param     A parameter name to be validated.
     * @param object $validator A Validator instance.
     *
     * @return void
     */
    public function register($param, $validator)
    {
        if (!isset($this->validators[$param])) {
            $this->validators[$param] = array();
        }

        if (!isset($this->validators[$param]['validators'])) {
            $this->validators[$param]['validators'] = array();
        }

        // add this validator to the list for this parameter
        $this->validators[$param]['validators'][] = $validator;

        // if a required status has not yet been specified, set one.
        if (!isset($this->validators[$param]['required'])) {
            $this->setRequired($param, false);
        }
    }

    /**
     * Set the required status of a parameter.
     *
     * @param string      $name     A parameter name.
     * @param bool        $required The required status.
     * @param string|null $message  Error message to be set if the parameter
     *                              has not been sent or has a length of 0.
     *
     * @return void
     */
    public function setRequired($name, $required = true, $message = null)
    {
        if (!isset($this->validators[$name])) {
            $this->validators[$name] = array();
        }

        $this->validators[$name]['required'] = $required;
        $this->validators[$name]['message']  = empty($message)?'Required':$message;

        // if validators array has not yet been defined, initialize it.
        if (!isset($this->validators[$name]['validators'])) {
            $this->validators[$name]['validators'] = array();
        }
    }

    /**
     * Add a complete validation
     *
     * @param string $name          A parameter name.
     * @param string $validatorName The name of the validator class. If a fully
     *                              qualified namespace is not specified, will assume
     *                              class is a standard built in that is in the
     *                              \Xmf\Xadr\Validator\ namespace.
     * @param array  $initParms     $params for a Xmf\Xadr\Validator::initialize()
     *
     * @return void
     */

    public function addValidation($name, $validatorName, $initParms = array())
    {
        $validatorClass = (false === strpos($validatorName, '\\'))
            ? $validatorClass = '\Xmf\Xadr\Validator\\'.$validatorName
            : $validatorName;
        if (class_exists($validatorClass)) {
            $validator = new $validatorClass($this->context());
            if (!is_array($initParms)) {
                $initParms = array();
            }
            $validator->initialize($initParms);
            $this->register($name, $validator);
        } else {
            \Xoops::getInstance()->logger()->error("Class \"$validatorClass\" was not found");
        }
    }
}
