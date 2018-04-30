<?php
// UniBan 服务器注册API
// ✝️ God bless us.
// Copyright (C) EucalyptusLeaves 2018
if(!defined("UBSecurity")) exit("Access denied.");

$retjson = 
[	"result"	=>	"failed",
	"reason"	=>	""];
$isFailed = false;

if ($_POST["key"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:key";
	$isFailed=true;
}
else if ($_POST["invitecode"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:invitecode";
	$isFailed=true;
}

checkInput(); //注入检测

if ($isFailed) exit(json_encode($retjson));


if ($Mysql->get_row("SELECT * FROM invitecode WHERE code='".$_POST["invitecode"]."'")!=false) {
	$userIP = $_SERVER['REMOTE_ADDR'];
	if($Mysql->get_row("SELECT * FROM servers WHERE serverkey='".$_POST["key"]."'")!=false) {
        $serverID=$Mysql->get_row("SELECT serverid FROM servers WHERE serverkey='".$_POST["key"]."'")['serverid'];
        $invitecodecount=$Mysql->get_row("SELECT count FROM invitecode WHERE code='".$_POST["invitecode"]."'")['count'];
        $boundSID=$Mysql->get_row("SELECT boundserverid FROM invitecode WHERE code='".$_POST["invitecode"]."'")['boundserverid'];
        if($invitecodecount>=1 && $boundSID != $serverID) {
                $retjson["result"] = "failed";
                $retjson["reason"] = "Invitecode not available (Count:".$invitecodecount.")";
                exit(json_encode($retjson));
        }
        else {
            $sql = "UPDATE servers SET ip = '".$userIP."' WHERE serverkey='".$_POST["key"]."'";
            if($Mysql->query($sql)) {
                if($boundSID!=$serverID) $invitecodecount++;
                $Mysql->query("UPDATE invitecode SET `count` = '".$invitecodecount."' WHERE `code` = '".$_POST["invitecode"]."'");
                $Mysql->query("UPDATE invitecode SET `boundserverid` = '".$serverID."' WHERE `code` = '".$_POST["invitecode"]."'");
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