<?php

require_once '/data/sure/SureInit.inc.php';
// 相对路径不行，在定时任务中是相对于/var/spool/root的路径
// require_once '../dev/logic/exUserLogic.class.php';
// require_once '../dev/logic/ProjectLogic.class.php';
require_once '/data/dep/coderchain/dev/logic/exUserLogic.class.php';
require_once '/data/dep/coderchain/dev/logic/ProjectLogic.class.php';
use \sure\tools\SureCurl;

class generateProjectRankTask extends SureController
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

				// 这里再循环获取项目的Token支持数
				$oRet = projectLogic::getSupportCount($oUserProject['name'], $sWalletAddress);
				$iToken = 0;

				$oRet = json_decode($oRet, true);
				if ($oRet['iCode'] != 0) {
					SURE_LOG(__FILE__, __LINE__, LP_INFO, "拉取项目投票失败: projectName => " . $oUserProject['name'] . "sAddress => " . $sWalletAddress . ' 拉取列表结果：' . json_encode($oRet));
					$iToken = '---';
				}

				$iToken = $oRet['sMsg'];
				$oUserProject['iSupportToken'] = $iToken;
				$arrRet[] = $oUserProject;
			}
		}

		$sStaticFilePath = '/data/coderchain-static-files/project-rank-list.json';
		$sJsonText =  json_encode($arrRet);
		// 把这个内容写进去
		file_put_contents($sStaticFilePath, $sJsonText);
		// $this->echoRet(0, '写入文件成功', $sJsonText);
	}
}

RUN_CONTROLLER( "generateProjectRankTask");
