<?php
include("lib.php");
define("PAGENAME","Register");
include("templates/guest_header.php");
if(!isset($_POST['submit'])){
	?>
	<form method="post">
	Username: <input type="text" name="name"><br/>
	<fieldset width="100%"><legend>Password</legend>
	Please enter a password for your account. PLEASE NOTE: passwords are CaSe SeNsAtIvE!<br/>
	<table width="100%">
	<tr><td width="50%">Password:</td><td width="50%">Repeat Password</td></tr>
	<tr><td><input type="password" name="pass"></td><td><input type="password" name="pass2"></td></tr>
	</table>
	</fieldset><br/>
	<fieldset width="100%"><legend>Email Address</legend>
	Please enter a valid password for your account.<br/>
	<table width="100%">
	<tr><td width="50%">Email:</td><td width="50%">Repeat Email</td></tr>
	<tr><td><input type="text" name="email"></td><td><input type="text" name="email2"></td></tr>
	</table>
	</fieldset><br/>
	<?php
	if($settings->captcha == "enabled"){
		?>
		<fieldset width="100%"><legend>CAPTCHA</legend>
		<table width="100%">
		<tr><td width="75%">Please enter the letters you see to the right.<br/><input type="text" name="captcha"></td><td><img src="modules/captcha/captcha.php" border="0"></td></tr>
		</table>
		</fieldset><br/>
		<?php
	}
	?>
	<input type="submit" name="submit" value="Register!">
	</form>
	<?php
}else{
	$check=$db->execute("select * from `users` where `name`=?",array($_POST['name']));
	if($_POST['name'] == "" or $_POST['pass'] == "" or $_POST['pass2'] == "" or $_POST['email'] == "" or $_POST['email2'] == "" ){
		echo "all fields are required!<br />";
	}elseif($_POST['email'] != $_POST['email2']){
		echo "your emails don't match!";
	}elseif($_POST['pass'] != $_POST['pass2']){
		echo "your passwords don't match!";
	}elseif($settings->captcha == "enabled" and $_POST['captcha'] != $_SESSION['captcha']){
		echo "you entered the captcha wrong";
	}elseif (!preg_match("/^[-_a-zA-Z0-9]+$/", $_POST['name'])){
		echo "you're username contains illegal characters";
	}elseif($check->recordcount() >= 1){
		echo "username is taken";
	}else{
		$salt=md5(rand(000000000,999999999));
		$pass=md5(md5($_POST['pass']).md5($salt));
		$insert['name'] = $_POST['name'];
		$insert['salt'] = $salt;
		$insert['pass'] = $pass;
		$insert['email'] = htmlentities($_POST['email'], ENT_QUOTES);
		$insert['regdate'] = time();
		$insert['last_active'] = -1;
		$insert['register_ip'] = $_SERVER['REMOTE_ADDR'];
		$insert['last_ip'] = $_SERVER['REMOTE_ADDR'];
		$insertdata=$db->AutoExecute("users",$insert,"INSERT");
		if(insertdata){
			echo "your account has been created, you may now login and play";
		}else{
			echo "sorry, there was an error creating your account. Please contact an administrator";
		}
	}
}
include("templates/guest_footer.php");
?>