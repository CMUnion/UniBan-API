<?php
// UniBan AccessToken有效性检查API
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
if ($_REQUEST["serverid"]=="") {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Incompleted argument:serverid";
	$isFailed=true;
}

checkInput(); //注入检测

if($isFailed) exit(json_encode($retjson));

if(isTokenLegal($_REQUEST["token"],$_REQUEST["serverid"])) {
    $retjson['result'] = "OK";
}
exit(json_encode($retjson));
?>