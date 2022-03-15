<?php

require_once '/data/sure/SureInit.inc.php';

use \sure\tools\SureCurl;

class projectListTask extends SureController {

	// 日志路径
	public $m_sFilePath = '/data/log/coderchain/user/';

    public $m_iType = CRONTAB;

	public function initAction() {

        $this->m_sDomain = "coderchain.cn";
		global $g_sFileName;
		$g_sFileName = __FILE__;

	}

    public function indexAction() {

    	$sHttp = "http://172.18.55.4:50011/project/all";
    	$sData = file_get_contents($sHttp);

        var_dump($sData);

    	// 获取了所有钱包的地址
    	$arrData = json_decode($sData, true);

    	// var_dump($arrData);
    	$arrUser = $arrData["oRet"];

    	// var_dump($arrUser);
    	$arrRet = array();

    	foreach ($arrUser as $item) {

    		$itemRet = array();

    		// 获取当前人的项目列表
    		$sHttp = "http://172.18.55.4:50011/project/list/".$item["name"];
    		// var_dump($sHttp);
    		$sData = file_get_contents($sHttp);

            var_dump($sData);

    		$arrUserProjectList = json_decode($sData, true);
    		// var_dump($arrUserProjectList);

    		$itemRet['name'] = $item["name"];

    		if ($arrUserProjectList["iCode"] == 0 && count($arrUserProjectList["oRet"]) > 0) {
    			// $itemRet['sProjectName'] = $arrUserProjectList[]
                // $itemRet['projectList'] = $arrUserProjectList['oRet'];

                $arrProjectList = array();

                foreach ($arrUserProjectList['oRet'] as $itemUserProjectList) {
                    $arrProjectList[] = $itemUserProjectList['name'];    
                }

                $itemRet['projectList'] = $arrProjectList;
                $arrRet[] = $itemRet;
    		}
    	}

        if (count($arrRet) == 0) {
            return;
        }

        $sData = "var arrProject = ".json_encode($arrRet);
        $a  = file_put_contents("/var/www/html/coderchain/js/projectList.js", $sData);
        var_dump($a);
    }

}

RUN_CONTROLLER("projectListTask");

