<?php
include("lib.php");
define("PAGENAME","Mail");
$user=usr($session_key, $db);
include("templates/member_header.php");
echo "<a href=\"mail.php\">Inbox</a> | <a href=\"mail.php?p=newmail\">New Message</a><hr>";
switch($_GET['p']){
	default: //view inbox
		echo "<table width=\"100%\"><tr><th width=\"75%\">Title</th><th width=\"25%\">From</th></tr>";
		$query=$db->execute("select * from `mail` where `to`=? and `status`!=? order by `id` desc",array($user->id,"deleted"));
		while($mail=$query->fetchrow()){
			$from=$db->GetOne("select `name` from `users` where `id`=?",array($mail['from']));
			echo "<tr><td><a href=\"mail.php?p=viewmail&id=".$mail['id']."\">".$mail['name']."</a> (".$mail['status'].")</td><td><a href=\"profile.php?id=".$mail['from']."\">".userrank($from,$db)."</a></td></tr>";
		}
		echo "</table>";
		break;

	case "viewmail":
		$query=$db->execute("select * from `mail` where `id`=? and `to`=?",array($_GET['id'], $user->id));
		if($query->recordcount() == 0){
			echo "sorry, this mail either doesn't exist or doesn't belong to you. If you belive this is an error please contact an administrator";
		}else{
			$mail=$query->fetchrow();
			$from=$db->GetOne("select `name` from `users` where `id`=?",array($mail['from']));
			?>
			<b><?=$mail['name']?></b><br/>
			From: <a href="profile.php?id=<?=$mail['from']?>"><?=userrank($from,$db)?></a><br/>
			Date: <?=date("D, d M Y H:i:s",$mail['senddate'])?>
			Message:<br/>
			<i><?=nl2br($mail['body'])?></i><hr>
			<a href="mail.php?p=newmail&do=reply&mid=<?=$mail['id']?>">Reply</a>
			<?php
			if($mail['status'] == "unread"){
				$changestatus=$db->execute("update `mail` set `status`=? where `id`=?",array("read",$mail['id']));
			}
		}
		break;

	case "newmail":
		if(!isset($_POST['submit'])){
			if($_GET['do'] == "reply"){
				$query=$db->execute("select * from `mail` where `id`=? and `to`=?",array($_GET['mid'],$user->id));
				if($query->recordcount() == 1){
					$mail=$query->fetchrow();
					$from=$db->GetOne("select `name` from `users` where `id`=?",array($mail['from'])); //$mail['body']
					$body="\n\n-------------ORIGINAL MESSAGE-------------\nSent By: ".$from."\nSent On: ".date("D, d M Y H:i:s",$mail['senddate'])."\nMessage: \n\n".$mail['body'];
					echo "<form method=\"post\">To: <input type=\"text\" name=\"to\" value=\"".$from."\"><br/>Title: <input type=\"text\" name=\"name\" value=\"RE:".$mail['name']."\"><br/>Body<br/><textarea name=\"body\" cols=\"50\" rows=\"10\">".$body."</textarea><br/><input type=\"submit\" name=\"submit\" value=\"send\"></form>";
				}else{
					echo "sorry, this message doesn't exist";
				}
			}else{
				if(isset($_GET['uid'])){
					$query=$db->execute("select * from `users` where `id`=?",array($_GET['uid']));
					if($query->recordcount() == 1){
						$to1=$query->fetchrow();
						$to=$to1['name'];
					}else{
						$to = "";
					}
				}else{
					$to = "";
				}
				echo "<form method=\"post\">To: <input type=\"text\" name=\"to\" value=\"".$to."\"><br/>Title: <input type=\"text\" name=\"name\"><br/>Body<br/><textarea name=\"body\" cols=\"50\" rows=\"10\"></textarea><br/><input type=\"submit\" name=\"submit\" value=\"send\"></form>";
			}
		}else{
			if($_POST['to'] == ""){
				echo "the 'to' field is required.";
			}elseif($_POST['name'] == ""){
				echo "the 'title' field is required";
			}elseif($_POST['body'] == ""){
				echo "the 'body' field is required";
			}else{
				$toquery = $db->execute("select * from `users` where `name`=?",array($_POST['to']));
				if($toquery->recordcount() == 1){
					$tofet=$toquery->fetchrow();
					$to=$tofet['id'];
					$name=htmlentities($_POST['name'],ENT_QUOTES);
					$senddate = time();
					$from = $user->id;
					$body = htmlentities($_POST['body'],ENT_QUOTES);
					$ins=$db->execute("INSERT INTO `pathernaan`.`mail` (`name`, `senddate`, `to`, `from`, `body`) VALUES ('{$name}', '{$senddate}', '{$to}', '{$from}', '{$body}')");
					if($ins){
						echo "Mail was sent!";
					}else{
						echo "There was an error sending the mail, please contact an administrator";
					}
				}else{
					echo "sorry, this user doesn't exist";
				}
			}
		}
}
include("templates/member_footer.php");
?>