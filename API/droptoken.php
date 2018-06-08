<?php
// UniBan AccessToken API
// ✝️ God bless us.
// Copyright (C) EucalyptusLeaves 2018
if(!defined("UBSecurity")) exit("Access denied.");

$retjson =
[	"result"	=>	"failed",
	"reason"	=>	""
];

isAllPostVarSet(["token"]);

$userIP = $_SERVER['REMOTE_ADDR'];
$server=$Mysql->get_row("SELECT * FROM servers WHERE token='".$_POST["token"]."'");
if($server!=false) {
    if($server['ip']!=$userIP) {
        $retjson["result"] = "failed";
        $retjson["reason"] = "Server IP not matched";
        exit(json_encode($retjson));
    }
    else {
        $token=getToken($server['serverkey'],true);
        if ($token==false) {
            $retjson["result"] = "failed";
            $retjson["reason"] = "Failed droping token";
        }
        else {
            $retjson['result'] = "OK";
        }
        exit(json_encode($retjson));
    }

}
else {
	$retjson["result"] = "failed";
	$retjson["reason"] = "Server not registered or token not correct";
	exit(json_encode($retjson));
}
?>
