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
 * A ResponseSelector specifies which Responder object should be used for the current Action.
 * The response code is used to determine the name of the responder, which by default is derived
 * from the current unit and action. An different unit and action can also be specified.
 *
 * The response code Xadr::RESPONSE_NONE is special, as it will result in no Responder phase.
 *
 * @category  Xmf\Xadr\ResponseSelector
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2015 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class ResponseSelector
{

    /**
     * @var string The response code (Responder suffix,) typically a Xadr::RESPONSE_* constant.
     */
    protected $responseCode = null;

    /**
     * @var string Unit used to select the Responder
     */
    protected $responseUnit = null;

    /**
     * @var string Action used to select the Responder
     */
    protected $responseAction = null;

    /**
     * @param string      $responseCode   Response type code used to select Responder
     * @param string|null $responseUnit   Unit used to select Responder, null for default
     * @param string|null $responseAction Action used to select Responder, null for default
     */
    public function __construct($responseCode, $responseUnit = null, $responseAction = null)
    {
        $this->responseCode = $responseCode;
        $this->responseUnit = $responseUnit;
        $this->responseAction = $responseAction;
    }

    /**
     * get the response code
     *
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * get the response code
     *
     * @return string
     */
    public function getResponseUnit()
    {
        return $this->responseUnit;
    }

    /**
     * get the response code
     *
     * @return string
     */
    public function getResponseAction()
    {
        return $this->responseAction;
    }

    /**
     * Set the responseUnit and responseAction properties to supplied defaults if null.
     *
     * @param string $defaultUnit   Unit used to select Responder
     * @param string $defaultAction Action used to select Responder
     *
     * @return void
     */
    public function setDefaultAction($defaultUnit, $defaultAction)
    {
        if ($this->responseUnit === null) {
            $this->responseUnit = $defaultUnit;
        }
        if ($this->responseAction === null) {
            $this->responseAction = $defaultAction;
        }
    }
}
