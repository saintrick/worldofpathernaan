<?php
include("lib.php");
define("PAGENAME","Login");
if(!isset($_POST['submit'])){
	include("templates/guest_header.php");
	?>
	Login:<br/>
	<form method="post">
	<input type="text" name="name" value="username"><input type="password" name="pass" value="++++++++"><input type="submit" name="submit" value="login">
	</form>
	<br/>
	<br/>
	<i>Welcome to pathernaan the free PHP based RPG engine.  Edit login.php to change t his description :)</i>
	<?php
	include("templates/guest_footer.php");
}else{
	$check1=$db->execute("select * from `users` where `name`=?",array($_POST['name']));
	if($check1->recordcount() == 1){
		$u1=$check1->fetchrow();
		$pass=md5(md5($_POST['pass']).md5($u1['salt']));
		$check=$db->execute("select * from `users` where `name`=? and `pass`=?",array($_POST['name'],$pass));
		if($check->recordcount() == 1){
			$_SESSION['uid'] = $u1['id'];
			$_SESSION['hash'] = sha1(md5($u1['id']).md5($_SERVER['REMOTE_ADDR']).md5($session_key));
			header("Location: index.php");
		}else{
			include("templates/guest_header.php");
			echo "please check your username and password!";
			include("templates/guest_footer.php");
		}
	}else{
		include("templates/guest_header.php");
		echo "please check your username and password!";
		include("templates/guest_footer.php");
	}
}