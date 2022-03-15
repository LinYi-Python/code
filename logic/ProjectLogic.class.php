<?php

use \sure\tools\SureCurl;

require_once 'Web3Logic.class.php';

class ProjectLogic {

	static $sHttp = "";

	/**
	 * 创建项目
	 */
	static public function createProject($sAddress, $sProjectName, $fullFilePath = '') {
		// 对项目名编码，以防止乱码
		// $sProjectName = urlencode($sProjectName);
		if ($fullFilePath == '') {
			// 处理
			$sUrl = Web3Logic::getWeb3Http()."/project/create/".$sAddress;
			$arrParams = array('sProjectName' => $sProjectName);
		} else {
			// 参数是项目名和项目文件夹的路径
			$sUrl = Web3Logic::getWeb3Http()."/project/files/" . $sAddress;
			$arrParams = array('sProjectName' => $sProjectName, 'sPath' => $fullFilePath);
		}

        $oRet = SureCurl::post($sUrl, $arrParams);
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "创建项目传递的参数：arrParams: ". json_encode($arrParams));
		return $oRet;
	}

	/**
	 * 拉取个人项目
	 */
	static public function getProjectList($sAddress) {
		$sUrl = Web3Logic::getWeb3Http()."/project/list/".$sAddress;
        $sData = file_get_contents($sUrl);
		// $arrContent = json_decode($sData, true);
		return $sData;
	}


	/*
		拉取项目详情
	 */
	static public function getProjectDetail($sAddress, $sProjectName, $sPath) {
		// 对项目名编码，以防止乱码
		// $sProjectName = urlencode($sProjectName);
		$sUrl = Web3Logic::getWeb3Http()."/project/dir/" . $sAddress;
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "拉取项目url： sUrl: " . $sUrl);
		$arrParams = array('sProjectName' => $sProjectName, 'sPath' => $sPath);
		$oRet = SureCurl::post($sUrl, $arrParams);
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "拉取项目详情： sProjectName: " .$sProjectName." --- sUrl: $arrParams => " . $sUrl . ' | ' . $arrParams . ' ['. $oRet .']结果：' . json_encode($oRet));
		return $oRet;
	}

	/*

		创建文件夹createDir
	 */
	static public function createDir($sAddress, $sProjectName, $sPath) {
		// $sProjectName = urlencode($sProjectName);
		$sUrl = Web3Logic::getWeb3Http()."/project/mkdir/" . $sAddress;
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "创建文件夹url： sUrl: " . $sUrl);
		$arrParams = array('sProjectName' => $sProjectName, 'sPath' => $sPath);
		$oRet = SureCurl::post($sUrl, $arrParams);
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "创建文件夹详情： sProjectName: " .$sProjectName." --- sUrl: $arrParams => " . $sUrl . ' | ' . $arrParams . ' ['. $oRet .']结果：' . json_encode($oRet));
		return $oRet;
	}

	// 删除项目
	static public function deleteProject($sAddress, $sProjectName) {
		// $sProjectName = urlencode($sProjectName);
		$sUrl = Web3Logic::getWeb3Http()."/project/removeProject/" . $sAddress;
		$arrParams = array('sProjectName' => $sProjectName);
		$oRet = SureCurl::post($sUrl, $arrParams);
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "删除文件夹 sAddress: {$sAddress} , sProjectName: {$sProjectName}, sUrl: {$sUrl}， 结果：oRet:". json_encode($oRet));
		return $oRet;
	}

	// 投票
	static public function vote($sProjectName, $sProjectOwner, $iSupportCount, $sFromUser, $sPK) {
		// $sProjectName = urlencode($sProjectName);
		$sUrl = Web3Logic::getWeb3Http()."/project/support";
		$arrParams = array(
			'sProjectName' => $sProjectName,
			'sProjectOwner' => $sProjectOwner,
			'iSupportCount' => $iSupportCount,
			'sFromUser' => $sFromUser,
			'sPK' => $sPK
			);
		$oRet = SureCurl::post($sUrl, $arrParams);
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "投票信息 arrParams:". json_encode($arrParams));
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "投票信息 sProjectName: {$sProjectName} , sProjectOwner: {$sProjectOwner}, sUrl: {$sUrl}， sFromUser：{$sFromUser}, 投的币数-iSupportCount{$iSupportCount}, 结果：oRet:". json_encode($oRet));
		return $oRet;
	}

	// 投票
	static public function getSupportCount($sProjectName, $sProjectOwner) {
		// $sProjectName = urlencode($sProjectName);
		$sUrl = Web3Logic::getWeb3Http()."/project/supportCount";
		$arrParams = array(
			'sProjectName' => $sProjectName,
			'sProjectOwner' => $sProjectOwner
			);
		$oRet = SureCurl::post($sUrl, $arrParams);
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "拉取的结果：-". json_encode($oRet) ."-拉取投票信息 arrParams:". json_encode($arrParams));
		return $oRet;
	}

	// 判断项目名是否合法
	static public function isValidFileName($name) {
		// return true;
		$reg = '/[*\\/\\?"<>|]+/';
		return preg_match($reg, $name) ? false : true;
	}
}

