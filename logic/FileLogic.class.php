<?php

use \sure\tools\SureCurl;

require_once 'Web3Logic.class.php';

class FileLogic {

	static $sHttp = "";


	// 根据哈希获取文件内容
	static public function getFileContent($sHash) {
		// $sUrl = Web3Logic::getWeb3Http()."/ipfs/mkdir/" . $sHash;
		//
		$sUrl = "http://121.201.35.141:18080/ipfs/{$sHash}";
		SURE_LOG(__FILE__, __LINE__, LP_INFO, "----获取文件内容url： sUrl: " . $sUrl);
		$sData = file_get_contents($sUrl);

		return $sData;
	}


	// 保存文件内容
	static public function saveFile($sAddress, $sProjectName, $sFilePath, $sData) {
        // $sData在这里处理一次
        // 不需要转义了
        // $sData = rawurlencode($sData);
        // 对项目名进行编码
        $sUrl = Web3Logic::getWeb3Http()."/project/file/" . $sAddress;
        $arrParams = array('sProjectName' => $sProjectName, 'sFilePath' => $sFilePath, 'sData' => $sData);
        $oRet = SureCurl::post($sUrl, $arrParams);
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "保存文件成功： sUrl: {$sUrl}, sAddress: {$sUrl}, sProjectName: {$sProjectName}, sFilePath: {$sFilePath}, sData: {$sData}");

        return $oRet;
	}

	// 删除文件内容
	static public function deleteFile($sAddress, $sProjectName, $sPath) {
        $sUrl = Web3Logic::getWeb3Http()."/project/remove/" . $sAddress;
        $arrParams = array('sProjectName' => $sProjectName, 'sPath' => $sPath);
        $oRet = SureCurl::post($sUrl, $arrParams);
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "删除文件成功： sUrl: {$sUrl}, sAddress: {$sUrl}, sProjectName: {$sProjectName}, sPath: {$sPath}");

        return $oRet;
	}

    // 解压文件
    //Url_zip解压包路径，zip_name加压后的文件名，To_zip解压路径
    static public function unzip($Url_zip,$zip_name,$To_zip){
        // unzip
        $path =$Url_zip;//文件路径
        $topath=$To_zip;//存储路径
        $dir =$topath.$zip_name;//解压文件名
        if(!is_dir($dir)){
           mkdir($dir);
             }
     if(file_exists($path)) {//文件存在

            $resource = zip_open($path);//进入压缩包
            if($resource==false){
                    return false;
                    // echo ("zip打开失败");
                }
            else{
            while($zip = zip_read($resource)) {//遍历项目

                  zip_entry_open($resource, $zip);
                  //处理项目名
                  $file_content = $dir.'/'.zip_entry_name($zip);
                  // +++
                  // $file_content = iconv('UTF-8', 'GB2312', $file_content);
                  //
                  $file_contentw = substr($file_content, strrpos($file_content, '/') );
                  //如果是文件夹，创建文件夹
                  if($file_contentw=='/') {
                    mkdir($file_content,0777,true);
                 }
                 else {//如果不是文件夹
                  $save_path = $file_content;//存储目录
                  $file_size = zip_entry_filesize($zip);//返回文件中尺寸。
                  $file = zip_entry_read($zip, $file_size);//读取文件中内容。

                  // ...
                  // 这里处理一下文件编码
                  $file = self::convertStringEncoding($file);
                  // ...
                  //
                  // 转码
                  // $save_path = iconv('UTF-8', 'GB2312', $save_path);
                  $num=file_put_contents($save_path, $file);//写入内容
                  zip_entry_close($zip);//关闭的 zip 项目资源。
                     }
               }//while
            }
            zip_close($resource);
            return true;
        }
        else{// if(file_exists($path))
            // echo ("zip文件不存在");
            return false;
        }
    }

    // 上传zip, 这个功能暂时不开放
    static public function batchUploadFile($sAddress, $sProjectName, $sPath, $sFilePath) {
        $sUrl = Web3Logic::getWeb3Http()."/xxx" . $sAddress;
        $arrParams = array('sProjectName' => $sProjectName, 'sPath' => $sPath, '$sFilePath' => $sFilePath);
        $oRet = SureCurl::post($sUrl, $arrParams);
        SURE_LOG(__FILE__, __LINE__, LP_INFO, "上传文件夹成功： sUrl: {$sUrl}, sAddress: {$sUrl}, sProjectName: {$sProjectName}, sPath: {$sPath}, sFilePath: {$sFilePath}");

        return $oRet;
    }


    // 转换内容的编码格式
    static public function convertStringEncoding($sFileContent, $newEncoding = 'utf-8') {
      // 这里对内容进行编码转换，转换成UTF-8
      $encoding = mb_detect_encoding($sFileContent, array('GB2312','GBK','UTF-16','UCS-2','UTF-8','BIG5','ASCII', 'CP936'));

      SURE_LOG(__FILE__, __LINE__, LP_INFO, "文件的编码是：{$encoding}, 内容是：=> " . $sFileContent . '---end---');
      if ($encoding != 'UTF-8') {
          // 转换编码
          $sFileContent = mb_convert_encoding($sFileContent, $newEncoding, $encoding);
          SURE_LOG(__FILE__, __LINE__, LP_INFO, "文件的编码转换后的内容是：{$encoding}, 内容是：=> " . $sFileContent . '---end---');
      }

      return $sFileContent;
    }

    // 保存图片文件
    static public function saveImage($sAddress, $sProjectName, $sFilePath, $sResPath, $sResName) {
          // $sData在这里处理一次
          // 不需要转义了
          // $sData = rawurlencode($sData);
          $sUrl = Web3Logic::getWeb3Http()."/project/res/" . $sAddress;
          $arrParams = array('sProjectName' => $sProjectName, 'sFilePath' => $sFilePath, 'sResPath' => $sResPath, 'sResName' => $sResName);
          $oRet = SureCurl::post($sUrl, $arrParams);
          SURE_LOG(__FILE__, __LINE__, LP_INFO, "保存图片文件成功： sUrl: {$sUrl}, sAddress: {$sUrl}, sProjectName: {$sProjectName}, sFilePath: {$sFilePath}, sResPath: {$sResPath}, sResName: {$sResName}");
          SURE_LOG(__FILE__, __LINE__, LP_INFO, "上传图片传递的参数： arrParams: " . json_encode($arrParams));
          return $oRet;
    }


    // 获取文件后缀
    static public function getFileExtension($filename) {
      return pathinfo($filename, PATHINFO_EXTENSION);
    }

    static public function convertDirFilesEncoding($path, $newEncoding = 'utf-8') {
        if (file_exists($path)) {
            if (is_dir($path)) {
                foreach (glob("$path/*") as $key => $value) {
                    self::convertDirFilesEncoding($value);
                }
                return;
            }

          // 这里处理各种文件类型，如果是图片
          $ext = self::getFileExtension($path);
          if (self::isResourceType($ext)) {
            return;
            // file
          }
          $sFileContent = file_get_contents($path);

          // 这里对内容进行编码转换，转换成UTF-8
          $encoding = mb_detect_encoding($sFileContent, array('GB2312','GBK','UTF-16','UCS-2','UTF-8','BIG5','ASCII', 'CP936'));
          SURE_LOG(__FILE__, __LINE__, LP_INFO, "转换项目的编码是：{$encoding}");
          SURE_LOG(__FILE__, __LINE__, LP_INFO, "转换项目的内容是：{$sFileContent}");

          if ($encoding != 'UTF-8') {
              // 转换编码
              $sFileContent = mb_convert_encoding($sFileContent, $newEncoding, $encoding);
              file_put_contents($path, $sFileContent);
          }

        } else {
            //
          SURE_LOG(__FILE__, __LINE__, LP_INFO, "转换目录编码，目录不存在：${path}");
        }
    }

    static public function isImage($ext) {
      $IMG_RESOURCE = ['jpg', 'jpeg', 'gif', 'png', 'ico'];
      return in_array(strtolower($ext), $IMG_RESOURCE);
    }

    static public function isResourceType($ext) {
      $IMG_RESOURCE = ['jpg', 'jpeg', 'gif', 'png', 'ico', 'pdf', 'doc', 'docx', 'woff'];
      return in_array(strtolower($ext), $IMG_RESOURCE);
    }
}

