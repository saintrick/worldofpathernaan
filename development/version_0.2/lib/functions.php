<?php
function usr($secret, &$db){
	if (!isset($_SESSION['uid']) || !isset($_SESSION['hash']))
	{
		header("Location: login.php");
		exit;
	}else{
		$hash=sha1(md5($_SESSION['uid']).md5($_SERVER['REMOTE_ADDR']).md5($secret));
		if($_SESSION['hash'] != $hash){
			session_destroy();
			session_unset();
			header("location: login.php");
			exit;
		}else{
			$userinformation=$db->execute("select * from `users` where `id`=?",array($_SESSION['uid']));
			$ui=$userinformation->fetchrow();
			if($userinformation->recordcount() == 0){
				session_destroy();
				session_unset();
				header("location: login.php");
				exit;
			}
			foreach($ui as $key=>$value){
				$user->$key=$value;
			}
			return $user;
		}
	}
}

//get suffix and prefix
function userrank($id, &$db)
{
	$userrank=$db->GetOne("select `rank` from `users` where `name`=?",array($id));
	$group=$db->execute("select * from `groups` where `id`=?",array($userrank));
	$g=$group->fetchrow();
	$prefix=$g['prefix'];
	$suffix=$g['suffix'];
	return $prefix.$id.$suffix;
}

//settings variables
$settings11 = $db->execute("select * from `settings`");
while ($set = $settings11->fetchrow()){
	$settings->$set['name'] = $set['value'];
}

function getmailcount($playerid,&$db){
	$query=$db->execute("select * from `mail` where `to`=? and `status`=?",array($playerid,"unread"));
	$mailcount=$query->recordcount();
	return $mailcount;
}

function getlogcount($playerid,&$db){
	$query=$db->execute("select * from `logs` where `userid`=? and `status`=?",array($playerid,"unread"));
	$logcount=$query->recordcount();
	return $logcount;
}

function groupinfo($groupID,$field,&$db){
	$query=$db->GetOne("select `{$field}` from `groups` where `id`=?",array($groupID));
	return $query;
}

function addlog($uid,$msg,&$db){
	$insert['userid'] = $uid;
	$insert['time'] = time();
	$insert['message'] = $msg;
	$query=$db->AutoExecute("logs",$insert,"INSERT");
}
?>