<?php
// UniBan AccessToken有效性检查API
// ✝️ God bless us.
// Copyright (C) EucalyptusLeaves 2018
if(!defined("UBSecurity")) exit("Access denied.");

$retjson =
[	"result"	=>	"failed",
	"reason"	=>	""];

isAllPostVarSet(["token","serverid"]);

checkInput(); //注入检测

if(isTokenLegal($_POST["token"])) {
    $retjson['result'] = "OK";
}
exit(json_encode($retjson));

?>
