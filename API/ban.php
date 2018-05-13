<?php
// UniBan 封禁上报API
// ✝️ God bless us.
// Copyright (C) EucalyptusLeaves 2018
if(!defined("UBSecurity")) exit("Access denied.");

$retjson =
[	"result"	=>	"failed",
	"reason"	=>	""];

isAllPostVarSet(["token","uuid","displayname","level","reason"]);

checkInput(); //注入检测

if ($_POST["level"]>3 || $_POST["level"]<1) {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Illegal argument:level (1~3)";
	exit(json_encode($retjson))
}

$userIP = $_SERVER['REMOTE_ADDR'];
$server = $Mysql->get_row("SELECT * FROM servers WHERE token='".$_POST['token']."'");
if($server!=false) {
    $serverID=$server['serverid'];
    if (isTokenLegal($_POST['token'])) { //TODO: Token检查
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
            $playerBannedResult = $Mysql->get_row("SELECT * FROM banned WHERE UUID='".$_POST['uuid']."'");
            if($playerBannedResult != false) {
                if($playerBannedResult['fromserver'] != $serverID) {
                    $retjson["result"] = "failed";
                    $retjson["reason"] = "The player has already banned by other server.";
                    exit(json_encode($retjson));
                }
                else {
                    $sql="UPDATE banned SET `reason` = '".$_POST['reason']."',`level` = '".$_POST['level']."',`latestname` = '".$_POST['displayname']."' WHERE `UUID` = '".$_POST["uuid"]."'";
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
                $sql = "INSERT INTO `banned` (`bannedid`, `UUID`, `latestname`, `operatorid`, `fromserver`, `reason`, `level`, `screenshot`, `banneddate`, `vote`) VALUES ('".$bannedID."', '".$_POST['uuid']."', '".$_POST['displayname']."', '".$operatorID."', '".$serverID."', '".$_POST['reason']."', '".$_POST['level']."', 'null.jpg', '".time()."', '0')";
                if($Mysql->query($sql)) {
                    $retjson["result"] = "OK";
                    $retjson["reason"] = "Banned player information added. Please upload screenshot later.";
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
        $retjson["reason"] = "Invalid session";
        exit(json_encode($retjson));
    }
}
else {
    $retjson["result"] = "failed";
    $retjson["reason"] = "Server not registered or token not correct";
    exit(json_encode($retjson));
}



?>
