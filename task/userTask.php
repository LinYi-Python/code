<?php

require_once '/data/sure/SureInit.inc.php';

use \sure\tools\SureCurl;
use \sure\db\SureDBManager;

class userTask extends SureController {

	// 日志路径
	public $m_sFilePath = '/data/log/coderchain/user/';

    public $m_iType = CRONTAB;

	public function initAction() {
		global $g_sFileName;
		$g_sFileName = __FILE__;

	}

    public function indexAction() {

        /*
        for ($i=0; $i < 100; $i++) { 
            $sRet = $this->salt();
            var_dump($sRet);
        }*/

        $sql = "SELECT * FROM tbUser";
        $dbCon = SureDBManager::getDBManager(DB_CODERCHAIN);

        $iCount = $dbCon->query($sql, $arrRet);

        for ($i=0; $i < $iCount; $i++) {

            if ($arrRet[$i]['sSalt'] != '') {
                continue;
            }

            $sSalt = $this->salt();

            $sPwdHash = sha1($sSalt.$arrRet[$i]['sPwd']."_CODERCHAIN");
            $sId = $arrRet[$i]['id'];
            $sql = "UPDATE tbUser SET `sPwdHash` = '".$sPwdHash."', `sSalt` = '".$sSalt."' WHERE id = '".$sId."'";

            $iRet = $dbCon->update($sql);

            if ($iRet < 1) {
                echo $sql."\r\n";
                return;
            }

        }

    }

    function salt () {
        $sRet = "";

        for ($i = 1; $i <= 8; $i++) {
            $sRet .= chr(rand(97, 122));
        }

        return $sRet;
    }

}

RUN_CONTROLLER("userTask");
