<?php
/**
 * Log
 *
 * CLASSDESCRIPTION
 *
 * @category    CATEGORY NAME
 * @package     PACKAGE NAME
 * @subpackage  PACKAGE NAME
 *
 * @author      "Marcus Merchel" <kontakt@marcusmerchel.de>
 * @copyright   Copyright (c) 2009-2013 Marcus Merchel (http://www.marcusmerchel.de/)
 * @link        http://www.marcusmerchel.de/
 * @license     http://www.marcusmerchel.de/licence/
 * @version     1.0.0 (23.12.13 - 18:20)
 */
namespace com\apparena\modules\logging;

use \com\apparena\system\Database AS DB;

class Log
{
    protected $_logging_data = array();
    protected $_scope = null;
    protected $_code = null;
    protected $_aa_inst_id = null;
    protected $_user_agent_hash = '';
    protected $_user_agent_id = 0;
    protected $_uid = 0;
    protected $_uid_temp = 0;
    protected $_db = null;
    protected $_current_time = null;
    protected $_status_message = array('error' => array(), 'success' => array());
    protected $_log_statement = null;

    public function __construct(DB $db, \DateTime $currentTime)
    {
        $this->_db           = $db;
        $this->_current_time = clone $currentTime;
    }

    /**
     * @param int $aa_inst_id
     */
    public function setAaInstId($aa_inst_id)
    {
        $this->_aa_inst_id = $aa_inst_id;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getAaInstId()
    {
        return $this->_aa_inst_id;
    }

    /**
     * @param null $code
     */
    public function setCode($code)
    {
        $this->_code = $code;

        return $this;
    }

    /**
     * @return null
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param array $logging_data
     */
    public function setLoggingData($logging_data)
    {
        $this->_logging_data = $logging_data;

        return $this;
    }

    /**
     * @return array
     */
    public function getLoggingData()
    {
        return $this->_logging_data;
    }

    /**
     * @param null $scope
     */
    public function setScope($scope)
    {
        $this->_scope = $scope;

        return $this;
    }

    /**
     * @return null
     */
    public function getScope()
    {
        return $this->_scope;
    }

    /**
     * @param int $uid
     */
    public function setUid($uid)
    {
        $this->_uid = $uid;

        return $this;
    }

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->_uid;
    }

    /**
     * @param int $uid_temp
     */
    public function setUidTemp($uid_temp)
    {
        $this->_uid_temp = $uid_temp;

        return $this;
    }

    /**
     * @return int
     */
    public function getUidTemp()
    {
        return $this->_uid_temp;
    }

    /**
     * @param string $user_agent_hash
     */
    public function setUserAgentHash($user_agent_hash)
    {
        $this->_user_agent_hash = md5($this->getUserAgent());

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgentHash()
    {
        if ($this->_user_agent_hash === null)
        {
            $this->setUserAgentHash($this->getUserAgent());
        }

        return md5($this->_user_agent_hash);
    }

    public function generateUserAgentId()
    {
        $hash  = $this->getUserAgentHash();
        $agent = $this->getUserAgent();

        $sql = "INSERT INTO
                    mod_log_user_agents
                SET
                    hash_id = " . $this->_db->quote($hash) . ",
                    data = " . $this->_db->quote($agent) . "
                ";

        $stmt = $this->_db->query($sql);
        $this->setUserAgentId($this->_db->lastInsertId());

        return $this;
    }

    public function setUserAgentId($user_agent_id)
    {
        if (!is_numeric($user_agent_id))
        {
            throw new \Exception('$user_agent_id is not numeric');
        }
        $this->_user_agent_id = $user_agent_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgentId()
    {
        if ($this->_user_agent_id === 0)
        {
            $hash = $this->getUserAgentHash();

            $sql    = "SELECT
                        id
                    FROM
                        mod_log_user_agents
                    WHERE
                        hash_id = " . $this->_db->quote($hash) . "
                    LIMIT 1
                    ";
            $stmt   = $this->_db->query($sql);
            $result = $stmt->fetchAll();

            // if agent not exist, create a new entry and get back the new id
            if (empty($result) || $stmt === false)
            {
                $this->generateUserAgentId();
            }
            else
            {
                // store agent id
                $this->setUserAgentId($result[0]->id);
            }
        }

        return $this->_user_agent_id;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return trim(strtolower($_SERVER['HTTP_USER_AGENT']));
    }

    public function log($type)
    {
        switch (strtolower($type))
        {
            case 'action':
                $this->logAction();
                break;

            case 'admin':
                $this->logAdmin();
                break;
        }
        return $this;
    }

    protected function defineLogAdminStatement()
    {
        // set log type to action
        $this->_log_statement = 'admin';

        // prepare new logging entry
        $sql = "INSERT INTO
                mod_log_adminpanel
            SET
                hash= :hash,
                scope = :scope,
                aa_inst_id = :aa_inst_id,
                value = :value,
                date_added = FROM_UNIXTIME(:date_added),
                counter = 1
            ON DUPLICATE KEY UPDATE
                counter = counter+1
            ";

        $this->_log_statement = $this->_db->prepare($sql);
    }

    protected function defineLogActionStatement()
    {
        // set log type to action
        $this->_log_statement = 'action';

        // prepare new logging entry
        $sql = "INSERT INTO
                mod_log_user
            SET
                auth_uid = :auth_uid,
                auth_uid_temp = :auth_uid_temp,
                aa_inst_id = :aa_inst_id,
                data = :data,
                scope = :scope,
                code = :status_code,
                agend_id = :agend_id,
                ip = INET_ATON(:ip),
                date_added = FROM_UNIXTIME(:date_added)
            ";

        $this->_log_statement = $this->_db->prepare($sql);
    }

    protected function logAction()
    {
        if ($this->_log_statement === null || $this->_log_statement !== 'action')
        {
            $this->defineLogActionStatement();
        }

        // define basic variables
        $timestamp  = $this->_current_time->getTimestamp();
        $aa_inst_id = $this->getAaInstId();
        $uid        = $this->getUid();
        $uid_temp   = $this->getUidTemp();
        $scope      = $this->getScope();
        $value      = json_encode($this->getLoggingData());
        $code       = $this->getCode();
        $ip         = get_client_ip();
        $agent_id   = $this->getUserAgentId();

        // bind variables to database statement
        $this->_log_statement->bindParam(':aa_inst_id', $aa_inst_id, \PDO::PARAM_STR);
        $this->_log_statement->bindParam(':date_added', $timestamp, \PDO::PARAM_STR);
        $this->_log_statement->bindParam(':scope', $scope, \PDO::PARAM_STR);
        $this->_log_statement->bindParam(':data', $value, \PDO::PARAM_STR);
        $this->_log_statement->bindParam(':auth_uid', $uid, \PDO::PARAM_INT);
        $this->_log_statement->bindParam(':auth_uid_temp', $uid_temp, \PDO::PARAM_STR, 32);
        $this->_log_statement->bindParam(':status_code', $code, \PDO::PARAM_STR);
        $this->_log_statement->bindParam(':agend_id', $agent_id, \PDO::PARAM_INT);
        $this->_log_statement->bindParam(':ip', $ip, \PDO::PARAM_INT);

        try
        {
            if ($this->_log_statement->execute())
            {
                $this->setStatusMessage('log successfully', 'success');
            }
        }
        catch (\PDOException $e)
        {
            $this->setStatusMessage($e->getMessage(), 'error');
        }
    }

    public function setStatusMessage($status_message, $type)
    {
        $this->_status_message[$type][] = $status_message;

        return $this;
    }

    public function getStatusMessage()
    {
        $return = (object)$this->_status_message;
        $return->errorCount = count($return->error);
        $return->successCount = count($return->success);
        return $return;
    }

    protected function logAdmin()
    {
        if ($this->_log_statement === null || $this->_log_statement !== 'admin')
        {
            $this->defineLogAdminStatement();
        }
        // modify time of day
        $this->_current_time->setTime(0, 0, 0);

        // define basic variables
        $timestamp  = $this->_current_time->getTimestamp();
        $aa_inst_id = $this->getAaInstId();
        $scope      = $this->getScope();
        $value      = $this->getLoggingData();
        $hash       = md5($aa_inst_id . $timestamp . $scope . $value);

        // bind variables to database statement
        $this->_log_statement->bindParam(':hash', $hash, \PDO::PARAM_STR, 32);
        $this->_log_statement->bindParam(':aa_inst_id', $aa_inst_id, \PDO::PARAM_STR);
        $this->_log_statement->bindParam(':date_added', $timestamp, \PDO::PARAM_STR);
        $this->_log_statement->bindParam(':scope', $scope, \PDO::PARAM_STR);
        $this->_log_statement->bindParam(':value', $value, \PDO::PARAM_STR);

        try
        {
            if ($this->_log_statement->execute())
            {
                $this->setStatusMessage($scope, 'success');
            }
        }
        catch (\PDOException $e)
        {
            $this->setStatusMessage($e->getMessage(), 'error');
        }

        return $this;
    }
}