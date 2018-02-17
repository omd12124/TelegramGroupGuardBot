<?php
include '../function/function.php';
include '../helper/config.php';
include '../helper/utility.php';
include '../helper/jdf.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//$CHARSET='set NAMES utf8;';
//$DB_CONNECTING->query($CHARSET);
//
////insertUsers(850320,"Username","irst_name","last_name",$DB_CONNECTING);
//$k=getChallengeFlag(-1001082087649,$DB_CONNECTING);
//$k=getChallengeCount(1001082087649, 85032630, $DB_CONNECTING);
//echo $k;
//$ts=time() + (30 * 86400); //همین ساعت در پنج روز بعد
//
//echo jdate('Y/n/j H:i:s',$ts,'','','en');

$admins=getChatAdmin(-1001377267460);
$arr=json_decode($admins);
//$admins=$admins['result'];
$arr=$arr->result;
print_r($arr)  ;
echo $arr->user;
echo '<br><br>TEST';
echo $arr[1]['user']['id'];
echo $arr['1']['user']['id'];


