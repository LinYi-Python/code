<?php

require_once '/data/sure/SureInit.inc.php';

use \sure\db\SureDBManager;

class testController extends SureController{

    // 日志路径
    public $m_sFilePath = '/data/log/coderchain/project/';

    public $m_iType = WEB_JSON;

    public function initAction() {
        global $g_sFileName;
        $g_sFileName = __FILE__;
    }


    public function indexAction() {

        $arrStr = array('sName');
        $this->mustParamsForString($arrStr);

        $dbCon = SureDBManager::getDBManager(DB_CODERCHAIN);
        $sql = "SELECT * FROM tbUser WHERE `sUserName` = '".$this->m_arrParams['sName']."' LIMIT 1";
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "$sql");
        $iCount = $dbCon->query($sql, $arrRet);
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "$sql -> ".$iCount);

        var_dump($arrRet);

    }
}

RUN_CONTROLLER("testController");
