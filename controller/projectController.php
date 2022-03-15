<?php
require_once '/data/sure/SureInit.inc.php';
require_once '../logic/UserLogic.class.php';
require_once '../logic/ProjectLogic.class.php';
require_once '../logic/FileLogic.class.php';

class testController extends SureController
{

    // 日志路径
    public $m_sFilePath = '/data/log/coderchain/project/';

    // public $m_iType = WEB;
    public $m_iType = WEB_JSON;

    public function initAction()
    {

        $this->m_sDomain = "coderchain.cn";
        global $g_sFileName;
        $g_sFileName = __FILE__;
    }

    public function indexAction()
    { }

    /**
     * 创建项目
     * @return [type] [description]
     */
    public function createAction()
    {
        $this->mustCoderchainLogin();
        $arrStr = array('sProjectName');
        $this->mustParamsForString($arrStr);
        $sAddress = UserLogic::getUserAddress();
        // $sProjectName = $this->m_arrParams['sProjectName'];
        $sProjectName = $_POST['sProjectName'];
        $sProjectName = urlencode($sProjectName);
        $bMoveFileError = false;

        if (!ProjectLogic::isValidFileName($sProjectName)) {
            echo json_encode(array('retCode' => 100, 'retMsg' => '项目名不合法'));
            return;
        }

        // 这里要判断一下用户有没有传递文件上来
        if (isset($_FILES['upload-file'])) {
            $file = $_FILES['upload-file'];
            $tmpName =  $file['tmp_name'];
            $fileName = $file['name'];
            // 临时文件目录
            $uploadDirPath = '/data/dep/tmp/coderchain/' . $sAddress;
            // 新文件的文件路径
            $newFileName = time() . rand(10000, 99999) . '-' . $fileName;
            $fullFilePath = $uploadDirPath . '/' . $newFileName;

            SURE_LOG(__FILE__, __LINE__, LP_INFO, "===临时文件路径: => " . $tmpName);

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
                // 获取文件后缀
                $ext = FileLogic::getFileExtension($fileName);

                if ($ext == 'zip' || $ext == 'rar') {
                    // 解压zip
                    $newFileName = $sProjectName;
                    SURE_LOG(__FILE__, __LINE__, LP_INFO, "用户{$sAddress}创建文件夹，解压的具体参数：fullFilePath: ${fullFilePath},newFileName: {$newFileName},  uploadDirPath: {$uploadDirPath}");

                    // $r = FileLogic::unzip($fullFilePath, $newFileName, $uploadDirPath .'/');


                    // if (!$r) {
                    //     // 解压文件夹失败
                    //     SURE_LOG(__FILE__, __LINE__, LP_INFO, "用户{$sAddress}创建文件夹，解压文件夹失败${fullFilePath}");
                    //     $fullFilePath = '';
                    // }


                    // exec('locale charmap');
                    // 设置编码，很重要，服务器环境的编码默认是UTF-8，而php代码执行的环境不一定是UTF-8
                    $locale = 'zh_CN.UTF-8';
                    setlocale(LC_ALL, $locale);
                    putenv('LC_ALL=' . $locale);
                    $res = exec($sCmd);
                    SURE_LOG(__FILE__, __LINE__, LP_INFO, "[解压结果] - system 路径：{$res}");


                    // deste
                    $destFilePath = $uploadDirPath . '/' .  $newFileName;


                    if ($ext == 'zip') {
                        $sCmd = "unzip -O CP936 -d {$destFilePath} {$fullFilePath}";
                    } else if ($ext == 'rar') {
                        // rar解压必须要先创建目录，不然会解压失败
                        if (!file_exists($destFilePath)) {
                            mkdir($destFilePath, 0777, true);
                            chmod($destFilePath, 0777);
                            SURE_LOG(__FILE__, __LINE__, LP_ERROR, "先创建目录Dir: => " . $destFilePath . ' 不存在，创建目录 ');
                        }

                        $sCmd = "unrar x {$fullFilePath} {$destFilePath}";
                    }


                    $res = exec($sCmd);

                    SURE_LOG(__FILE__, __LINE__, LP_INFO, "解压命令：cmd --> {$sCmd}");

                    // 这里要对用户的这个文件夹进行遍历处理，都转化成UTF-8
                    // destFilePath
                    FileLogic::convertDirFilesEncoding($destFilePath);

                    // 删除这个压缩包
                    // unlink($fullFilePath);

                    // 得到路径
                    $fullFilePath = $uploadDirPath . '/' .  $newFileName;
                    // 删除zip
                    SURE_LOG(__FILE__, __LINE__, LP_INFO, "用户{$sAddress}创建文件夹，临时文件路径：${fullFilePath}");
                } else {
                    // 普通文件

                    // 先创建文件夹
                    $oRet = ProjectLogic::createProject($sAddress, $sProjectName);

                    SURE_LOG(__FILE__, __LINE__, LP_INFO, "创建项目时上传文件=> 用户{$sAddress}创建项目时。创建项目结果：" . $oRet);

                    $oRet = json_decode($oRet, true);

                    if ($oRet['iCode'] == 200) {
                        echo json_encode(array('retCode' => 100, 'retMsg' => '项目名已经存在，请填入其他项目名'));
                        return;
                    }

                    if ($oRet['iCode'] != 0) {
                        echo json_encode(array('retCode' => 100, 'retMsg' => '创建项目失败'));
                        return;
                    }


                    // 这里判断是不是图片
                    if (FileLogic::isResourceType($ext)) {
                        $sPath = ''; // 根路径
                        FileLogic::saveImage($sAddress, $sProjectName, $sPath, $fullFilePath, $fileName);
                    } else {
                        $sPath = $fileName;
                        $sFileContent = file_get_contents($fullFilePath);

                        // 这里对文件进行编码转换
                        $sFileContent = FileLogic::convertStringEncoding($sFileContent);

                        // 这里的项目名必须要encode一下
                        $oFileUpLoadRet = FileLogic::saveFile($sAddress, $sProjectName, $sPath, $sFileContent);
                    }

                    SURE_LOG(__FILE__, __LINE__, LP_INFO, "用户{$sAddress}创建项目时。创建文件结果：" . $sFileContent);
                    SURE_LOG(__FILE__, __LINE__, LP_INFO, "用户{$sAddress}创建项目时。上传的是单个文件，临时文件内容：${sFileContent}");


                    // 删除临时文件
                    // unlink($fullFilePath);
                    echo json_encode(array('retCode' => 0, 'retMsg' => '创建项目成功'));
                    return;
                }
            } else {
                SURE_LOG(__FILE__, __LINE__, LP_ERROR, "用户{$sAddress}项目创建成功，但是没有成功导入文件，临时路径：{$tmpName}, 终点路径：{$fullFilePath}");
                $fullFilePath = '';
                $bMoveFileError = true;
            }
        } else {
            SURE_LOG(__FILE__, __LINE__, LP_INFO, "用户{$sAddress}创建项目，但是没有选择文件");
            $fullFilePath = '';
        }

        // 向Node发请求创建项目
        $oRet = ProjectLogic::createProject($sAddress, $sProjectName, $fullFilePath);

        // 删除zip
        if ($fullFilePath != '') {
            if (strlen($sAddress) == 42) {
                // $sCmd = "rm -rf /data/dep/tmp/coderchain/{$sAddress}";
                SURE_LOG(__FILE__, __LINE__, LP_INFO, "cmd --> {$sCmd}");
                $res = exec($sCmd);
                SURE_LOG(__FILE__, __LINE__, LP_INFO, "[假删除] - system 删除文件夹的结果：{$res}");
            }
        }

        $oRet = json_decode($oRet, true);
        // 如果上传文件结果存在
        // $oFileUpLoadRet = json_decode($oFileUpLoadRet, true);

        SURE_LOG(__FILE__, __LINE__, LP_ERROR, "创建项目的结果: $sAddress => " . $sAddress . ' 结果：' . json_encode($oRet));

        if ($oRet['iCode'] == 200) {
            echo json_encode(array('retCode' => 100, 'retMsg' => '项目名已经存在，请填入其他项目名'));
            return;
        }

        // 登陆逻辑
        if ($oRet['iCode'] != 0) {
            echo json_encode(array('retCode' => 100, 'retMsg' => '上传项目失败', 'oRet' => json_encode($oRet)));
            return;
        }

        if ($bMoveFileError) {
            $sMsg = '项目创建成功，但是没有成功导入文件';
        } else {
            $sMsg = '创建项目成功';
        }
        echo json_encode(array('retCode' => 0, 'retMsg' => $sMsg, 'oRet' => json_encode($oRet)));
    }

    /**
     * 拉取项目工程下面的所有文件
     * @return [type] [description]
     */
    public function getDetailAction()
    {
        //        $this->mustCoderchainLogin();
        // 参数校验
        $arrStr = array('sProjectName', 'sPath', 'sUser');
        $this->mustParamsForString($arrStr);

        if ($this->m_arrParams['sPath'] == ' ') {
            $this->m_arrParams['sPath'] = '';
        }

        // $sAddress = UserLogic::getUserAddress();
        $sAddress = $this->m_arrParams['sUser'];

        $sProjectName = $this->m_arrParams['sProjectName'];
        SURE_LOG(__FILE__, __LINE__, LP_ERROR, "查看不同参数获取的值：m_arrparams -> projectName: {$sProjectName}");
        $sProjectName = $_POST['sProjectName'];
        SURE_LOG(__FILE__, __LINE__, LP_ERROR, "查看不同参数获取的值：post -> projectName: " . $_POST['sProjectName']);
        $sPath = $this->m_arrParams['sPath'];

        // 向Node发请求创建项目
        SURE_LOG(__FILE__, __LINE__, LP_ERROR, "----准备logic---: sAddress => " . $sAddress . ' projectName => ' . $sProjectName);

        if (!ProjectLogic::isValidFileName($sProjectName)) {
            $this->echoRet(100, '项目名不合法');
            return;
        }

        $oRet = ProjectLogic::getProjectDetail($sAddress, $sProjectName, $sPath);

        $oRet = json_decode($oRet, true);

        SURE_LOG(__FILE__, __LINE__, LP_ERROR, "拉取项目详情: sAddress => " . $sAddress . ' projectName => ' . $sProjectName . '拉取列表结果：' . json_encode($oRet));

        // 登陆逻辑
        if ($oRet['iCode'] != 0) {
            $this->echoRet(100, '拉取项目失败');
        }

        $this->echoRet(0, '拉取项目成功', json_encode($oRet['oRet']));
    }


    /**
     * 获取所有的项目列表
     * @return [type] [description]
     */
    public function getListAction()
    {
        $this->mustCoderchainLogin();
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "getListAction -> 获取用户登录的session: " . $_SESSION['user_id']);
        $sAddress = UserLogic::getUserAddress();

        // 向Node发请求创建项目
        $oRet = ProjectLogic::getProjectList($sAddress);
        $oRet = json_decode($oRet, true);

        SURE_LOG(__FILE__, __LINE__, LP_ERROR, "拉取项目列表: sAddress => " . $sAddress . ' 拉取列表结果：' . json_encode($oRet));

        if ($oRet['iCode'] != 0) {
            $this->echoRet(100, '拉取项目失败');
        }
        $oRet = array('user' => $sAddress, 'projectList' => $oRet['oRet']);
        $this->echoRet(0, '拉取项目成功', json_encode($oRet));
    }

    /**
     * 创建目录
     * @return [type] [description]
     */
    public function createDirAction()
    {
        $this->mustCoderchainLogin();
        $arrStr = array('sProjectName', 'sPath');
        $this->mustParamsForString($arrStr);

        $sAddress = UserLogic::getUserAddress();
        $sProjectName = $this->m_arrParams['sProjectName'];
        $sProjectName = $_POST['sProjectName'];
        $sPath = $this->m_arrParams['sPath'];

        if (!ProjectLogic::isValidFileName($sProjectName)) {
            $this->echoRet(100, '目录名不合法');
            return;
        }

        // 向Node发请求创建项目
        $oRet = ProjectLogic::createDir($sAddress, $sProjectName, $sPath);

        SURE_LOG(__FILE__, __LINE__, LP_ERROR, "创建文件夹：sAddress: $address | sProjectName: $sProjectName | sPath: $sPath" . '' . ' 结果：' . json_encode($oRet));
        // 登陆逻辑
        if (!$oRet['iCode']) {
            $this->echoRet(100, '创建文件夹失败');
        }

        $this->echoRet(0, '创建文件夹成功');
    }

    /**
     * 删除项目
     * @return [type] [description]
     */
    public function deleteAction()
    {
        $this->mustCoderchainLogin();
        $arrStr = array('sProjectName');
        $this->mustParamsForString($arrStr);

        $sAddress = UserLogic::getUserAddress();
        // $sProjectName = $this->m_arrParams['sProjectName'];
        $sProjectName = $_POST['sProjectName'];

        if (!ProjectLogic::isValidFileName($sProjectName)) {
            $this->echoRet(100, '项目名不合法');
            return;
        }

        # 上传文件内容
        $oRet = ProjectLogic::deleteProject($sAddress, $sProjectName);

        if (!$oRet['iCode']) {
            $this->echoRet(100, '删除文件失败');
        }

        $this->echoRet(0, '删除文件成功');
    }

    /**
     * 对项目进行投票
     * @return [type] [description]
     */
    public function voteAction()
    {
        $this->mustCoderchainLogin();
        $arrStr = array('sProjectName', 'sProjectOwner', 'iSupportCount', 'sFromUser');
        /*
            sProjectName: sProjectName,
            sProjectOwner: sProjectOwner,
            iSupportCount: iSupportCount,
            sFromUser: sFromUser,
         */
        $this->mustParamsForString($arrStr);

        // 获取用户的密钥
        $sAddress = UserLogic::getUserAddress();
        $sProjectName = $_POST['sProjectName'];
        $sProjectOwner = $this->m_arrParams['sProjectOwner'];
        $iSupportCount = $this->m_arrParams['iSupportCount'];
        $sFromUser = $this->m_arrParams['sFromUser'];
        //        $sProjectName =rawurldecode($sProjectName);
        if (!ProjectLogic::isValidFileName($sProjectName)) {
            SURE_LOG(__FILE__, __LINE__, LP_ERROR, "投票失败的项目名：{$sProjectName}");
            $this->echoRet(100, '项目名不合法');
            return;
        }

        if ($sAddress != $sFromUser) {
            SURE_LOG(__FILE__, __LINE__, LP_ERROR, "投票情况出现了伪造：sAddress: {$sAddress} 不等于 sFromUser: {$sFromUser}, sProjectName: {$sProjectName}, sProjectOwner: {$sProjectOwner}, iSupportCount: {$iSupportCount}");
            $this->echoRet(100, '投票失败');
        }

        // 获取用户的密钥
        $sPK = UserLogic::getUserPK($sAddress);

        if (!$sPK) {
            $this->echoRet(100, '用户的密钥找不到');
        }

        // 投票
        $oRet = projectLogic::vote($sProjectName, $sProjectOwner, $iSupportCount, $sFromUser, $sPK);

        $oRet = json_decode($oRet, true);

        if ($oRet['iCode'] == 100) {
            $this->echoRet(100, '投票失败，你的Token不够支付交易费');
        }

        if ($oRet['iCode'] != 0) {
            $this->echoRet(100, '投票失败，请等会再尝试');
        }

        $this->echoRet(0, '投票成功');
    }

    /**
     * 
     * 获取投票情况
     * @return [type] [description]
     */
    public function getSupportCountAction()
    {
        $this->mustCoderchainLogin();
        $arrStr = array('sProjectName', 'sProjectOwner');
        $this->mustParamsForString($arrStr);

        // $sProjectName = $this->m_arrParams['sProjectName'];
        $sProjectName = $_POST['sProjectName'];
        $sProjectOwner = $this->m_arrParams['sProjectOwner'];

        if (!ProjectLogic::isValidFileName($sProjectName)) {
            $this->echoRet(100, '项目名不合法');
            return;
        }

        # 上传文件内容
        $oRet = projectLogic::getSupportCount($sProjectName, $sProjectOwner);

        $oRet = json_decode($oRet, true);
        if ($oRet['iCode'] != 0) {
            $this->echoRet(100, '获取投票数量失败');
        }


        $arr = array('count' => $oRet['sMsg']);
        $this->echoRet(0, '获取投票数量成功', json_encode($arr));
    }
}

RUN_CONTROLLER("testController");
