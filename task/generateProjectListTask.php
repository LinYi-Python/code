<?php

require_once '/data/sure/SureInit.inc.php';
// 相对路径不行，在定时任务中是相对于/var/spool/root的路径
// require_once '../dev/logic/exUserLogic.class.php';
// require_once '../dev/logic/ProjectLogic.class.php';
require_once '/data/dep/coderchain/dev/logic/exUserLogic.class.php';
require_once '/data/dep/coderchain/dev/logic/ProjectLogic.class.php';
use \sure\tools\SureCurl;

class generateProjectListTask extends SureController
{

	// 日志路径
	public $m_sFilePath = '/data/log/coderchain/ex/project/crontab/';

	public $m_iType = CRONTAB;

	public function initAction()
	{

		$this->m_sDomain = "coderchain.cn";
		global $g_sFileName;
		$g_sFileName = __FILE__;
	}

	public function indexAction()
	{
		// 获取所有地址，遍历
		$arrProject = ProjectLogic::getAllChainProject();
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "拉取到所有项目列表的长度：" . count($arrProject));
		$arrRet = array();
		// sWalletAddress, $arrProjectList
		foreach ($arrProject as $oProject) {
			$sWalletAddress = $oProject['sWalletAddress'];
			$arrUserProject = $oProject['arrProject'];

			// concat
			$arrUser = exUserLogic::getUser(array('sWalletAddress' => $sWalletAddress));
			if (count($arrUser) < 1) { }
			$oUser = $arrUser[0];
			$sUserId = $oUser['sId'];
			$sAvatar = $oUser['sAvatar'];
			$sUserName = $oUser['sUserName'];
			$sDisplayName = $oUser['sDisplayName'];
			$arrUserProject = ProjectLogic::concatProjectListDetailFromDb($sUserId, $arrUserProject);
			// 再次拼接

			foreach ($arrUserProject as $oUserProject) {
				$oUserProject['sAvatar'] = $sAvatar;
				$oUserProject['sUserId'] = $sUserId;
				$oUserProject['sUserName'] = $sUserName;
				$oUserProject['sDisplayName'] = $sDisplayName;
				$arrRet[] = $oUserProject;
			}
		}

		$sStaticFilePath = '/data/coderchain-static-files/project-list.json';
		$sJsonText =  json_encode($arrRet);
		// 把这个内容写进去
		file_put_contents($sStaticFilePath, $sJsonText);
	}
}

RUN_CONTROLLER( "generateProjectListTask");
