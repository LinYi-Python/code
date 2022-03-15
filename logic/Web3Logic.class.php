<?php

use \sure\tools\SureCurl;

class Web3Logic {

	static $sHttp = "";

	/**
	 * 是否生产环境
	 */
	static public function isProduction () {

		if (strpos($_SERVER['HTTP_HOST'], 'test') !== false) {
			return false;
		}

		return true;
	}

	/**
	 * 创建账号
	 */
	static public function createAccount() {
		$sHttp = self::getWeb3Http()."/users/create";
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "{$sHttp}");
		$sData = file_get_contents($sHttp);
		// {"address":"0x0480Ffd74Ad278e441D16aD8119E6ec6F39513c7","privateKey":"0x3242386f8710d95e57a6ee9694e4f44c8ee00e52be2694736716346ac77e33bb"}
		$arrContent = json_decode($sData, true);
		return $arrContent;
	}

	/**
	 * 获取请求web3的http地址
	 */
	static public function getWeb3Http() {

		if (self::isProduction()) {
			return "http://127.0.0.1:50011";
		}

		return "http://127.0.0.1:50011";
	}

}
