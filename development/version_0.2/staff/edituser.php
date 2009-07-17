<?php
include("lib.php");
$min=90;
$db->debug=true;
define("PAGENAME","Edit User");
$user=usr($session_key, $db);
include("../templates/admin_header.php");
$userinfo=$db->execute("select * from `users` where `id`=?",array($_GET['id']));
if($userinfo->recordcount() == 1){
	$m=$userinfo->fetchrow();
	if($m['rank'] == 100 and groupinfo($user->rank,"permission",$db) != 100){
		echo "sorry, this user is an owner, and you don't have the permissions to edit them";
	}else{
		if(!isset($_POST['submit'])){
			if(groupinfo($user->rank,"permission",$db) == 100){
				$getgroups=$db->execute("select * from `groups`");
			}else{
				$getgroups=$db->execute("select * from `groups` where `permission`!=?",array(100));
			}
			$cities=$db->execute("select * from `cities`");
			?>
			<form method="post">
			Username: <input type="text" name="name" value="<?=$m['name']?>"><br/>
			Email: <input type="text" name="email" value="<?=$m['email']?>"><br/>
			Rank: <select name="rank">
			<?php
				while($g=$getgroups->fetchrow()){
					if($g['id'] == groupinfo($m['rank'],"id",$db)){
						$selected = " selected=\"true\"";
					}else{
						$selected = " ";
					}
					echo "<option value=\"".$g['id']."\"{$selected}>".$g['name']."</option>";
				}
			?>
			</select><br/>
			City: <select name="city">
			<?php
				while($c=$cities->fetchrow()){
					if($c['id'] == $user->city){
						$selected = " selected=\"true\"";
					}else{
						$selected = " ";
					}
					echo "<option value=\"".$c['id']."\"{$selected}>".$c['name']."</option>";
				}
			?>
			</select><br/>
			Cash: <input type="text" name="cash" value="<?=$m['cash']?>"><br/>
			<input type="submit" name="submit" value="Edit!">
			</form>
			<?php
			//id 	name 	salt 	pass 	email 	rank 	city 	cash 	eng 	meng 	regdate 	last_active 	register_ip 	last_ip
		}else{
			//$query1=$db->execute("update `users` set `name`=?, `email`=?, `rank`=?, `city`=?, `cash`=? where `id`=?'",array($_POST['name'], $_POST['email'],$_POST['rank'],$_POST['city'],$_POST['cash'],$_GET['id']));
			$name=$_POST['name'];
			$email=$_POST['email'];
			$rank=$_POST['rank'];
			$city=$_POST['city'];
			$cash=$_POST['cash'];
			$query1=$db->execute("update `users` set `name`=?, `email`=?, `rank`=?, `city`=?, `cash`=? where `id`=?",array($name,$email,$rank,$city,$cash,$_GET['id']));
			if($query1){
				echo "user edited";
			}else{
				echo "error!";
			}
		}
	}
	?>
	<?php
}else{
	echo "sorry, this user doesn't exist";
}
include("../templates/admin_footer.php");
?>