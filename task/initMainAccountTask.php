<?php

/**
 * 用于创建钱包
 */

require_once '/data/sure/SureInit.inc.php';

use \sure\tools\SureCurl;
use \sure\db\SureDBManager;

class initMainAccountTask extends SureController {
	
	// 日志路径
	public $m_sFilePath = '/data/log/coderchain/user/';
	
    public $m_iType = CRONTAB;

    public function initAction() {
        global $g_sFileName;
        $g_sFileName = __FILE__;
    }

	public function indexAction() {

		/*
		$sHttp = "http://172.18.55.4:3002/users/send/0x01Ed41d1Ca9c777860464185929e7870148a62Cb";
		$sRet = file_get_contents($sHttp);
		var_dump($sRet);
		*/

		/*
		$sHttp = "http://172.18.55.4:50011/users/mainAccount";
		$dbCon = SureDBManager::getDBManager(DB_CODERCHAIN);

		for ($i = 0; $i < 80; $i++) {

			$sRet = file_get_contents($sHttp);
			$arrRet = json_decode($sRet, true);

			echo "{$sRet}\r\n";

			if ($arrRet['iCode'] != 0) {
				echo "失败\r\n";
				continue;
			}

			$sql = "INSERT INTO tbMainAddress SET `sAddress` = '".$arrRet['address']."', `sPK` = '".$arrRet['privateKey']."' ";
			SURE_LOG(__FILE__, __LINE__, LP_INFO, "$sql");
			$iCount = $dbCon->insert($sql);

        	if ($iCount < 1) {
        		SURE_LOG(__FILE__, __LINE__, LP_ERROR, "$sql -> ".$iCount);
        	}

		}*/

		
		$sql = "SELECT * FROM tbMainAddress";
		$dbCon = SureDBManager::getDBManager(DB_CODERCHAIN);

		$iCount = $dbCon->query($sql, $arrRet);
		
		
		 for ($j=0; $j < 100; $j++) { 
			for ($i = 0; $i < $iCount; $i++) {
				// $sHttp = "http://172.18.55.4:3002/users/send/".$arrRet[$i]["sAddress"];
				
				$sHttp = "http://172.18.55.4:50011/users/info/".$arrRet[$i]["sAddress"];
				$sRet = file_get_contents($sHttp);

				//cecho $arrRet[$i]["sAddress"]."\r\n";

				$arrData = json_decode($sRet, true);
				var_dump($sRet);

				/*
				if (floatval($arrData['dBalance']) < floatval('10000000000000000000')) {
					$sHttp = "http://172.18.55.4:50011/users/sendMainAccount/".$arrRet[$i]["sAddress"];
					$sRet = file_get_contents($sHttp);
					var_dump($sRet);
				} else {
					echo floatval($arrData['dBalance'])." -> no";
				}*/

				if (floatval($arrData['dBalance']) < floatval('1000000000000000000000')) {
					$sHttp = "http://172.18.55.4:50011/users/sendMainAccount/".$arrRet[$i]["sAddress"];
					$sRet = file_get_contents($sHttp);
					var_dump($sRet);
				} else {
					echo $arrRet[$i]["sAddress"]." -> no";
				}


			}			
		}

		/*
		for ($i = 0; $i < $iCount; $i++) {
			$sHttp = "http://172.18.55.4:50011/users/reset/".$arrRet[$i]["sAddress"];
			$sRet = file_get_contents($sHttp);
			$arrData = json_decode($sRet, true);
			var_dump($sRet);
		}*/

	}

}

RUN_CONTROLLER("initMainAccountTask");
