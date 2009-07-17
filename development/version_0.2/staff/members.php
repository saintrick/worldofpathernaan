<?php
include("lib.php");
$min=90;
define("PAGENAME","Member List");
$user=usr($session_key, $db);
include("../templates/admin_header.php");
$query=$db->execute("select * from `users`");
while($m=$query->fetchrow()){
	echo "<a href=\"edituser.php?id=".$m['id']."\">".$m['name']."</a><br/>";
}
include("../templates/admin_footer.php");
?>