<?php
error_reporting(0);
include '../function/function.php';
include '../helper/utility.php';
include '../helper/config.php';

$Content=file_get_contents('php://input');
$Object=json_decode($Content,true);
$x=0;
$CHARSET='set NAMES utf8;';
$DB_CONNECTING->query($CHARSET);
$QUERY=null;

$isExist=false;

$CHAT_ID=$Object['message']['chat']['id'];
$Message=$Object['message'];
$Message_ID=$Object['message']['message_id'];
$Document=$Message['document'];
$Document_TYPE=$Document['mime_type'];
$TEXT=$Object['message']['text'];
$CHAT_TYPE=$Object['message']['chat']['type'];
$Message_Entities=$Object['message']['entities'];
$Caption_Entities=$Object['message']['caption_entities'];
$USER_ID=$Object['message']['from']['id'];
$FROM_USER=$Object['message']['from'];
$FNAME_inGroup=$FROM_USER['first_name'];
$LNAME_inGroup=$FROM_USER['last_name'];
$isRobot=$FROM_USER['is_bot'];
$USERNAME_inGroup=$FROM_USER['username'];
$CHAT_Member= json_decode(getChatMember($CHAT_ID, $USER_ID),true);
$NOTE=$FNAME_inGroup.' '.$LNAME_inGroup.' عزیز '.chr(10).'لطفا قوانین را رعایت نمایید'.chr(10).'ارسال هر گونه لینک ممنوع است';
$NOTE= urlencode($NOTE);
$Welcome_Message=" سلام ".$FNAME_inGroup.' '.$LNAME_inGroup.' عزیز '.chr(10)."خوش آمدید. امیدواریم اوقات خوشی در این گروه داشته باشید.";
$isProhibited=false;
$Message_TYPE_FA=null;
$Message_Sticker=$Message['sticker'];
$new_chat_members=$Message['new_chat_members']; //array of invitedUsers
if($CHAT_TYPE=="private")
{
    if($TEXT=='/start' or $TEXT=='start')
    {
        $mes='سلام جهت استفاده از قابلیت های رباط در گروه، ربات را در گروه خود ادمین کنید';
        $mes=urlencode($mes);
        sendMessage($CHAT_ID,$mes);
        $mes='دستوزات زبات زا از من بپرسید'.chr(10).'@mali25zah';
        $mes=urlencode($mes);
        sendMessage($CHAT_ID,$mes);
        insertRobotUsers($USER_ID, $USERNAME_inGroup, $FNAME_inGroup, $LNAME_inGroup, '', $DB_CONNECTING);
    }
}
if($CHAT_TYPE=="supergroup" or $CHAT_TYPE=="group")
{
    $status=1;
    $LockStatus=getLockStatus($CHAT_ID,$DB_CONNECTING);
    //چک فعال بودن ربات در گروه
    if(isset($LockStatus[4]) and $LockStatus[4]==0)
    {
        $mes='لطفاجهت شارژ ربات اقدام نمایید'.chr(10).'@mali25zah';
        $mes=urlencode($mes);
        //sendMessage($CHAT_ID,$mes);
        $status=0;
       // return;
    }            
    
    
    
    if($CHAT_ID==-1001082087649)
    {
      //  sendMessage($CHAT_ID,$Content);
    }
    
    
    //Save Users except for robots
    if($FROM_USER['is_bot']==false)
    {
        $QUERY="SELECT `userid`, `username`, `fname`, `lname` FROM `tblUsers` WHERE userid=".$USER_ID;
                $res=$DB_CONNECTING->query($QUERY);
                $row=$res->fetch_assoc();
                
                if(isset($row['userid']) or $row['userid']!="")
                {
                    //$s="tekrari";
                    //nothing
                }
                else
                {
                    insertUsers($USER_ID, $USERNAME_inGroup, $FNAME_inGroup, $LNAME_inGroup, $DB_CONNECTING);
                   
                }
        
    }
    
    
    
    //Force Invite
    $forcecnt= getForceCount($CHAT_ID, $DB_CONNECTING);
    $invited= getInvitedCount($CHAT_ID, $USER_ID, $DB_CONNECTING);
    //sendMessage($CHAT_ID,$forcecnt);
    if($invited<0)
        $invited=0;
    $diff=$forcecnt-$invited;
    if($forcecnt>0 and $diff>0 and !isset($new_chat_members) )
    {
        
        $isProhibited=true;
        $Message_TYPE_FA="ادد اجباری";
        
    }
    
    
    
    
    
    //welcome message for new chat members
    if(isset($new_chat_members) and $new_chat_members!=null)
    {
        $k=0;
        $k=sizeof($new_chat_members)-1;
        //sendMessage($CHAT_ID,$k);
        
        foreach($new_chat_members as $member)
        {
            $member_id=$member['id'];
            if($member['is_bot'])
            {
                
                if(!isAdministrator($CHAT_Member))
                {
                    kickChatMember($CHAT_ID, $USER_ID);
                    $ban_Message="کاربر ".$FNAME_inGroup.' '.$LNAME_inGroup.' '.'به دلیل دعوت ربات به گروه اخراج گردید';
                    sendMessage($CHAT_ID, $ban_Message);
                }
                kickChatMember($CHAT_ID, $member_id);
            }
            else
            {
                $first_name=$member['first_name'];
                $last_name=$member['last_name'];
                $Welcome_Message=" سلام ".$first_name.' '.$last_name.' عزیز '.chr(10)."خوش آمدید. امیدواریم اوقات خوشی در این گروه داشته باشید.";
                $Welcome_Message=urlencode($Welcome_Message);
                $Welcome_flag= getWelcomeStatus($CHAT_ID, $DB_CONNECTING);
                if($Welcome_flag)
                    sendMessage($CHAT_ID,$Welcome_Message);
                $k++;
               
            }
        }
        //for save count of invite
        if(isset($USER_ID) and $USER_ID!=null)
        {
            $invitedcnt= getInvitedCount($CHAT_ID, $USER_ID, $DB_CONNECTING);
            $challenge_count= getChallengeCount($CHAT_ID, $USER_ID, $DB_CONNECTING);
            $cnt=$k;
            $k+=$invitedcnt;
            $Challenge_flag=getChallengeFlag($CHAT_ID,$DB_CONNECTING);
            if($Challenge_flag==false)
            {
                $cnt=0;
            }
            else
            {
                $cnt+=$challenge_count;
            }
            if($invitedcnt<0)
            {
                $k++;
                insertToInviteList($CHAT_ID, $USER_ID, $k, $cnt, $DB_CONNECTING);
            }
            else
            {
                UpdateInvitedCnt($CHAT_ID, $USER_ID, $k,$cnt, $DB_CONNECTING);
            }
        }
    }
    
    
    
    
    //sendMessage($CHAT_ID,$LockStatus[1]);
    //
    //Check Lock for ChatID
    $QUERY="SELECT `chatid`, `chat`, `sticker`, `forward`, `link` FROM `lockeditems` WHERE `chatid`=".$CHAT_ID;
                $res=$DB_CONNECTING->query($QUERY);
                $row=$res->fetch_assoc();
                
                if(isset($row['chatid']) or $row['chatid']!="")
                {
                    //$s="tekrari";
                    $isExist=true;
                }
                else
                {
                    //$s="0";
                    $isExist=false;
                }
                //sendMessage($CHAT_ID, $s);
                    
     //دستور فعالسازی               
    if(($USER_ID==369023864 or $USERNAME_inGroup=="omd6894") and $TEXT=="!فعال")
    {
        if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `status`) VALUES ($CHAT_ID,true)";
                        $DB_CONNECTING->query($QUERY);
                        //sendMessage($CHAT_ID, urlencode('خوش آمدگویی غیر فعال شد.'));
                    }
                    else
                    {

                        $QUERY="UPDATE `lockeditems` SET `status`=true WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        //sendMessage($CHAT_ID, urlencode('خوش آمدگویی غیر فعال شد.'));
                    }
                    sendMessage($CHAT_ID, urlencode('ربات فعال شد.'));
    }
    if($USER_ID==369023864 and $TEXT=="!غیر فعال")
    {
        if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `status`) VALUES ($CHAT_ID,false)";
                        $DB_CONNECTING->query($QUERY);
                        //sendMessage($CHAT_ID, urlencode('خوش آمدگویی غیر فعال شد.'));
                    }
                    else
                    {

                        $QUERY="UPDATE `lockeditems` SET `status`=false WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        //sendMessage($CHAT_ID, urlencode('خوش آمدگویی غیر فعال شد.'));
                    }
                    sendMessage($CHAT_ID, urlencode('ربات غیر فعال شد.'));
    }
                        
                    
                    
                
    
    //دستورات قفل
    if(isset($TEXT) and (isAdministrator($CHAT_Member))==true)
    {
        $fisrtChar=substr($TEXT,0,1);
        if($fisrtChar=="!")
        {
            //$splite=explode(" ",$TEXT);
            
            $num = preg_replace('/[^0-9]/', '', $TEXT);
            
            switch ($TEXT) {
                case "!شروع چالش ادد":
                    if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `challengeflag`) VALUES ($CHAT_ID,true)";
                        $DB_CONNECTING->query($QUERY);
                        //sendMessage($CHAT_ID, urlencode('خوش آمدگویی غیر فعال شد.'));
                    }
                    else
                    {

                        $QUERY="UPDATE `lockeditems` SET `challengeflag`=true WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        //sendMessage($CHAT_ID, urlencode('خوش آمدگویی غیر فعال شد.'));
                    }
                    break;
                case "!پایان چالش ادد":
                    if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `challengeflag`) VALUES ($CHAT_ID,false)";
                        $DB_CONNECTING->query($QUERY);
                        //sendMessage($CHAT_ID, urlencode('خوش آمدگویی فعال شد.'));
                    }
                    else
                    {

                        $QUERY="UPDATE `lockeditems` SET `challengeflag`=false WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        //sendMessage($CHAT_ID, urlencode('خوش آمدگویی فعال شد.'));
                    }
                    endAddChallenge($CHAT_ID,$DB_CONNECTING);
                    break;    
                case "!حذف خوش آمد":
                    if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `WelcomeMsg`) VALUES ($CHAT_ID,false)";
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('خوش آمدگویی غیر فعال شد.'));
                    }
                    else
                    {

                        $QUERY="UPDATE `lockeditems` SET `WelcomeMsg`=false WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('خوش آمدگویی غیر فعال شد.'));
                    }
                    break;
                case "!خوش آمد":
                    if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `WelcomeMsg`) VALUES ($CHAT_ID,true)";
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('خوش آمدگویی فعال شد.'));
                    }
                    else
                    {

                        $QUERY="UPDATE `lockeditems` SET `WelcomeMsg`=true WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('خوش آمدگویی فعال شد.'));
                    }
                    break;    
                case "!قفل چت":
                    if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `chat`) VALUES ($CHAT_ID,true)";
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل چت فعال شد'));
                    }
                    else
                    {

                    $QUERY="UPDATE `lockeditems` SET `chat`=true WHERE chatid=".$CHAT_ID;
                    $DB_CONNECTING->query($QUERY);
                    sendMessage($CHAT_ID, urlencode('قفل چت فعال شد'));
                    }
                    break;
                case "!قفل استیکر":
                    if(!$isExist)
                    {

                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `sticker`) VALUES ($CHAT_ID,true)";
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل استیکر فعال شد'));
                    }
                    else
                    {
                        $QUERY="UPDATE `lockeditems` SET `sticker`=true WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل استیکر فعال شد'));
                    }

                    break;
                case "!قفل گیف":
                    if(!$isExist)
                    {

                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `gif`) VALUES ($CHAT_ID,true)";
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل گیف فعال شد'));
                    }
                    else
                    {
                        $QUERY="UPDATE `lockeditems` SET `gif`=true WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل گیف فعال شد'));
                    }

                    break;
                case "!گیف":
                    if(!$isExist)
                    {

                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `gif`) VALUES ($CHAT_ID,false)";
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل گیف غیرفعال شد'));
                    }
                    else
                    {
                        $QUERY="UPDATE `lockeditems` SET `gif`=false WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل گیف غیرفعال شد'));
                    }

                    break;
                case "!قفل لینک":
                    if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `link`) VALUES ($CHAT_ID,true)";
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل لینک فعال شد'));
                    }
                    else
                    {
                        $QUERY="UPDATE `lockeditems` SET `link`=true WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل لینک فعال شد'));
                    }

                    break;
                    case "!چت":
                    if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `chat`) VALUES ($CHAT_ID,false)";
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل چت غیر فعال شد'));
                    }
                    else
                    {
                        $QUERY="UPDATE `lockeditems` SET `chat`=false WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل چت غیر فعال شد'));
                    }

                    break;
                    case "!لینک":
                    if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `link`) VALUES ($CHAT_ID,false)";
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل لینک غیر فعال شد'));
                    }
                    else
                    {
                        $QUERY="UPDATE `lockeditems` SET `link`=false WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل لینک غیر فعال شد'));
                    }

                    break;
                    case "!استیکر":
                    if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `sticker`) VALUES ($CHAT_ID,false)";
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل استیکر غیر فعال شد'));
                    }
                    else
                    {
                        $QUERY="UPDATE `lockeditems` SET `sticker`=false WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('قفل استیکر غیر فعال شد'));
                    }
                    case "!آزاد همه":
                    if(!$isExist)
                    {
                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `chat`,`sticker`,`link`) VALUES ($CHAT_ID,false,false,false)";
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('تمامی قفل ها غیر فعال شد'));
                    }
                    else
                    {
                        $QUERY="UPDATE `lockeditems` SET `chat`=false,`sticker`=false,`forward`=false,`link`=false WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                        sendMessage($CHAT_ID, urlencode('تمامی قفل ها غیر فعال شد'));
                    }
                    break;

                default:
                    break;
            }
            if($num!="" and isset($num) and $num>=0)
            {
                //sendMessage($CHAT_ID,"ok");
                if(!$isExist)
                    {

                        $QUERY="INSERT INTO `lockeditems`(`chatid`, `forceflag`,forcecnt) VALUES ($CHAT_ID,true,$num)";
                        $DB_CONNECTING->query($QUERY);
                    }
                    else
                    {
                        $QUERY="UPDATE `lockeditems` SET `forceflag`=true,forcecnt=$num WHERE chatid=".$CHAT_ID;
                        $DB_CONNECTING->query($QUERY);
                    }
            }
        }
    }
    
    //ارسال چت
    if(isset($LockStatus[0]) and $LockStatus[0]>0)
    {
        $Message_TYPE_FA='پیام';
        $isProhibited=true;
    }

    
    
    //ارسال استیکر
    if(isset($LockStatus[1]) and $LockStatus[1]>0)
    {
        if(isset($Message_Sticker))
        {
            $Message_TYPE_FA='استیکر';
            $isProhibited=true;
        }
    }
    //ارسال گیف
    if(isset($LockStatus[3]) and $LockStatus[3]>0)
    {
        if(isset($Document_TYPE) and $Document_TYPE=="video/mp4")
        {
            $Message_TYPE_FA='گیف';
            $isProhibited=true;
        }
    }
    
    //ارسال لینک
    if(isset($LockStatus[2]) and $LockStatus[2]>0)
    {
        //sendMessage($CHAT_ID,$Content);
//        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
//        if(preg_match($reg_exUrl, $TEXT, $url))
//        {
//            
//            $Message_TYPE_FA='لینک';
//                     $isProhibited=true;
//        }
     if(isset($Message_Entities) and (isAdministrator($CHAT_Member))==false)
     {
         
         foreach ($Message_Entities as $entity) {
             //sendMessage($CHAT_ID,$entity['type']);
             
             $Message_TYPE=$entity['type'];
             switch ($Message_TYPE) {
                 case "bot_command":
                     $Message_TYPE_FA='دستور ربات';
                     $isProhibited=true;
                     break;
                 case "url":
                     $Message_TYPE_FA='لینک';
                     $isProhibited=true;
                     break;
                 case "text_link":
                     $Message_TYPE_FA='لینک';
                     $isProhibited=true;
                     break;
                 case "text_mention":
                     $Message_TYPE_FA='لینک';
                     $isProhibited=true;
                     break;
                 case "mention":
                     $Message_TYPE_FA='لینک';
                     $isProhibited=true;
                     break;
                 default:
                     break;
             }
             
         }
         

     }
     if(isset($Caption_Entities) and (isAdministrator($CHAT_Member))==false)
     {
         
         foreach ($Caption_Entities as $entity) {
             //sendMessage($CHAT_ID,$entity['type']);
             
             $Message_TYPE=$entity['type'];
             switch ($Message_TYPE) {
                 case "bot_command":
                     $Message_TYPE_FA='دستور ربات';
                     $isProhibited=true;
                     break;
                 case "url":
                     $Message_TYPE_FA='لینک';
                     $isProhibited=true;
                     break;
                 case "text_link":
                     $Message_TYPE_FA='لینک';
                     $isProhibited=true;
                     break;
                 case "text_mention":
                     $Message_TYPE_FA='لینک';
                     $isProhibited=true;
                     break;
                 case "mention":
                     $Message_TYPE_FA='لینک';
                     $isProhibited=true;
                     break;
                 default:
                     break;
             }
             
         }
         

     }
     
    }
    if($isProhibited and $status==0)
    {
        $mes='لطفاجهت شارژ ربات اقدام نمایید'.chr(10).'@mali25zah';
        $mes=urlencode($mes);
        sendMessage($CHAT_ID,$mes);
        
        return;
    }
    if($isProhibited and (isAdministrator($CHAT_Member))==false and $status)
         {
            if($Message_TYPE_FA=="ادد اجباری")
            {
                $NOTE='کاربر '.$FNAME_inGroup.' '.$LNAME_inGroup.'عزیز'.chr(10).$diff.' نفر به گروه دعوت نمایید. سپس پیام خود را بگذارید.';
                $NOTE= urlencode($NOTE);
            }
            else {
                $NOTE='کاربر '.$FNAME_inGroup.' '.$LNAME_inGroup.' عزیز '.chr(10).'لطفا قوانین را رعایت نمایید'.chr(10).'ارسال '.$Message_TYPE_FA.' '.'ممنوع است';
                $NOTE= urlencode($NOTE);
            }
             sendMessage($CHAT_ID,$NOTE);
             deleteMessage($CHAT_ID,$Message_ID);
         }
    else if($isProhibited and $isRobot and $USERNAME_inGroup!="omid_WeatherBot" and $status ) {
        deleteMessage($CHAT_ID,$Message_ID);
    }
    
     
}






//End
$DB_CONNECTING->close();
exit();
