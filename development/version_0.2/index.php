<?php
include("lib.php");
define("PAGENAME","Home");
$user=usr($session_key, $db);
include("templates/member_header.php");
?>
<table width="100%">
<tr><td width="50%">
<fieldset width="100%"><legend>User Information</legend>
<b>Username</b>: <?=userrank($user->name,$db)?><br/>
<b>Email</b>: <?=$user->email?><br/>
</td></tr>
</table>
<?php
include("templates/member_footer.php");
?>