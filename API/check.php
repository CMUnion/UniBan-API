<?php
// UniBan 查询封禁状态API
// ✝️ God bless us.
// Copyright (C) EucalyptusLeaves 2018
if(!defined("UBSecurity")) exit("Access denied.");

$retjson = 
[	"result"	=>	"failed",
	"banned"	=>	"false",
	"reason"	=>	"",
	"level"    	=>	"0",
	"fromServer"	=>	""
];
$isFailed = false;

if ($_POST["uuid"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:uuid";
	$isFailed=true;
}

if ($isFailed) exit(json_encode($retjson));

checkInput(); //注入检测


$userIP = $_SERVER['REMOTE_ADDR'];
if(1==1) {
    $😂=$Mysql->get_row("SELECT * FROM banned WHERE UUID='".$_POST["uuid"]."'");
    $💻=$Mysql->get_row("SELECT name FROM servers WHERE serverid='".$😂['fromserver']."'");
    
    if($😂!=false) {
        if($retjson['level']==0) {
            $retjson['result'] = "OK";
            $retjson['banned'] = "false";
        }
        else {
            $retjson['result'] = "OK";
            $retjson['banned'] = "true";
            $retjson['reason'] = $😂['reason'];
            $retjson['fromServer'] = $💻['name'];
            $retjson['level'] = $😂['level'];
        }
    }
    else {
        $retjson['result'] = "OK";
        $retjson['banned'] = "false";
    }
    exit(json_encode($retjson));
}
else {
    $retjson["result"] = "failed";
    $retjson["reason"] = "Invalid session";
    exit(json_encode($retjson));
}


?>