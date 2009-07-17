<?php
if(groupinfo($user->rank,"permission",$db) < $min){
	header("location: ../index.php");
	exit;
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Admin: <?=PAGENAME?> - <?=$settings->gamename?></title>
<link rel="stylesheet" href="../templates/style.css" type="text/css" />
</head>
<body>
<div id="container">
<div id="header">
<div id="headerimg">
<p></p>
</div>
</div>
<div id="nav">
<ul>
<li><a href="index.php" class="selected">Home</a></li>
<li><a href="../index.php" class="selected">Game</a></li>
<li><a href="members.php" class="selected">Users</a></li>
</ul>
</div>
<div id="content">
<div id="page">
<p>