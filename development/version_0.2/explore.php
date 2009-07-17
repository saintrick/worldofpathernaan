<?php
include("lib.php");
define("PAGENAME","Explore The City");
$user=usr($session_key, $db);
include("templates/member_header.php");
?>
<table width="100%">
<tr valign="top"><td width="50%">
<b>Community</b><br/>
<a href="mail.php">Mail</a>[<?=getmailcount($user->id,$db)?>]<br/>
<a href="memberlist.php">Member List</a><br/>
</td><td width="50%">
<b>City Information</b><br/>
<?php
$citycheck=$db->execute("select * from cities where `id`=?",array($user->city));
$city=$citycheck->fetchrow();
?>
You are currently in <?=$city['name']?><br/>
<a href="travel.php">Travel</a>
</td></tr>
</table>
<?php
include("templates/member_footer.php");
?>