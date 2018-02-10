<?php
include '../helper/config.php';
include '../helper/utility.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$CHARSET='set NAMES utf8;';
$DB_CONNECTING->query($CHARSET);

//insertUsers(850320,"Username","irst_name","last_name",$DB_CONNECTING);
$k=getChallengeFlag(-1001082087649,$DB_CONNECTING);
$k=getChallengeCount(1001082087649, 85032630, $DB_CONNECTING);
echo $k;

