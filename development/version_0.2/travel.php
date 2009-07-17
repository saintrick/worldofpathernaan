<?php
include("lib.php");
$user=usr($session_key, $db);
define("PAGENAME","Travel Agent");
include("templates/member_header.php");
switch($_GET['act']){
	default:
		echo "Where would you like to go?<br/>";
		echo "<table width=\"100%\"><tr><th width=\"25%\">City Name</th><th width=\"25%\">Price</th><th width=\"25%\">Users In City</th><th width=\"25%\">Actions</th></tr>";
		$query=$db->execute("select * from `cities`");
		while($c=$query->fetchrow()){
			$userstotal1=$db->execute("select * from `users` where `city`=?",array($c['id']));
			$users=$userstotal1->recordcount();
			echo "<tr align=\"center\"><td>".$c['name']."</td><td>".$c['price']."</td><td>".$users."</td><td><a href=\"travel.php?act=travel&city=".$c['id']."\">Move To City</a></td></tr>";
		}
		echo "</table>";
		break;

	case "travel":
		if($user->city == $_GET['city']){
			echo "you are already in this city!";
		}else{
			$query=$db->execute("select * from `cities` where `id`=?",array($_GET['city']));
			if($query->recordcount()==0){
				echo "sorry, this city doesn't exist";
			}else{
				$city=$query->fetchrow();
				if($user->cash < $city['price']){
					echo "sorry, you don't have enough cash!";
				}else{
					$price=$user->cash - $city['price'];
					$query2=$db->execute("update `users` set `city`=?, `cash`=? where `id`=?",array($_GET['city'], $price, $user->id));
					if($query2){
						echo "You have moved to ".$city['name'];
					}else{
						echo "there was an error moving you. please contact an administrator";
					}
				}
			}
		}
		break;
}
include("templates/member_footer.php");
?>