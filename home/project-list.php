<?php
header('Content-Type:application/json');
header('Access-Control-Allow-Origin:*');

// 获取项目列表
$projectScript = file_get_contents('http://coderchain.denglu1.cn/js/projectList.js');

// 返回的是script格式，提取数据
$projectListStr = preg_replace("/var arrProject = /", '', $projectScript);

// true代表转换成索引数组
$projectList = json_decode($projectListStr, true);

$result = array();

// 循环拼接项目名称和项目所有者
foreach($projectList as $value) {
  foreach($value['projectList'] as $project) {
    array_push($result, array(
      "name" => rawurldecode($project),
      "user" => $value['name']
    ));
  }
}

// return 结果
echo json_encode(array('iCode' => 0, 'data' => $result));;
