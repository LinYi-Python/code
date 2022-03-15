<?php
header('Content-Type:application/json');
header('Access-Control-Allow-Origin:*');
$searchArr = array(
  array(
    'name' => 'ng-zorro-antd',
    'id' => '1',
    'createUserId' => '123456',
    'createUserName'=> 'NG-ZORRO',
    'createAt' => '2018-10-26',
    'voteNumber' => '5'
  ),
  array(
    'name' => 'ec-do',
    'id' => '1',
    'createUserId' => '123456',
    'createUserName'=> 'chenhuiYj',
    'createAt' => '2018-10-01',
    'voteNumber' => '50'
  ),
  array(
    'name' => '33-js-concepts',
    'id' => '1',
    'createUserId' => '123456',
    'createUserName'=> 'stephentian',
    'createAt' => '2018-10-01',
    'voteNumber' => '3'
  ),
  array(
    'name' => 'react-zmage',
    'id' => '1',
    'createUserId' => '123456',
    'createUserName'=> 'Caldis',
    'createAt' => '2018-10-01',
    'voteNumber' => '3'
  ),
  array(
    'name' => 'react-pullLoad',
    'id' => '1',
    'createUserId' => '123456',
    'createUserName'=> 'react-Ld',
    'createAt' => '2018-10-01',
    'voteNumber' => '3'
  ),
);

if (isset($_POST['key'])) {
  $key = $_POST['key'];
  echo json_encode(array('iCode' => 0, 'data' => $searchArr));
}