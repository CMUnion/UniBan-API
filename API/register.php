<?php
// UniBan 服务器注册API
// Copyright (C) EucalyptusLeaves 2018
if(!defined("UBSecurity")) exit("Access denied.");

$retjson = 
[	"result"	=>	"error",
	"reason"	=>	""];
$isFailed = false;

if ($_REQUEST["key"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:key";
	$isFailed=true;
}
else if ($_REQUEST["invitecode"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:invitecode";
	$isFailed=true;
}
else if ($_REQUEST["key"]!=addslashes($_REQUEST["key"]) || $_REQUEST["invitecode"]!=addslashes($_REQUEST["invitecode"])) {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Invalid argument";
	$isFailed=true;
}
/*
else if (!preg_match("/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i",$_REQUEST["invitecode"])) {
	$retjson["result"] = "error";
	$retjson["reason"] = "Invalid invitecode";
	$retjson["extrainfo"] = "";
	$isFailed=true;
}*/

if ($isFailed) exit(json_encode($retjson));


if ($Mysql->count("SELECT * FROM invitecode WHERE code='".$_REQUEST["invitecode"]."'")==1) {
	$userIP = $_SERVER['REMOTE_ADDR'];
	if($Mysql->count("SELECT * FROM servers WHERE serverkey='".$_REQUEST["key"]."'")==1) {
        $invitecodecount=(int)$Mysql->query("SELECT `count` FROM `invitecode` WHERE `code`='".$_REQUEST["invitecode"]."'");
        if($invitecodecount>1) {
                $retjson["result"] = "failed";
                $retjson["reason"] = "Invitecode not available";
                exit(json_encode($retjson));
        }
        else {
            $sql = "UPDATE servers SET ip = '".$userIP."' WHERE serverkey='".$_REQUEST["key"]."'";
            if($Mysql->query($sql)) {
                $invitecodecount++;
                $Mysql->query("UPDATE invitecode SET `count` = '".(String)$invitecodecount."' WHERE `code` = '".$_REQUEST["invitecode"]."'");
                $retjson["result"] = "OK";
                $retjson["reason"] = "Server information updated";
                exit(json_encode($retjson));
            }
            else {
                $retjson["result"] = "failed";
                $retjson["reason"] = "Failed updating server information, Try again later";
                exit(json_encode($retjson));
            }
        }
	}
	else {
		$retjson["result"] = "failed";
		$retjson["reason"] = "Server not registered or key not correct";
		exit(json_encode($retjson));
	}
	
}
else {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Invitecode not exist";
	exit(json_encode($retjson));
}

?>