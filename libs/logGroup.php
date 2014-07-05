<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once('Log.php');

try
{
    if (empty($_POST['i_id']))
    {
        throw new \Exception('i_id was not sent by request in ' . __FILE__);
    }
    $i_id = $_POST['i_id'];

    if (empty($_POST['data']) && !is_array($_POST['data']))
    {
        throw new \Exception('data was not sent by request or not as array in ' . __FILE__);
    }
    $data = (object)$_POST['data'];

    $log = new \Apparena\Modules\Logging\Log($db, $current_date);

    foreach($data AS $scope => $value)
    {
        if(is_array($value))
        {
            // log action
            $obj = (object)$value;

            if(empty($obj->data_obj))
            {
                $obj->data_obj = json_encode(array());
            }

            $log->setAaInstId($i_id)
                ->setScope($scope)
                ->setCode($obj->code)
                ->setUid($obj->auth_uid)
                ->setUidTemp($obj->auth_uid_temp)
                ->setLoggingData($obj->data_obj)
                ->log('action');
        }
        else
        {
            // log admin action
            $log->setAaInstId($i_id)
                ->setScope($scope)
                ->setLoggingData($value)
                ->log('admin');
        }
    }

    // update all user data entries, if uid is higher than 0
    $log->updateUserId();

    // generate return
    $status = $log->getStatusMessage();
    if ($status->successCount > 0)
    {
        $return['code']    = 200;
        $return['status']  = 'success';
    }
    $return['message'] = 'Successes: ' . implode(', ', $status->success);

    if ($status->errorCount > 0)
    {
        if ($status->successCount > 0)
        {
            $return['message'] .= ', ';
        }
        $return['message'] .= 'Error: ' . implode(', ', $status->error);
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