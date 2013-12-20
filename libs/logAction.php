<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

try
{
    if (empty($_POST['aa_inst_id']))
    {
        throw new \Exception('aa_inst_id was not sent by request in ' . __FILE__);
    }
    $aa_inst_id = $_POST['aa_inst_id'];

    if (empty($_POST['data']) && !is_array($_POST['data']))
    {
        throw new \Exception('data was not sent by request or not as array in ' . __FILE__);
    }

    $data      = $_POST['data'];
    $admin_log = false;
    if (!empty($data['data_obj']['admin']))
    {
        $admin_log = (object)$data['data_obj']['admin'];
        unset($data['data_obj']['admin']);
    }
    $data = (object)$data;

    if (!isset($data->auth_uid) || !isset($data->data_obj) || !isset($data->scope))
    {
        throw new \Exception('needet data keys not exists (auth_uid, data_obj, action) in ' . __FILE__);
    }

    // get agent id from database
    $sql = "SELECT
                id
            FROM
                mod_log_user_agents
            WHERE
                hash_id = :agent_hash
            LIMIT 1
            ";

    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $hash  = md5($agent);
    $stmt  = $db->prepare($sql);
    $stmt->bindParam(':agent_hash', $hash, PDO::PARAM_STR, 32);
    $stmt->execute();

    // if agent not exist, create a new entry and get back the new id
    if ($stmt->rowCount() === 0)
    {
        $sql = "INSERT INTO
                    mod_log_user_agents
                SET
                    hash_id = :hash,
                    data = :agent
                ";

        // prepare query
        $stmt2 = $db->prepare($sql);
        $stmt2->bindParam(':hash', $hash, PDO::PARAM_STR, 32);
        $stmt2->bindParam(':agent', $agent, PDO::PARAM_STR);
        $stmt2->execute();

        // store agent id
        $agent_id = $db->lastInsertId();
    }
    else
    {
        // store agent id
        $agent_id = $stmt->fetchColumn();
    }

    // unset statements
    unset($stmt);
    unset($stmt2);

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

    // prepare some variables
    $uid        = (int)$data->auth_uid;
    $uid_temp   = $data->auth_uid_temp;
    $ip         = get_client_ip();
    $date_added = $current_date->getTimestamp();

    // convert data object into a json string
    if (is_array($data->data_obj))
    {
        if (isset($data->data_obj['empty']))
        {
            unset($data->data_obj['empty']);
        }
        $data->data_obj = json_encode($data->data_obj);
    }

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':auth_uid', $uid, PDO::PARAM_INT);
    $stmt->bindParam(':auth_uid_temp', $uid_temp, PDO::PARAM_STR, 32);
    $stmt->bindParam(':aa_inst_id', $aa_inst_id, PDO::PARAM_STR);
    $stmt->bindParam(':data', $data->data_obj, PDO::PARAM_STR);
    $stmt->bindParam(':scope', $data->scope, PDO::PARAM_STR);
    $stmt->bindParam(':status_code', $data->code, PDO::PARAM_STR);
    $stmt->bindParam(':agend_id', $agent_id, PDO::PARAM_INT);
    $stmt->bindParam(':ip', $ip, PDO::PARAM_INT);
    $stmt->bindParam(':date_added', $date_added, PDO::PARAM_INT);

    if ($stmt->execute())
    {
        $return['code']    = 200;
        $return['status']  = 'success';
        $return['message'] = 'log successfully';
    }

    // update all user data entries, if uid is higher than 0
    if ($uid > 0)
    {
        $sql  = "UPDATE
                mod_log_user
            SET
                auth_uid = :auth_uid
            WHERE
                aa_inst_id = :aa_inst_id
            AND auth_uid = 0
            AND auth_uid_temp = :auth_uid_temp
            ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':auth_uid', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':auth_uid_temp', $uid_temp, PDO::PARAM_STR, 32);
        $stmt->bindParam(':aa_inst_id', $aa_inst_id, PDO::PARAM_STR);
        $stmt->execute();
    }
}
catch (Exception $e)
{
    // prepare return data
    $return['code']    = $e->getCode();
    $return['status']  = 'error';
    $return['message'] = $e->getMessage();
    $return['trace']   = $e->getTrace();
}
catch (PDOException $e)
{
    // prepare return data for database errors
    $return['code']    = $e->getCode();
    $return['status']  = 'error';
    $return['message'] = $e->getMessage();
    $return['trace']   = $e->getTrace();
}

// additional logs, if admin key is not empty
if ($admin_log !== false)
{
    $logging_path = ROOT_PATH . '/modules/logging/libs/logAdmin.php';
    if (file_exists($logging_path))
    {
        $_POST['aa_inst_id']  = $aa_inst_id;
        $_POST['data']['log'] = (array)$admin_log;
        require_once $logging_path;
    }
}