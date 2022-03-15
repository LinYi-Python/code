<?php
require_once '/data/sure/SureInit.inc.php';
require_once '../logic/UserLogic.class.php';
require_once '../logic/Web3Logic.class.php';
require_once '../logic/FileLogic.class.php';
require_once '../logic/ProjectLogic.class.php';

use \sure\tools\SureCurl;

class testController extends SureController
{

    // 日志路径
    public $m_sFilePath = '/data/log/coderchain/fileupload/';

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
        $this->mustCoderchainLogin();
        $file = $_FILES['upload-file'];
        $tmpName = $file['tmp_name'];
        $fileName = $file['name'];
        // 临时文件目录
        $uploadDirPath = '/data/dep/tmp/coderchain';
        // 新文件的文件路径
        $newFileName = time() . rand(10000, 99999) . '-' . $fileName;
        $fullFilePath = $uploadDirPath . '/' . $newFileName;

        if (!isset($file)) {
            SURE_LOG(__FILE__, __LINE__, LP_ERROR, "file: => " . $tmpName . ' 没有指定文件');
            echo json_encode(array('retCode' => 100, 'retMsg' => '没有指定的文件'));
            return;
        }

        // 检测文件是否是通过HTTP POST方式上传上来
        if (!is_uploaded_file($tmpName)) {
            SURE_LOG(__FILE__, __LINE__, LP_ERROR, "file: => " . $tmpName . ' 文件不是通过HTTP POST方式上传上来的，非法');
        }

        // 目录不存在则创建
        if (!file_exists($uploadDirPath)) {
            mkdir($uploadDirPath, 0777, true);
            chmod($uploadDirPath, 0777);
            SURE_LOG(__FILE__, __LINE__, LP_ERROR, "Dir: => " . $uploadDirPath . ' 不存在，创建目录 ');
        }

        // 移动文件
        if (move_uploaded_file($tmpName, $fullFilePath)) {
            $arrStr = array('sProjectName', 'sPath');
            $this->mustParamsForString($arrStr);

            $sAddress = UserLogic::getUserAddress();
            // $sProjectName = $this->m_arrParams['sProjectName'];
            $sProjectName = $_POST['sProjectName'];
            $sPath = $this->m_arrParams['sPath'];

            if (!ProjectLogic::isValidFileName($sProjectName)) {
                echo json_encode(array('retCode' => 100, 'retMsg' => '项目名不合法'));
                return;
            }

            // 判断资源的后缀
            $ext = FileLogic::getFileExtension($fileName);

            if (FileLogic::isResourceType($ext)) {
                // 文件后缀) {
                // 是图片文件
                // ($sAddress, $sProjectName, $sFilePath, $sResPath, $sResName)

                if ($sPath == ' ') {
                    $sPath = '';
                } else {
                    $sPath = $sPath;
                }

                $oRet = FileLogic::saveImage($sAddress, $sProjectName, $sPath, $fullFilePath, $fileName);
            } else {

                // 处理文件路径
                if ($sPath == ' ') {
                    $sPath = $fileName;
                } else {
                    $sPath = $sPath . '/' . $fileName;
                }

                $sFileContent = file_get_contents($fullFilePath);
                $sFileContent = FileLogic::convertStringEncoding($sFileContent);

                SURE_LOG(__FILE__, __LINE__, LP_INFO, "上传文件的内容: => ---start---" . $sFileContent . '---end---');

                # 上传文件内容
                $oRet = FileLogic::saveFile($sAddress, $sProjectName, $sPath, $sFileContent);
                SURE_LOG(__FILE__, __LINE__, LP_INFO, "sFileContent: ${sFileContent} --- sPath: {$sPath}");
            }

            SURE_LOG(__FILE__, __LINE__, LP_INFO, "上传文件的返回结果oRet: {$oRet}");
            $oRet = json_decode($oRet, true);

            if ($oRet['iCode'] != 0) {
                echo json_encode(array('retCode' => 100, 'retMsg' => '上传文件失败，请重新再试'));
            } else {
                echo json_encode(array('retCode' => 0, 'retMsg' => '上传文件成功'));
            }

            // 删除临时文件
            unlink($fullFilePath);
        } else {
            SURE_LOG(__FILE__, __LINE__, LP_ERROR, "move_uploaded_file(tmpName, fullFilePath)失败，参数：move_uploaded_file({$tmpName}, {$fullFilePath})");
            echo json_encode(array('retCode' => 100, 'retMsg' => '上传文件失败，请重新再试'));
            unlink($fullFilePath);
        }
    }

    /**
     * 获取文件内容
     * @return [type] [description]
     */
    public function getContentAction()
    {
        $sAddress = UserLogic::getUserAddress();

        // 向Node发请求创建项目
        $arrStr = array('sHash');
        $this->mustParamsForString($arrStr);

        $sHash = $this->m_arrParams['sHash'];

        $oRet = FileLogic::getFileContent($sHash);

        SURE_LOG(__FILE__, __LINE__, LP_ERROR, "获取文件内容: sHash => " . $sHash . ' 拉取列表结果：' . json_encode($oRet));
        // 编码文件内容


        // 由于前端JSONPEX 使用eval函数，该函数无法解析'\r','\n' 将其转换掉
        // // 用户上传zip文件的时候，文件的处理不是php向Node后台请求的，所以说为了兼容zip文件下的内容，需要手动编码，然后前端才能正确解码
        // $oRet = str_replace("\r", "%0D", $oRet);
        // $oRet = str_replace("\n", "%0A", $oRet);
        $oRet = rawurlencode($oRet);
        $oData = array();
        $oData['sMsg'] = $oRet;

        SURE_LOG(__FILE__, __LINE__, LP_ERROR, "---获取文件内容: oData => " . json_encode($oData));
        $this->echoRet(0, '获取内容成功', json_encode($oData));
    }


    /**
     * 修改文件内容
     * @return [type] [description]
     */
    public function updateContentAction()
    {
        $this->mustCoderchainLogin();
        // 获取传递上来的文字名称
        $arrStr = array('sProjectName', 'sPath', 'sData');
        $this->mustParamsForString($arrStr);

        $sAddress = UserLogic::getUserAddress();
        // $sProjectName = $this->m_arrParams['sProjectName'];
        $sProjectName = $_POST['sProjectName'];
        $sPath = $this->m_arrParams['sPath'];
        // $sData = $this->m_arrParams['sData'];

        if (!ProjectLogic::isValidFileName($sProjectName)) {
            echo json_encode(array('retCode' => 100, 'retMsg' => '项目名不合法'));
            return;
        }

        // 此sData千万不要操作DB，有注入攻击的风险
        // 此sData千万不要操作DB，有注入攻击的风险
        // 此sData千万不要操作DB，有注入攻击的风险
        $sData = $_POST["sData"];

        if ($sData == ' ') {
            $sData = '';
        }

        $oRet = fileLogic::saveFile($sAddress, $sProjectName, $sPath, $sData);
        $oRet = json_decode($oRet, true);

        if ($oRet['iCode'] != 0) {
            echo json_encode(array('retCode' => 100, 'retMsg' => '修改文件内容失败'));
        }

        echo json_encode(array('retCode' => 0, 'retMsg' => '修改文件内容成功'));
    }


    /**
     * 删除文件
     * @return [type] [description]
     */
    public function deleteAction()
    {
        $this->mustCoderchainLogin();

        // 获取传递上来的文字名称
        $arrStr = array('sProjectName', 'sPath');
        $this->mustParamsForString($arrStr);

        $sAddress = UserLogic::getUserAddress();
        // $sProjectName = $this->m_arrParams['sProjectName'];
        $sProjectName = $_POST['sProjectName'];
        $sPath = $this->m_arrParams['sPath'];

        if (!ProjectLogic::isValidFileName($sProjectName)) {
            $this->echoRet(100, '项目名不合法');
            return;
        }

        # 上传文件内容
        $oRet = fileLogic::deleteFile($sAddress, $sProjectName, $sPath);
        $oRet = json_decode($oRet, true);

        if ($oRet['iCode'] != 0) {
            $this->echoRet(100, '删除文件失败');
        }

        $this->echoRet(0, '删除文件成功');
    }

}

RUN_CONTROLLER("testController");
