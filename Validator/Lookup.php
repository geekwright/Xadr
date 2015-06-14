<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (http://www.gnu.org/licenses/lgpl-2.1.html)
 */

namespace Xmf\Xadr\Validator;

/**
 * Lookup Validator provides a constraint on a parameter by making sure
 * the value is found in the specified table.
 *
 * @category  Xmf\Xadr\Validator\Lookup
 * @package   Xmf
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    Sean Kerr <skerr@mojavi.org>
 * @copyright 2013-2015 XOOPS Project (http://xoops.org)
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Lookup extends AbstractValidator
{

    /**
     * Execute this validator.
     *
     * @param string &$value parameter value - can be changed by reference.
     *
     * @return bool TRUE if the validator completes successfully, otherwise FALSE.
     */
    public function execute (&$value)
    {
        $xoops = \Xoops::getInstance();

        $table = $this->cleanName($this->params['lookup_table']);
        $column = $this->cleanName($this->params['lookup_column']);

        if (empty($table)) {
            $this->setErrorMessage($this->params['table_error']);
            return false;
        }

        if (empty($column)) {
            $this->setErrorMessage($this->params['column_error']);
            return false;
        }

        $qb = $xoops->db()->createXoopsQueryBuilder()
            ->select('count(*)')
            ->fromPrefix($table, 't')
            ->where('t.'.$column.' = :id')
            ->setParameter(':id', $value);

        if (!$result = $qb->execute()) {
            $this->setErrorMessage($this->params['lookup_error']);
            return false;
        }
        list ($count) = $result->fetch(\PDO::FETCH_NUM);

        if ($count<=0) {
            $this->setErrorMessage($this->params['lookup_error']);
            return false;
        }

        return true;

    }

    /**
     * Clean database names
     *
     * @param string $name A table name
     *
     * @return string|null cleaned name
     */
    private function cleanName($name)
    {
        $name = trim($name);
        if (preg_match('#^[a-zA-Z0-9._]*$#i', $name)) {
            return $name;
        } else {
            return null; // Contains illegal characters
        }
    }

    /**
     * getDefaultParameters
     *
     * All expected parameters should be listed here and be given a default value
     *
     * Initialization Parameters:
     *
     * Name          | Type   | Default | Required | Description
     * ------------- | ------ | ------- | -------- | -----------
     * lookup_table  | string | n/a     | yes      | database table name
     * lookup_column | string | n/a     | yes      | column to match value
     *
     * Error Messages:
     *
     * Name          | Default
     * ------------- | -------
     * lookup_error  | Lookup failed
     * table_error   | lookup_table parameter is invalid
     * column_error  | lookup_column parameter is invalid
     *
     * @return array of default parameters
     */
    public function getDefaultParams()
    {
        $defaults = array(
            'lookup_column' => '',
            'lookup_table'  => '',
            'lookup_error'  => 'Lookup failed',
            'table_error'   => 'lookup_table parameter is invalid',
            'column_error'  => 'lookup_column parameter is invalid',
        );
        return $defaults;
    }
}
