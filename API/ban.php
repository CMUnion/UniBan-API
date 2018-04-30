<?php
// UniBan 封禁上报API
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
else if ($_REQUEST["displayname"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:displayname";
	$isFailed=true;
}
else if ($_REQUEST["level"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:level";
	$isFailed=true;
}
else if ($_REQUEST["level"]>3 || $_REQUEST["level"]<1) {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Illegal argument:level (1~3)";
	$isFailed=true;
}
else if ($_REQUEST["reason"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:reason";
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
                    $retjson["reason"] = "The player has already banned by other server.";
                    exit(json_encode($retjson));
                }
                else {
                    $sql="UPDATE banned SET `reason` = '".$_REQUEST['reason']."',`level` = '".$_REQUEST['level']."',`latestname` = '".$_REQUEST['displayname']."' WHERE `UUID` = '".$_REQUEST["uuid"]."'";
                    if($Mysql->query($sql)) {
                        $retjson["result"] = "OK";
                        $retjson["reason"] = "Banned player information updated";
                        exit(json_encode($retjson));
                    }
                    else {
                        $retjson["result"] = "failed";
                        $retjson["reason"] = "Failed updating banned player information, Try again later";
                        exit(json_encode($retjson));
                    }
                }
            }
            else {
                $bannedID = $Mysql->count("SELECT COUNT(*) FROM banned")[0]+1;
                $sql = "INSERT INTO `banned` (`bannedid`, `UUID`, `latestname`, `operatorid`, `fromserver`, `reason`, `level`, `screenshot`, `banneddate`, `vote`) VALUES ('".$bannedID."', '".$_REQUEST['uuid']."', '".$_REQUEST['displayname']."', '".$operatorID."', '".$serverID."', '".$_REQUEST['reason']."', '".$_REQUEST['level']."', 'null.jpg', '".time()."', '0')";
                if($Mysql->query($sql)) {
                    $retjson["result"] = "OK";
                    $retjson["reason"] = "Banned player information added";
                    exit(json_encode($retjson));
                }
                else {
                    $retjson["result"] = "failed";
                    $retjson["reason"] = "Failed updating banned player information, Try again later ($bannedID)";
                    exit(json_encode($retjson));
                }
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