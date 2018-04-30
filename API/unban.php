<?php
// UniBan 解除封禁API
// ✝️ God bless us.
// Copyright (C) EucalyptusLeaves 2018
if(!defined("UBSecurity")) exit("Access denied.");

$retjson = 
[	"result"	=>	"failed",
	"reason"	=>	""];
$isFailed = false;

if ($_REQUEST["token"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:token";
	$isFailed=true;
}
else if ($_REQUEST["uuid"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:uuid";
	$isFailed=true;
}

checkInput(); //注入检测

if ($isFailed) exit(json_encode($retjson));


$userIP = $_SERVER['REMOTE_ADDR'];
$server = $Mysql->get_row("SELECT * FROM servers WHERE token='".$_REQUEST['token']."'");
if($server!=false) {
    $serverID=$server['serverid'];
    if (isTokenLegal($_REQUEST['token'])) { //TODO: Token检查
        $operatorID=$server['ownerid'];
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
        $retjson["reason"] = "Server not registered or token not correct";
        exit(json_encode($retjson));
   }
}
else {
    $retjson["result"] = "failed";
    $retjson["reason"] = "Invalid session";
    exit(json_encode($retjson));
}

?>