<?php
// UniBan AccessToken API
// ✝️ God bless us.
// Copyright (C) EucalyptusLeaves 2018
if(!defined("UBSecurity")) exit("Access denied.");

$retjson =
[	"result"	=>	"failed",
	"reason"	=>	"",
	"token"	=>	""];

isAllPostVarSet(["key"]);


$userIP = $_SERVER['REMOTE_ADDR'];
$server=$Mysql->get_row("SELECT * FROM servers WHERE serverkey='".$_POST["key"]."'");
if($server!=false) {
    if($server['ip']!=$userIP) {
        $retjson["result"] = "failed";
        $retjson["reason"] = "Server IP not matched";
        exit(json_encode($retjson));
    }
    else {
        $token=getToken($_POST["key"]);
        if ($token==false) {
            $retjson["result"] = "failed";
            $retjson["reason"] = "Failed generating token";
        }
        else {
            $retjson['result'] = "OK";
            $retjson['token'] = $token;
        }
        exit(json_encode($retjson));
    }

}
else {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Server not registered or key not correct";
	exit(json_encode($retjson));
}
?>
