<?php
include("lib.php");
$min=90;
define("PAGENAME","Home");
$user=usr($session_key, $db);
include("../templates/admin_header.php");
?>
Welcome to the <?=$settings->gamename?> admin panel.
<br/><br/>
DO NOT ABUSE YOUR POWERS
<br/><br/>
<?php
include("../templates/admin_footer.php");
?>