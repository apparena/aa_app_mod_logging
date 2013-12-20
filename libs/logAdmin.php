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
    $data = (object)$_POST['data'];

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

    // prepare timestamp
    $current_day = new DateTime('now', new DateTimeZone($aa_default_timezone));
    $current_day->setTime(0, 0, 0);
    $timestamp = $current_day->getTimestamp();

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':hash', $hash, PDO::PARAM_STR, 32);
    $stmt->bindParam(':aa_inst_id', $aa_inst_id, PDO::PARAM_STR);
    $stmt->bindParam(':date_added', $timestamp, PDO::PARAM_STR);
    $stmt->bindParam(':scope', $scope, PDO::PARAM_STR);
    $stmt->bindParam(':value', $value, PDO::PARAM_STR);

    $return_message = '';
    if (is_array($data->log))
    {
        foreach ($data->log AS $scope => $value)
        {
            $hash = md5($aa_inst_id . $timestamp . $scope . $value);
            if ($stmt->execute())
            {
                $return_message .= $scope . ',';
            }
        }
    }

    if (!empty($return_message))
    {
        $return['code']    = 200;
        $return['status']  = 'success';
        $return['message'] = 'log successfully';
        $return['added']   = $return_message;
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