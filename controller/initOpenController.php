<?php
require_once '/data/sure/SureInit.inc.php';
require_once '../logic/UserLogic.class.php';
require_once '../logic/ProjectLogic.class.php';
require_once '../logic/FileLogic.class.php';

class testController extends SureController{

    // 日志路径
    public $m_sFilePath = '/data/log/coderchain/project/';

    // public $m_iType = WEB;
    public $m_iType = WEB_JSON;

    public function initAction() {

        $this->m_sDomain = "coderchain.cn";
        global $g_sFileName;
        $g_sFileName = __FILE__;

    }

    public function indexAction() {
      // 获取 get 的 session_id

       $arrStr = array('session_id', 'username');
       $this->mustParamsForString($arrStr);

       //
       session_abort();

       session_id($this->m_arrParams['session_id']);
       session_start();
       $this->echoRet(0, '');
    }

    /**
     * 创建项目
     * @return [type] [description]
     */

}

RUN_CONTROLLER("testController");
