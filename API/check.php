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

checkInput(); //注入检测

isAllPostVarSet["uuid"];


$player=$Mysql->get_row("SELECT * FROM `banned` WHERE UUID='".$_POST["uuid"]."'");

if($player!=false) {
    if($player['level']==0) {
        $retjson['result'] = "OK";
        $retjson['banned'] = "false";
    }
    else {
        $server=$Mysql->get_row("SELECT * FROM servers WHERE serverid='".$player['fromserver']."'");
        $retjson['result'] = "OK";
        $retjson['banned'] = "true";
        $retjson['reason'] = $player['reason'];
        $retjson['fromServer'] = $server['name'];
        $retjson['level'] = $player['level'];
    }
}
else {
    $retjson['result'] = "OK";
    $retjson['banned'] = "false";
}
exit(json_encode($retjson));

?>
