<?php
// UniBan 解除封禁API
// ✝️ God bless us.
// Copyright (C) EucalyptusLeaves 2018
if(!defined("UBSecurity")) exit("Access denied.");

$retjson = 
[	"result"	=>	"failed",
	"reason"	=>	""];
$isFailed = false;

if ($_REQUEST["key"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:key";
	$isFailed=true;
}
else if ($_REQUEST["uuid"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:uuid";
	$isFailed=true;
}

checkInput(); //注入检测

if ($isFailed) exit(json_encode($retjson));


if (1==1) { //TODO: Token检查
	$userIP = $_SERVER['REMOTE_ADDR'];
	if($Mysql->get_row("SELECT * FROM servers WHERE serverkey='".$_REQUEST["key"]."'")!=false) {
        $serverID=$Mysql->get_row("SELECT serverid FROM servers WHERE serverkey='".$_REQUEST["key"]."'")['serverid'];
        $operatorID=$Mysql->get_row("SELECT ownerid FROM servers WHERE serverkey='".$_REQUEST["key"]."'")['ownerid'];
        $invitecode=$Mysql->get_row("SELECT * FROM invitecode WHERE boundserverid='".$serverID."'");
        if($invitecode==false) {
            $retjson["result"] = "failed";
            $retjson["reason"] = "Premission denied";
            exit(json_encode($retjson));
        }
        else if($operatorID==false || $invitecode==false) {
            $retjson["result"] = "failed";
            $retjson["reason"] = "Internal server error";
            exit(json_encode($retjson));
        }
        else {
            $playerBannedResult = $Mysql->get_row("SELECT * FROM banned WHERE UUID='".$_REQUEST['uuid']."'");
            if($playerBannedResult != false) {
                if($playerBannedResult['fromserver'] != $serverID) {
                    $retjson["result"] = "failed";
                    $retjson["reason"] = "The player was banned by other server.";
                    exit(json_encode($retjson));
                }
                else {
                    $sql="UPDATE banned SET `level` = '0' WHERE `UUID` = '".$_REQUEST["uuid"]."'";
                    if($Mysql->query($sql)) {
                        $retjson["result"] = "OK";
                        $retjson["reason"] = "Unbanned";
                        exit(json_encode($retjson));
                    }
                    else {
                        $retjson["result"] = "failed";
                        $retjson["reason"] = "Failed updating unbanning information, Try again later";
                        exit(json_encode($retjson));
                    }
                }
            }
            else {
                $retjson["result"] = "failed";
                $retjson["reason"] = "The player is not banned";
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
    $retjson["reason"] = "Invalid session";
    exit(json_encode($retjson));
}

?>