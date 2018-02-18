<?php
error_reporting(0);
include './config.php';

function isAdministrator($Chat_Member)
{
    if(isset($Chat_Member))
    {
        if($Chat_Member['result']['status']=="creator" or $Chat_Member['result']['status']=="administrator")
        {
            return true;
        }
    }
    return false;
}

function getLockStatus($CHAT_ID,$DB_CONNECTING)
{
    //return array (chat,sticker,link)
    $result=array(0,0,0,0,0);
    
    //$CHARSET='set NAMES utf8;';
    //$DB_CONNECTING->query($CHARSET);
    
    $db_query="SELECT `chatid`, `chat`, `sticker`, `forward`, `link` ,`gif` , `status`   FROM `lockeditems` WHERE chatid=".$CHAT_ID;
    $res=$DB_CONNECTING->query($db_query);
    $row=$res->fetch_assoc();
    if(isset($row['chatid']) or $row['chatid']!="")
    {
       // $result=array();
        $result[0]=$row['chat'];
        $result[1]=$row['sticker'];
        $result[2]=$row['link'];
        $result[3]=$row['gif'];
        $result[4]=$row['status'];
        
    }
    return $result;
    
    
}

function insertToInviteList($CHAT_ID,$USER_ID,$invitedCnt,$ChallengeCnt,$DB_CONNECTING)
{
    
    $QUERY="INSERT INTO `invitedlist`( `chatid`, `userid`, invitedcnt, challengecnt) VALUES ($CHAT_ID,$USER_ID,$invitedCnt,$ChallengeCnt)";
    $DB_CONNECTING->query($QUERY);
    
    
}

function UpdateInvitedCnt($CHAT_ID,$USER_ID,$invitedCnt,$ChallengeCnt,$DB_CONNECTING)
{
    
    $QUERY="UPDATE `invitedlist` SET invitedcnt=$invitedCnt , challengecnt=$ChallengeCnt WHERE chatid=".$CHAT_ID." and userid=".$USER_ID;
    $DB_CONNECTING->query($QUERY);
    
    
}

function getForceCount($CHAT_ID,$DB_CONNECTING)
{
    $forcecnt=0;
    $QUERY="SELECT `chatid`,forcecnt FROM `lockeditems` WHERE chatid=".$CHAT_ID;
                $res=$DB_CONNECTING->query($QUERY);
                $r=$res->fetch_assoc();
                
                if(isset($r['chatid']) or $r['chatid']!="")
                {
                    //$s="tekrari";
                    $forcecnt=$r['forcecnt'];
                }
                else
                {
                    //$s="0";
                    $forcecnt=0;
                }
    return $forcecnt;
}

function getInvitedCount($CHAT_ID,$USER_ID,$DB_CONNECTING)
{
    $invitedcnt=0;
    $QUERY="SELECT `chatid`,userid,invitedcnt FROM `invitedlist` WHERE chatid=".$CHAT_ID." and userid=".$USER_ID;
                $res=$DB_CONNECTING->query($QUERY);
                $r=$res->fetch_assoc();
                
                if(isset($r['invitedcnt']) or $r['invitedcnt']!="")
                {
                    //$s="tekrari";
                    $invitedcnt=$r['invitedcnt'];
                }
                else
                {
                    //$s="0";
                    $invitedcnt=-1;
                }
    return $invitedcnt;
}
function getWelcomeStatus($CHAT_ID,$DB_CONNECTING)
{
    //return array (chat,sticker,link)
    $result=true;
    
    //$CHARSET='set NAMES utf8;';
    //$DB_CONNECTING->query($CHARSET);
    
    $db_query="SELECT `WelcomeMsg` FROM `lockeditems` WHERE chatid=".$CHAT_ID;
    $res=$DB_CONNECTING->query($db_query);
    $row=$res->fetch_assoc();
    if(isset($row['WelcomeMsg']) or $row['WelcomeMsg']!="")
    {
       // $result=array();
        $result=$row['WelcomeMsg'];
    }
    return $result;
    
    
}
function insertUsers($USER_ID,$Username,$first_name,$last_name,$DB_CONNECTING)
{
     $QUERY="INSERT INTO `tblUsers`(`userid`, `username`, `fname`, `lname`) VALUES ($USER_ID,'$Username','$first_name','$last_name')";
    $DB_CONNECTING->query($QUERY);
    
}

function getfirstLastName($USER_ID,$DB_CONNECTING)
{
    $result="";
    
   $db_query="SELECT `userid`, `username`, `fname`, `lname` FROM `tblusers` WHERE userid=".$USER_ID;
    $res=$DB_CONNECTING->query($db_query);
    $row=$res->fetch_assoc();
    if(isset($row['userid']) or $row['userid']!="")
    {
       // $result=array();
        $result=$row['fname'].' '.$row['lname'];
    }
    
    return $result;
}


function endAddChallenge($CHAT_ID,$DB_CONNECTING)
{
    $k=0;
    //send Amaar
    $array=['نفر اول','نفر دوم','نفر سوم','نفر چهارم','نفر پنجم'];
    $Amaar='آمار چالش ادد'.chr(10);
    $QUERY="SELECT `chatid` , `userid` , `challengecnt` FROM `invitedlist` WHERE challengecnt>0 and chatid=".$CHAT_ID." order by challengecnt DESC limit 5";
    $res=$DB_CONNECTING->query($QUERY);
    while($row=$res->fetch_assoc())
    {
        $userid=$row['userid'];
        $name=getfirstLastName($userid,$DB_CONNECTING);
        $Amaar=$Amaar.$array[$k].':'.'کاربر'.' '.$name.' با تعداد '.$row['challengecnt'].chr(10);
        $k++;
    }
    if($k>0)
    {
        $Amaar=urlencode($Amaar);
        sendMessage($CHAT_ID,$Amaar);
    }
    
    //Challengecnt to be 0
    $QUERY="UPDATE `invitedlist` SET challengecnt=0 WHERE challengecnt>0 and chatid=".$CHAT_ID;
    $DB_CONNECTING->query($QUERY);
}

function getChallengeFlag($CHAT_ID,$DB_CONNECTING)
{
    //return array (chat,sticker,link)
    $result=false;
    
    //$CHARSET='set NAMES utf8;';
    //$DB_CONNECTING->query($CHARSET);
    
    $db_query="SELECT `challengeflag` FROM `lockeditems` WHERE chatid=".$CHAT_ID;
    $res=$DB_CONNECTING->query($db_query);
    $row=$res->fetch_assoc();
    if(isset($row['challengeflag']) or $row['challengeflag']!="")
    {
       // $result=array();
        $result=$row['challengeflag'];
    }
    return $result;
}

function getChallengeCount($CHAT_ID,$USER_ID,$DB_CONNECTING)
{
    $invitedcnt=0;
    $QUERY="SELECT `chatid`,userid,challengecnt FROM `invitedlist` WHERE chatid=".$CHAT_ID." and userid=".$USER_ID;
                $res=$DB_CONNECTING->query($QUERY);
                $r=$res->fetch_assoc();
                
                if(isset($r['challengecnt']) or $r['challengecnt']!="")
                {
                    //$s="tekrari";
                    $invitedcnt=$r['challengecnt'];
                }
                else
                {
                    //$s="0";
                    $invitedcnt=0;
                }
    return $invitedcnt;
}

function insertRobotUsers($USER_ID,$Username,$first_name,$last_name,$mobile,$DB_CONNECTING)
{
     $QUERY="INSERT INTO `tblrobotusers`(`userid`, `username`, `fname`, `lname`, `mobile`) VALUES ($USER_ID,'$Username','$first_name','$last_name','$mobile')";
    $DB_CONNECTING->query($QUERY);
    
}

function getGroupsByUserID($USER_ID,$connect)
{
    $groups=array();
    $sql="SELECT `chatid` FROM `lockeditems`";
    $result=$connect->prepare($sql);
    $result->execute();
    foreach($result as $rows)
    {
        $CHAT_ID=$rows['chatid'];
        $Users= getChatAdmin($CHAT_ID);
        $Users=json_decode($Users);
        $Users=$Users->result;
        foreach ($Users as $user)
        {
            if(isset($user->user->id) and $user->user->id==$USER_ID)
            {
                $groups[]=$CHAT_ID;
            }
        }
    }
    return  $groups;
}

function makeOrderid()
{
    $oid=0;
    $sql="SELECT `orderid` FROM `payment`";
    $result=$connect->prepare($sql);
    $result->execute();
    foreach($result as $rows)
    {
        if($rows['orderid']>$oid)
        {
            $oid=$rows['orderid'];
        }
    }
    return $oid+1;
}