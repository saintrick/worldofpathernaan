<?php
include("lib.php");
$user=usr($session_key, $db);
$query=$db->execute("select * from `users` where `id`=?",array($_GET['id']));
if($query->recordcount() != 1){
	header("location: memberlist.php");
	exit;
}
$profile=$query->fetchrow();
define("PAGENAME",$profile['name']."'s profile");
include("templates/member_header.php");
$city=$db->GetOne("select `name` from `cities` where `id`=?",array($profile['city']));
?>
<b>Username:</b> <?=userrank($profile['name'],$db)?><br/>
<b>Join Date:</b> <?=date("D, d M Y H:i:s",$profile['regdate'])?><br/>
<b>Cash:</b> <?=$profile['cash']?><br/>
<b>City:</b> <?=$city?><br/>
<?php
include("templates/member_footer.php");