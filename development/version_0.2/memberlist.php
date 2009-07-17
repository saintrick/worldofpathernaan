<?php
include("lib.php");
define("PAGENAME","Member List");
$user=usr($session_key, $db);
include("templates/member_header.php");
//start of pagination part 1
if (!(isset($pagenum)))
{
$pagenum = 1;
}

$data = $db->execute("SELECT * FROM users");
$rows = $data->recordcount();

//This is the number of results displayed per page
$page_rows = 4;

$last = ceil($rows/$page_rows);

if ($pagenum < 1)
{
$pagenum = 1;
}
elseif ($pagenum > $last)
{
$pagenum = $last;
}

$max = 'limit ' .($pagenum - 1) * $page_rows .',' .$page_rows; 
$query=$db->execute("select * from `users` {$max}");
//end of pagination pt. 1
echo "<table width=\"100%\">";
while($member=$query->fetchrow()){
	echo "<tr><td width=\"75%\"><a href=\"profile.php?id=".$member['id']."\">".userrank($member['name'],$db)."</a></td><td width=\"25%\"><a href=\"mail.php?p=newmail&uid=".$member['id']."\">Mail</a> | <a href=\"battle.php?act=attack&id=".$member['id']."\">Fight Player!</a></td></tr>";
}
echo "</table>";
//Start of pagination pt. 2
// This shows the user what page they are on, and the total number of pagestl
echo " --Page $pagenum of $last-- <p>";

// First we check if we are on page one. If we are then we don't need a link to the previous page or the first page so we do nothing. If we aren't then we generate links to the first page, and to the previous page.
if ($pagenum == 1){
}else{
	echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=1'> <<-First</a> ";
	echo " ";
	$previous = $pagenum-1;
	echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=$previous'> <-Previous</a> ";
}

//This does the same as above, only checking if we are on the last page, and then generating the Next and Last links
if ($pagenum == $last){
}else {
$next = $pagenum+1;
echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=$next'>Next -></a> ";
echo " ";
echo " <a href='{$_SERVER['PHP_SELF']}?pagenum=$last'>Last ->></a> ";
}
//end of pagination part 2
include("templates/member_footer.php");
?>