<?php
// UniBan 默认API提示
// ✝️ God bless us.
// Copyright (C) EucalyptusLeaves 2018
echo "<h1>Null</h1><br>UniBan API<br><br>";

if ($_GET['test']=="keyword") {
    echo "<h2>Test</h2>";
    echo $_GET['text']."<br>";
    print_r(getKeyWords($_GET['text']));
}
?>
