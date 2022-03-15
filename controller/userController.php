<?php
require_once '/data/sure/SureInit.inc.php';
require_once '../logic/UserLogic.class.php';
require_once './rsa.php';

class userController extends SureController
{

	// 日志路径
    public $m_sFilePath = '/data/log/coderchain/user/';

    // public $m_iType = WEB;
    public $m_iType = WEB_JSON;

    public function initAction()
    {
        $this->m_sDomain = "coderchain.cn";
        global $g_sFileName;
        $g_sFileName = __FILE__;
    }

    public function indexAction()
    {
    }

    /**
     * 登录
     * @return [type] [description]
     */
    public function loginAction()
    {
        $arrStr = array('sUserName', 'sPwd');
        $this->mustParamsForString($arrStr);

        // 登陆逻辑
        $bRet = UserLogic::login($this->m_arrParams['sUserName'], $this->m_arrParams['sPwd']);

        if (!$bRet) {
            $this->echoRet(100, '用户名不存在或密码错误', json_encode(array('msg' => 'hell')));
        }

        $this->echoRet(0, 'success', json_encode($bRet));
    }

    /**
     * 注册
     * @return [type] [description]
     */
    public function registerAction()
    {
        $arrStr = array('sUserName', 'sPwd', 'sPwd2');
        $this->mustParamsForString($arrStr);

        // 登陆逻辑
        $bUserExisted = UserLogic::isUserExisted($this->m_arrParams['sUserName']);
        if ($bUserExisted) {
            $this->echoRet(101, '用户名已经存在，请选择其他用户名');
        }

        $bRet = UserLogic::register($this->m_arrParams['sUserName'], $this->m_arrParams['sPwd'], $this->m_arrParams['sPwd2']);

        if (!$bRet) {
            $this->echoRet(100, '注册失败，网络繁忙，请重新再试');
        }

        $this->echoRet(0, '注册成功，马上登陆吧');
    }

    /**
     * 登录 - open api方式
     * @return [type] [description]
     */
    public function openLoginAction()
    {
        $arrStr = array('sUserName', 'sPassword', 'sEncryptedAESKey');
        $this->mustParamsForString($arrStr);

        $sUserName = $this->m_arrParams['sUserName'];
        $sPassword = $this->m_arrParams['sPassword'];
        $sEncryptedAESKey = $this->m_arrParams['sEncryptedAESKey'];

        //进行解密
        $sPassword = EncryptUtil::rsa_aes_decrypt($sPassword, $sEncryptedAESKey);

        // 登陆逻辑
        $bRet = UserLogic::login($sUserName, $sPassword);

        if (!$bRet) {
            $this->echoRet(100, '用户名不存在或密码错误', json_encode(array('msg' => 'FAIL')));
        }

        $this->echoRet(0, 'success', json_encode($bRet));
    }

    /**
     * 注册 - open api方式
     * @return [type] [description]
     */
    public function openRegisterAction()
    {
        $arrStr = array('sUserName', 'sPwd', 'sPwd2');
        $this->mustParamsForString($arrStr);

        // 校验用户是否存在
        $bUserExisted = UserLogic::isUserExisted($this->m_arrParams['sUserName']);
        if ($bUserExisted) {
            $this->echoRet(101, '用户名已经存在，请选择其他用户名');
        }

        $bRet = UserLogic::register($this->m_arrParams['sUserName'], $this->m_arrParams['sPwd'], $this->m_arrParams['sPwd2']);

        if (!$bRet) {
            $this->echoRet(100, '注册失败，网络繁忙，请重新再试');
        }

        $this->echoRet(0, '注册成功，马上登陆吧');
    }


    /**
     * 退出登录
     * @return [type] [description]
     */
    public function logoutAction()
    {
        UserLogic::logout();
        $this->echoRet(0, '退出成功');
    }

    /**
     * 获取用户的币数
     * @return [type] [description]
     */
    public function getTokenAction()
    {
        $this->mustCoderchainLogin();

        $sAddress = UserLogic::getUserAddress();
        $oRet = UserLogic::getToken($sAddress);

        $oRet = json_decode($oRet, true);
        if ($oRet['iCode'] != 0) {
            $this->echoRet(100, '获取Token失败', 'retInfoToken');
        }

        $this->echoRet(0, '获取Token成功', json_encode($oRet), 'retInfoToken');
    }
}

RUN_CONTROLLER("userController");
