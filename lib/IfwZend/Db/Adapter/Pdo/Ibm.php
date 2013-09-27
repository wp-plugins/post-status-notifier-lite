<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    IfwZend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/** @see IfwZend_Db_Adapter_Pdo_Abstract */
//require_once 'IfwZend/Db/Adapter/Pdo/Abstract.php';

/** @see IfwZend_Db_Abstract_Pdo_Ibm_Db2 */
//require_once 'IfwZend/Db/Adapter/Pdo/Ibm/Db2.php';

/** @see IfwZend_Db_Abstract_Pdo_Ibm_Ids */
//require_once 'IfwZend/Db/Adapter/Pdo/Ibm/Ids.php';

/** @see IfwZend_Db_Statement_Pdo_Ibm */
//require_once 'IfwZend/Db/Statement/Pdo/Ibm.php';


/**
 * @category   Zend
 * @package    IfwZend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwZend_Db_Adapter_Pdo_Ibm extends IfwZend_Db_Adapter_Pdo_Abstract
{
    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'ibm';

    /**
     * The IBM data server connected to
     *
     * @var string
     */
    protected $_serverType = null;

    /**
     * Keys are UPPERCASE SQL datatypes or the constants
     * IfwZend_Db::INT_TYPE, IfwZend_Db::BIGINT_TYPE, or IfwZend_Db::FLOAT_TYPE.
     *
     * Values are:
     * 0 = 32-bit integer
     * 1 = 64-bit integer
     * 2 = float or decimal
     *
     * @var array Associative array of datatypes to values 0, 1, or 2.
     */
    protected $_numericDataTypes = array(
                        IfwZend_Db::INT_TYPE    => IfwZend_Db::INT_TYPE,
                        IfwZend_Db::BIGINT_TYPE => IfwZend_Db::BIGINT_TYPE,
                        IfwZend_Db::FLOAT_TYPE  => IfwZend_Db::FLOAT_TYPE,
                        'INTEGER'            => IfwZend_Db::INT_TYPE,
                        'SMALLINT'           => IfwZend_Db::INT_TYPE,
                        'BIGINT'             => IfwZend_Db::BIGINT_TYPE,
                        'DECIMAL'            => IfwZend_Db::FLOAT_TYPE,
                        'DEC'                => IfwZend_Db::FLOAT_TYPE,
                        'REAL'               => IfwZend_Db::FLOAT_TYPE,
                        'NUMERIC'            => IfwZend_Db::FLOAT_TYPE,
                        'DOUBLE PRECISION'   => IfwZend_Db::FLOAT_TYPE,
                        'FLOAT'              => IfwZend_Db::FLOAT_TYPE
                        );

    /**
     * Creates a PDO object and connects to the database.
     *
     * The IBM data server is set.
     * Current options are DB2 or IDS
     * @todo also differentiate between z/OS and i/5
     *
     * @return void
     * @throws IfwZend_Db_Adapter_Exception
     */
    public function _connect()
    {
        if ($this->_connection) {
            return;
        }
        parent::_connect();

        $this->getConnection()->setAttribute(IfwZend_Db::ATTR_STRINGIFY_FETCHES, true);

        try {
            if ($this->_serverType === null) {
                $server = substr($this->getConnection()->getAttribute(PDO::ATTR_SERVER_INFO), 0, 3);

                switch ($server) {
                    case 'DB2':
                        $this->_serverType = new IfwZend_Db_Adapter_Pdo_Ibm_Db2($this);

                        // Add DB2-specific numeric types
                        $this->_numericDataTypes['DECFLOAT'] = IfwZend_Db::FLOAT_TYPE;
                        $this->_numericDataTypes['DOUBLE']   = IfwZend_Db::FLOAT_TYPE;
                        $this->_numericDataTypes['NUM']      = IfwZend_Db::FLOAT_TYPE;

                        break;
                    case 'IDS':
                        $this->_serverType = new IfwZend_Db_Adapter_Pdo_Ibm_Ids($this);

                        // Add IDS-specific numeric types
                        $this->_numericDataTypes['SERIAL']       = IfwZend_Db::INT_TYPE;
                        $this->_numericDataTypes['SERIAL8']      = IfwZend_Db::BIGINT_TYPE;
                        $this->_numericDataTypes['INT8']         = IfwZend_Db::BIGINT_TYPE;
                        $this->_numericDataTypes['SMALLFLOAT']   = IfwZend_Db::FLOAT_TYPE;
                        $this->_numericDataTypes['MONEY']        = IfwZend_Db::FLOAT_TYPE;

                        break;
                    }
            }
        } catch (PDOException $e) {
            /** @see IfwZend_Db_Adapter_Exception */
            //require_once 'IfwZend/Db/Adapter/Exception.php';
            $error = strpos($e->getMessage(), 'driver does not support that attribute');
            if ($error) {
                throw new IfwZend_Db_Adapter_Exception("PDO_IBM driver extension is downlevel.  Please use driver release version 1.2.1 or later", 0, $e);
            } else {
                throw new IfwZend_Db_Adapter_Exception($e->getMessage(), $e->getCode(), $e);
            }
        }
    }

    /**
     * Creates a PDO DSN for the adapter from $this->_config settings.
     *
     * @return string
     */
    protected function _dsn()
    {
        $this->_checkRequiredOptions($this->_config);

        // check if using full connection string
        if (array_key_exists('host', $this->_config)) {
            $dsn = ';DATABASE=' . $this->_config['dbname']
            . ';HOSTNAME=' . $this->_config['host']
            . ';PORT='     . $this->_config['port']
            // PDO_IBM supports only DB2 TCPIP protocol
            . ';PROTOCOL=' . 'TCPIP;';
        } else {
            // catalogued connection
            $dsn = $this->_config['dbname'];
        }
        return $this->_pdoType . ': ' . $dsn;
    }

    /**
     * Checks required options
     *
     * @param  array $config
     * @throws IfwZend_Db_Adapter_Exception
     * @return void
     */
    protected function _checkRequiredOptions(array $config)
    {
        parent::_checkRequiredOptions($config);

        if (array_key_exists('host', $this->_config) &&
        !array_key_exists('port', $config)) {
            /** @see IfwZend_Db_Adapter_Exception */
            //require_once 'IfwZend/Db/Adapter/Exception.php';
            throw new IfwZend_Db_Adapter_Exception("Configuration must have a key for 'port' when 'host' is specified");
        }
    }

    /**
     * Prepares an SQL statement.
     *
     * @param string $sql The SQL statement with placeholders.
     * @param array $bind An array of data to bind to the placeholders.
     * @return PDOStatement
     */
    public function prepare($sql)
    {
        $this->_connect();
        $stmtClass = $this->_defaultStmtClass;
        $stmt = new $stmtClass($this, $sql);
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt;
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $this->_connect();
        return $this->_serverType->listTables();
    }

    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * COLUMN_POSITION  => number; ordinal position of column in table
     * DATA_TYPE        => string; SQL datatype name of column
     * DEFAULT          => string; default expression of column, null if none
     * NULLABLE         => boolean; true if column can have nulls
     * LENGTH           => number; length of CHAR/VARCHAR
     * SCALE            => number; scale of NUMERIC/DECIMAL
     * PRECISION        => number; precision of NUMERIC/DECIMAL
     * UNSIGNED         => boolean; unsigned property of an integer type
     * PRIMARY          => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     *
     * @todo Discover integer unsigned property.
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        $this->_connect();
        return $this->_serverType->describeTable($tableName, $schemaName);
    }

    /**
     * Inserts a table row with specified data.
     * Special handling for PDO_IBM
     * remove empty slots
     *
     * @param mixed $table The table to insert data into.
     * @param array $bind Column-value pairs.
     * @return int The number of affected rows.
     */
    public function insert($table, array $bind)
    {
        $this->_connect();
        $newbind = array();
        if (is_array($bind)) {
            foreach ($bind as $name => $value) {
                if($value !== null) {
                    $newbind[$name] = $value;
                }
            }
        }

        return parent::insert($table, $newbind);
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param string $sql
     * @param integer $count
     * @param integer $offset OPTIONAL
     * @return string
     */
    public function limit($sql, $count, $offset = 0)
    {
       $this->_connect();
       return $this->_serverType->limit($sql, $count, $offset);
    }

    /**
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT
     * column.
     *
     * @param string $tableName OPTIONAL
     * @param string $primaryKey OPTIONAL
     * @return integer
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        $this->_connect();

         if ($tableName !== null) {
            $sequenceName = $tableName;
            if ($primaryKey) {
                $sequenceName .= "_$primaryKey";
            }
            $sequenceName .= '_seq';
            return $this->lastSequenceId($sequenceName);
        }

        $id = $this->getConnection()->lastInsertId();

        return $id;
    }

    /**
     * Return the most recent value from the specified sequence in the database.
     *
     * @param string $sequenceName
     * @return integer
     */
    public function lastSequenceId($sequenceName)
    {
        $this->_connect();
        return $this->_serverType->lastSequenceId($sequenceName);
    }

    /**
     * Generate a new value from the specified sequence in the database,
     * and return it.
     *
     * @param string $sequenceName
     * @return integer
     */
    public function nextSequenceId($sequenceName)
    {
        $this->_connect();
        return $this->_serverType->nextSequenceId($sequenceName);
    }

    /**
     * Retrieve server version in PHP style
     * Pdo_Idm doesn't support getAttribute(PDO::ATTR_SERVER_VERSION)
     * @return string
     */
    public function getServerVersion()
    {
        try {
            $stmt = $this->query('SELECT service_level, fixpack_num FROM TABLE (sysproc.env_get_inst_info()) as INSTANCEINFO');
            $result = $stmt->fetchAll(IfwZend_Db::FETCH_NUM);
            if (count($result)) {
                $matches = null;
                if (preg_match('/((?:[0-9]{1,2}\.){1,3}[0-9]{1,2})/', $result[0][0], $matches)) {
                    return $matches[1];
                } else {
                    return null;
                }
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }
}
