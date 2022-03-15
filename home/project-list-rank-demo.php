<?php
header('Content-Type:application/json');
header('Access-Control-Allow-Origin:*');

$time = time();

$searchArr = array(
  array(
    'name' => 'ng-zorro-antd',
    'id' => '1',
    'createUserId' => '123456',
    'createUserName'=> 'NG-ZORRO',
    'createAt' => $time - 8000,
    'voteNumber' => '5'
  ),
  array(
    'name' => 'ec-do',
    'id' => '1',
    'createUserId' => '123456',
    'createUserName'=> 'chenhuiYj',
    'createAt' => $time - 2000,
    'voteNumber' => '10'
  ),
  array(
    'name' => '33-js-concepts',
    'id' => '1',
    'createUserId' => '123456',
    'createUserName'=> 'stephentian',
    'createAt' => $time - 5000,
    'voteNumber' => '8'
  ),
  array(
    'name' => 'react-zmage',
    'id' => '1',
    'createUserId' => '123456',
    'createUserName'=> 'Caldis',
    'createAt' => $time - 1000,
    'voteNumber' => '60'
  ),
  array(
    'name' => 'react-pullLoad',
    'id' => '1',
    'createUserId' => '123456',
    'createUserName'=> 'react-Ld',
    'createAt' => $time - 10000,
    'voteNumber' => '0'
  ),
);

echo json_encode(array('iCode' => 0, 'data' => $searchArr));
