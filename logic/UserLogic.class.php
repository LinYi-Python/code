<?php

use \sure\db\SureDBManager;

require_once 'Web3Logic.class.php';

class UserLogic {

  static function salt () {
     $sRet = "";

     for ($i = 1; $i <= 8; $i++) {
         $sRet .= chr(rand(97, 122));
     }

     return $sRet;
  }

 /*
  static function checkPassword($sRawPassword, $sSalt, $sPassword) {
    $sPwdHash = sha1($sSalt.$sRawPassword."_CODERCHAIN");


  }*/

  static function hashPassword ($sPassword, $sSalt) {
    return sha1($sSalt.$sPassword."_CODERCHAIN");
  }

	static function login($sUserName, $sPwd) {
		$arrRet = array();
		$dbCon = SureDBManager::getDBManager(DB_CODERCHAIN);

		// 获取盐
		$sql = "SELECT * FROM tbUser WHERE `sUserName` = '".$sUserName."'";
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "$sql");
		$iCount = $dbCon->query($sql, $arrRet);
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "$sql -> ".$iCount);

		if ($iCount == 0) {
			SURE_LOG(__FILE__, __LINE__, LP_ERROR, "{$sUserName} {$sPwd} 找不到..");
			return false;
		}

    $sRawPassword = self::hashPassword($sPwd, $arrRet[0]['sSalt']);

    if ($sRawPassword != $arrRet[0]['sPwdHash']) {
      SURE_LOG(__FILE__, __LINE__, LP_ERROR, "{$sRawPassword}  - ". $arrRet[0]['sPwdHash']."不匹配");
      return false;
    }


    //
		// 未保存session
        UserLogic::saveLoginState($arrRet[0]['sAddress']);
		return array('id' => $arrRet[0]['id'], 'sUserName' => $arrRet[0]['sUserName'], 'sUserAddr' => $arrRet[0]['sAddress']);
	}

    static function register($sUserName, $sPwd, $sPwd2) {
        $dbCon = SureDBManager::getDBManager(DB_CODERCHAIN);
        $arrRetWeb3 = Web3Logic::createAccount();

        SURE_LOG(__FILE__, __LINE__, LP_INFO, "用户申请注册web3时的参数信息：arrRetWeb3: ". json_encode($arrRetWeb3));
        // $arrRetWeb3 = array();
        $sPK = $arrRetWeb3['privateKey'];
        $sAddress = $arrRetWeb3['address'];

        if ($sPK == '' || $sAddress == '') {
            // web3创建用户地址失败
            SURE_LOG(__FILE__, __LINE__, LP_ERROR, "注意，用户申请注册web3时的没有返回账户信息：arrRetWeb3: ". json_encode($arrRetWeb3));
            return false;
        }

        if (substr($sAddress, 0, 2) != '0x') {
            SURE_LOG(__FILE__, __LINE__, LP_ERROR, "警告，用户申请注册web3时的返回的地址不是0x开头！！结果是：arrRetWeb3: ". json_encode($arrRetWeb3));
            return false;
        }

        $sSalt = self::salt();
        $sPwdHash = self::hashPassword($sPwd, $sSalt);

        SURE_LOG(__FILE__, __LINE__, LP_INFO, "web3: 私钥=> {$sPK} 地址=> {$sAddress}");
        //
        $sql = "INSERT INTO tbUser SET `sUserName` = '".$sUserName."', `sPwd` = '', `sPK` = '". $sPK ."', `sAddress` = '". $sAddress ."', `sPwdHash` = '".$sPwdHash."', `sSalt` = '".$sSalt."' ";

        SURE_LOG(__FILE__, __LINE__, LP_INFO, "$sql");
        $iCount = $dbCon->insert($sql, $arrRet);
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "$sql -> ".$iCount);

        if ($iCount < 1) {
            SURE_LOG(__FILE__, __LINE__, LP_ERROR, "{$sUserName} {$sPwd} 插入数据库失败..");
            return false;
        }

        // 未保存session
        return true;
    }

    static function logout() {
        unset($_SESSION["user_id"]);
        return true;
    }

    static function isUserExisted($sUserName) {
        $dbCon = SureDBManager::getDBManager(DB_CODERCHAIN);
        $sql = "SELECT * FROM tbUser WHERE `sUserName` = '".$sUserName. "'";
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "$sql");
        $iCount = $dbCon->query($sql, $arrRet);
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "$sql -> ".$iCount);

        if ($iCount == 0) {
            return false;
        }

        return true;
    }

    static function getUserPK($sAddress) {
        $arrRet = array();
        $dbCon = SureDBManager::getDBManager(DB_CODERCHAIN);
        $sql = "SELECT * FROM tbUser WHERE `sAddress` = '".$sAddress."'";
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "$sql");
        $iCount = $dbCon->query($sql, $arrRet);
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "$sql -> ".$iCount);

        if ($iCount == 0) {
            SURE_LOG(__FILE__, __LINE__, LP_ERROR, "{$sAddress} 的 密钥 找不到..");
            return false;
        }

        return $arrRet[0]['sPK'];
    }

	static function saveLoginState($sAddress) {
        SURE_LOG(__FILE__, __LINE__, LP_ERROR, "保存用户登录态：{$sAddress}");
        $_SESSION["user_id"] = $sAddress;
        SURE_LOG(__FILE__, __LINE__, LP_ERROR, "保存之后获取用户登录态：" . $_SESSION["user_id"]);
	}

	static function isLogin() {

        return isset($_SESSION["user_id"]);

	}

    static function getUserAddress() {
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "getUserAddress -> 获取用户登录的session: " . $_SESSION['user_id']);
        return isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;
    }
    static public function getToken($sAddress) {
        $sUrl = Web3Logic::getWeb3Http()."/users/info/" . $sAddress;
        $sData = file_get_contents($sUrl);
        return $sData;
    }
}


