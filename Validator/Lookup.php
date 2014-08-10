<?php
/*
 * This file has its roots as part of the Mojavi package which was
 * Copyright (c) 2003 Sean Kerr. It has been incorporated into this
 * derivative work under the terms of the LGPL V2.1.
 * (license terms)
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
 * @copyright 2013-2014 The XOOPS Project http://sourceforge.net/projects/xoops/
 * @copyright 2003 Sean Kerr
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @version   Release: 1.0
 * @link      http://xoops.org
 * @since     1.0
 */
class Lookup extends AbstractValidator
{

    /**
     * Execute this validator.
     *
     * @param string &$value A user submitted parameter value.
     * @param string &$error The error message variable to be set if an error occurs.
     *
     * @return bool TRUE if the validator completes successfully, otherwise FALSE.
     *
     * @since  1.0
     */
    public function execute(&$value, &$error)
    {
        $xoops = \Xoops::getInstance();

        $table = $this->cleanName($this->params['lookup_table']);
        $column = $this->cleanName($this->params['lookup_column']);

        if (empty($table)) {
            $error = $this->params['table_error'];

            return false;
        }

        if (empty($column)) {
            $error = $this->params['column_error'];

            return false;
        }

        $qb = $xoops->db()->createXoopsQueryBuilder()
            ->select('count(*)')
            ->fromPrefix($table, 't')
            ->where('t.'.$column.' = :id')
            ->setParameter(':id', $value);

        if (!$result = $qb->execute()) {
            return false;
        }
        list ($count) = $result->fetch(\PDO::FETCH_NUM);

        if ($count<=0) {
            $error = $this->params['lookup_error'];

            return false;
        }

        return true;

    }

    /**
     * Clean database names
     *
     * @param string $name A table name
     *
     * @return string cleaned name
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
