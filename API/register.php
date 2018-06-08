<?php
// UniBan 服务器注册API
// ✝️ God bless us.
// Copyright (C) EucalyptusLeaves 2018
if(!defined("UBSecurity")) exit("Access denied.");

$retjson =
[	"result"	=>	"failed",
	"reason"	=>	""];

isAllPostVarSet(["key","invitecode","name","software","version","category","locale"]);


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
            if(strlen($_POST['name'])>20) {
                $retjson["result"] = "failed";
                $retjson["reason"] = "Server name too long (>20)";
                exit(json_encode($retjson));
            }

            // 检查服务器软件类型
            $software=$_POST['software'];
            $supportedSoftwareName=[
                'Bukkit','Spigot','PaperSpigot','BungeeCord','Other'
            ];
            if (!in_array($software,$supportedSoftwareName)) {
                $software='Other';
            }

            //TODO: 检查服务器版本
            $version=$_POST['version'];

            // 检查服务器分类
            $category=$_POST['category'];
            $supportedCategory=[
                'Survival', 'Faction', 'Mini-Game', 'RP', 'Creative', 'Other'
            ];
            if (!in_array($category,$supportedCategory)) {
                $category='Other';
            }

            // 检查服务器语言偏好
            $locale=$_POST['locate'];
            $supportedLocate=[
                'zh_CN', 'en_US'
            ];
            if (!in_array($locale,$supportedLocate)) {
                $locale='en_US';
            }

            $sql = "UPDATE servers SET
            `ip`='".$userIP."',
            `name`='".$_POST['name']."',
            `software`='".$software."',
            `version`='".$version."',
            `category`='".$category."',
            `locale`='".$locale."'
            WHERE serverkey='".$_POST["key"]."'";
            /*
            */
            if($Mysql->query($sql)) {
                if($boundSID!=$serverID) $invitecodecount++;
                $Mysql->query("UPDATE invitecode SET `count`='".$invitecodecount."' WHERE `code` = '".$_POST["invitecode"]."'");
                $Mysql->query("UPDATE invitecode SET `boundserverid`='".$serverID."' WHERE `code` = '".$_POST["invitecode"]."'");
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
