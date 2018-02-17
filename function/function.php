<?php
error_reporting(0);
define('API_TOKEN', '506982730:AAEyVVF4NMNyuFwe5da89lmgjs3rID-Q0Rk');
define('API_REQUEST', 'https://api.telegram.org/bot'.API_TOKEN.'/');

function sendMessage($CHAT_ID,$MESSAGE)
{
    
        
        $METHOD="sendMessage";
        $Request_To_Server=API_REQUEST . $METHOD ."?"."chat_id=".$CHAT_ID."&"."text=".$MESSAGE;
        file_get_contents($Request_To_Server);
    
}
function deleteMessage($CHAT_ID,$MESSAGE_ID)
{
    
        
        $METHOD="deleteMessage";
        $Request_To_Server=API_REQUEST . $METHOD ."?"."chat_id=".$CHAT_ID."&"."message_id=".$MESSAGE_ID;
        file_get_contents($Request_To_Server);
    
}
function getChatMember($CHAT_ID,$USER_ID)
{
    
        
        $METHOD="getChatMember";
        $Request_To_Server=API_REQUEST . $METHOD ."?"."chat_id=".$CHAT_ID."&"."user_id=".$USER_ID;
        return file_get_contents($Request_To_Server);
    
}

function kickChatMember($CHAT_ID,$USER_ID)
{
     $METHOD="kickChatMember";
        $Request_To_Server=API_REQUEST . $METHOD ."?"."chat_id=".$CHAT_ID."&"."user_id=".$USER_ID;
        return file_get_contents($Request_To_Server);
}

function getMe()
{
     $METHOD="getMe";
        $Request_To_Server=API_REQUEST . $METHOD;
        return file_get_contents($Request_To_Server);
}

function getChatAdmin($CHAT_ID)
{
    
        
        $METHOD="getChatAdministrators";
        $Request_To_Server=API_REQUEST . $METHOD ."?"."chat_id=".$CHAT_ID;
        return file_get_contents($Request_To_Server);
    
}


