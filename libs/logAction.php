<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once('Log.php');

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

    $obj        = (object)$_POST['data']['data'];
    $obj->scope = $_POST['data']['scope'];

    if (!isset($obj->auth_uid) || !isset($obj->data_obj) || !isset($obj->scope))
    {
        throw new \Exception('needet data keys not exists (auth_uid, data_obj, action) in ' . __FILE__);
    }

    $log = new \com\apparena\modules\logging\Log($db, $current_date);
    $log->setAaInstId($aa_inst_id)
        ->setScope($obj->scope)
        ->setCode($obj->code)
        ->setUid($obj->auth_uid)
        ->setUidTemp($obj->auth_uid_temp)
        ->setLoggingData($obj->data_obj)
        ->log('action');

    $status = $log->getStatusMessage();
    if ($status->errorCount === 0)
    {
        $return['code']    = 200;
        $return['status']  = 'success';
        $return['message'] = implode(', ', $status->success);
    }

    // update all user data entries, if uid is higher than 0
    $log->updateUserId();
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