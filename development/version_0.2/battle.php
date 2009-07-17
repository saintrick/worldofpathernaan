<?php
include("lib.php");
define("PAGENAME","Battle area");
$user=usr($session_key, $db);
switch($_GET['act'])
{
	case "attack":
		if (!$_GET['id']) //No name entered
		{
			header("Location: battle.php");
			break;
		}
		
		//Otherwise, get player data:
		$query = $db->execute("select * from `users` where `id`=?", array($_GET['id']));
		if ($query->recordcount() == 0) //Player doesn't exist
		{
			include("templates/member_header.php");
			echo "This player doesn't exist!";
			include("templates/member_footer.php");
			break;
		}
		
		$enemy1 = $query->fetchrow(); //Get player info
		foreach($enemy1 as $key=>$value)
		{
			$enemy->$key = $value;
		}
		
		//Otherwise, check if player has any health
		if ($enemy->hp <= 0)
		{
			include("templates/member_header.php");
			echo "This player is dead!";
			include("templates/member_footer.php");
			break;
		}
		
		//Player cannot attack anymore
		if ($user->eng <= 0)
		{
			include("templates/member_header.php");
			echo "You have no eng left! You must rest a while.";
			include("templates/member_footer.php");
			break;
		}
		
		//Player is dead
		if ($user->hp <= 0)
		{
			include("templates/member_header.php");
			echo "You are dead! Please visit the hospital or wait until you are revived!";
			include("templates/member_footer.php");
			break;
		}
		
		if ($enemy->name == $user->name)
		{
			include("templates/member_header.php");
			echo "You cannot attack yourself...";
			include("templates/member_footer.php");
			break;
		}
		
		//Get enemy's bonuses from equipment -- Coming Soon
		$enemy->atkbonus = 1;
		$enemy->defbonus = 1;

		
		//Get player's bonuses from equipment -- Coming Soon
		$user->atkbonus = 1;
		$user->defbonus = 1;
		
		//Calculate some variables that will be used
		$enemy->strdiff = (($enemy->attack - $user->attack) > 0)?($enemy->attack - $user->attack):0;
		$enemy->vitdiff = (($enemy->defense - $user->defense) > 0)?($enemy->defense - $user->defense):0;
		$enemy->agidiff = (($enemy->speed - $user->speed) > 0)?($enemy->speed - $user->speed):0;
		$user->strdiff = (($user->attack - $enemy->attack) > 0)?($user->attack - $enemy->attack):0;
		$user->vitdiff = (($user->defense - $enemy->defense) > 0)?($user->defense - $enemy->defense):0;
		$user->agidiff = (($user->speed - $enemy->speed) > 0)?($user->speed - $enemy->speed):0;
		$totalstr = $enemy->attack + $user->attack;
		$totalvit = $enemy->defense + $user->defense;
		$totalagi = $enemy->speed + $user->speed;
		
		//Calculate the damage to be dealt by each player (dependent on attack and defense)
		$enemy->maxdmg = (($enemy->attack * 2) + $enemy->atkbonus['effectiveness']) - ($user->defbonus['effectiveness']);
		$enemy->maxdmg = $enemy->maxdmg - intval($enemy->maxdmg * ($user->vitdiff / $totalvit));
		$enemy->maxdmg = ($enemy->maxdmg <= 2)?2:$enemy->maxdmg; //Set 2 as the minimum damage
		$enemy->mindmg = (($enemy->maxdmg - 4) < 1)?1:($enemy->maxdmg - 4); //Set a minimum damage range of maxdmg-4
		$user->maxdmg = (($user->attack * 2) + $user->atkbonus['effectiveness']) - ($enemy->defbonus['effectiveness']);
		$user->maxdmg = $user->maxdmg - intval($user->maxdmg * ($enemy->vitdiff / $totalvit));
		$user->maxdmg = ($user->maxdmg <= 2)?2:$user->maxdmg; //Set 2 as the minimum damage
		$user->mindmg = (($user->maxdmg - 4) < 1)?1:($user->maxdmg - 4); //Set a minimum damage range of maxdmg-4
		
		//Calculate battle 'combos' - how many times in a row a player can attack (dependent on speed)
		$enemy->combo = ceil($enemy->speed / $user->speed);
		$enemy->combo = ($enemy->combo > 3)?3:$enemy->combo;
		$user->combo = ceil($user->speed / $enemy->speed);
		$user->combo = ($user->combo > 3)?3:$user->combo;
		
		//Calculate the chance to miss opposing player
		$enemy->miss = intval(($user->agidiff / $totalagi) * 100);
		$enemy->miss = ($enemy->miss > 20)?20:$enemy->miss; //Maximum miss chance of 20% (possible to change in admin panel?)
		$enemy->miss = ($enemy->miss <= 5)?5:$enemy->miss; //Minimum miss chance of 5%
		$user->miss = intval(($enemy->agidiff / $totalagi) * 100);
		$user->miss = ($user->miss > 20)?20:$user->miss; //Maximum miss chance of 20%
		$user->miss = ($user->miss <= 5)?5:$user->miss; //Minimum miss chance of 5%
		
		
		$battlerounds = 30; //Maximum number of rounds/turns in the battle. Changed in admin panel?
		
		$output = ""; //Output message
		
		
		//While somebody is still alive, battle!
		while ($enemy->hp > 0 && $user->hp > 0 && $battlerounds > 0)
		{
			$attacking = ($user->speed >= $enemy->speed)?$user:$enemy;
			$defending = ($user->speed >= $enemy->speed)?$enemy:$user;
			
			for($i = 0;$i < $attacking->combo;$i++)
			{
				//Chance to miss?
				$misschance = intval(rand(0, 100));
				if ($misschance <= $attacking->miss)
				{
					$output .= $attacking->name . " tried to attack " . $defending->name . " but missed!<br />";
				}
				else
				{
					$damage = rand($attacking->mindmg, $attacking->maxdmg); //Calculate random damage				
					$defending->hp -= $damage;
					$output .= ($user->name == $defending->name)?"<font color=\"red\">":"<font color=\"green\">";
					$output .= $attacking->name . " attacks " . $defending->name . " for <b>" . $damage . "</b> damage! (";
					$output .= ($defending->hp > 0)?$defending->hp . " HP left":"Dead";
					$output .= ")<br />";
					$output .= "</font>";

					//Check if anybody is dead
					if ($defending->hp <= 0)
					{
						$user = ($user->speed >= $enemy->speed)?$attacking:$defending;
						$enemy = ($user->speed >= $enemy->speed)?$defending:$attacking;
						break 2; //Break out of the for and while loop, but not the switch structure
					}
				}
				$battlerounds--;
				if ($battlerounds <= 0)
				{
					break 2; //Break out of for and while loop, battle is over!
				}
			}
			
			for($i = 0;$i < $defending->combo;$i++)
			{
				//Chance to miss?
				$misschance = intval(rand(0, 100));
				if ($misschance <= $defending->miss)
				{
					$output .= $defending->name . " tried to attack " . $attacking->name . " but missed!<br />";
				}
				else
				{
					$damage = rand($defending->mindmg, $defending->maxdmg); //Calculate random damage
					$attacking->hp -= $damage;
					$output .= ($user->name == $defending->name)?"<font color=\"green\">":"<font color=\"red\">";
					$output .= $defending->name . " attacks " . $attacking->name . " for <b>" . $damage . "</b> damage! (";
					$output .= ($attacking->hp > 0)?$attacking->hp . " HP left":"Dead";
					$output .= ")<br />";
					$output .= "</font>";

					//Check if anybody is dead
					if ($attacking->hp <= 0)
					{
						$user = ($user->speed >= $enemy->speed)?$attacking:$defending;
						$enemy = ($user->speed >= $enemy->speed)?$defending:$attacking;
						break 2; //Break out of the for and while loop, but not the switch structure
					}
				}
				$battlerounds--;
				if ($battlerounds <= 0)
				{
					break 2; //Break out of for and while loop, battle is over!
				}
			}
			
			$user = ($user->speed >= $enemy->speed)?$attacking:$defending;
			$enemy = ($user->speed >= $enemy->speed)?$defending:$attacking;
		}
		
		if ($user->hp <= 0)
		{
			//Calculate losses
			$xploss1 = $user->level * 6;
			$xploss2 = (($user->level - $enemy->level) > 0)?($enemy->level - $user->level) * 4:0;
			$xploss = $xploss1 + $xploss2;
			$goldloss = intval(0.2 * $user->gold);
			$goldloss = intval(rand(1, $goldloss));
			
			$output .= "<br /><u>You were defeated by " . userrank($enemy->name,$db) . "!</u><br />";
			$output .= "<br />You lost <b>" . $xploss . "</b> xp and <b>" . $goldloss . "</b> gold.";
			$xploss3 = (($user->xp - $xploss) <= 0)?0:$xploss;
			$goldloss2 = (($user->gold - $goldloss) <= 0)?0:$goldloss;
			//Update player (the loser)
			$query = $db->execute("update `users` set `eng`=?, `xp`=?, `gold`=?, `deaths`=?, `hp`=0 where `id`=?", array($user->eng - 1, $user->xp - $xploss3, $user->gold - $goldloss2, $user->deaths + 1, $user->id));
			
			//Update enemy (the winner)
			if ($xploss + $enemy->xp < $enemy->mxp)
			{
				$query = $db->execute("update `users` set `xp`=?, `gold`=?, `kills`=?, `hp`=? where `id`=?", array($enemy->xp + $xploss, $enemy->gold + $goldloss, $enemy->kills + 1, $enemy->hp, $enemy->id));
				//Add log message for winner
				$logmsg = "You were attacked by <a href=\"profile.php?id=" . $user->id . "\">" . userrank($user->name,$db) . "</a> but you won!<br />\nYou gained " . $xploss . " xp and " . $goldloss . " gold.";
				addlog($enemy->id, $logmsg, $db);
			}
			else //Defender has gained a level! =)
			{
				$query = $db->execute("update `users` set `stat_points`=?, `level`=?, `mxp`=?, `xp`=?, `gold`=?, `kills`=?, `hp`=?, `mhp`=? where `id`=?", array($enemy->stat_points + 3, $enemy->level + 1, ($enemy->level+1) * 70 - 20, ($enemy->xp + $xploss) - $enemy->mxp, $enemy->gold + $goldloss, $enemy->kills + 1, $enemy->mhp + 30, $enemy->mhp + 30, $enemy->id));
				//Add log message for winner
				$logmsg = "You were attacked by <a href=\"profile.php?id=" . $user->id . "\">" . userrank($user->name,$db) . "</a> but you won!<br />\nYou gained a level and " . $goldloss . " gold.";
				addlog($enemy->id, $logmsg, $db);
			}
		}
		else if ($enemy->hp <= 0)
		{
			//Calculate losses
			$xpwin1 = $enemy->level * 6;
			$xpwin2 = (($user->level - $enemy->level) > 0)?$xpwin1 - (($user->level - $enemy->level) * 3):$xpwin1 + (($user->level - $enemy->level) * 3);
			$xpwin2 = ($xpwin2 <= 0)?1:$xpwin2;
			$xpwin3 = round(0.6 * $xpwin2);
			$xpwin = ceil(rand($xpwin3, $xpwin2));
			$goldwin = ceil(0.2 * $enemy->gold);
			$goldwin = intval(rand(1, $goldwin));
			$output .= "<br /><u>You defeated " . userrank($enemy->name,$db) . "!</u><br />";
			$output .= "<br />You won <b>" . $xpwin . "</b> xp and <b>" . $goldwin . "</b> gold.";
			
			if ($xpwin + $user->xp >= $user->mxp) //Player gained a level!
			{
				//Update player, gained a level
				$output .= "<br /><b>You leveled up!</b>";
				$newxp = $xpwin + $user->xp - $user->mxp;
				$query = $db->execute("update `users` set `stat_points`=?, `level`=?, `mxp`=?, `mhp`=?, `xp`=?, `gold`=?, `kills`=?, `hp`=?, `eng`=? where `id`=?", array($user->stat_points + 3, $user->level + 1, ($user->level+1) * 70 - 20, $user->mhp + 30, $newxp, $user->gold + $goldwin, $user->kills + 1, $user->mhp + 30, $user->eng - 1, $user->id));
			}
			else
			{
				//Update player
				$query = $db->execute("update `users` set `xp`=?, `gold`=?, `kills`=?, `hp`=?, `eng`=? where `id`=?", array($user->xp + $xpwin, $user->gold + $goldwin, $user->kills + 1, $user->hp, $user->eng - 1, $user->id));
			}
			
			//Add log message
			$logmsg = "You were attacked by <a href=\"profile.php?id=" . $user->id . "\">" . userrank($user->name,$db) . "</a> and you were defeated...";
			addlog($enemy->id, $logmsg, $db);
			//Update enemy (who was defeated)
			$query = $db->execute("update `users` set `hp`=0, `deaths`=? where `id`=?", array($enemy->deaths + 1, $enemy->id));
		}
		else
		{
			$output .= "<br /><u>Both of you were too tired to finish the battle! Nobody won...</u>";
			$query = $db->execute("update `users` set `hp`=?, `eng`=? where `id`=?", array($user->hp, $user->eng - 1, $user->id));
			$query = $db->execute("update `users` set `hp`=? where `id`=?", array($enemy->hp, $enemy->id));
			
			$logmsg = "You were attacked by <a href=\"profile.php?id=" . $user->id . "\">" . userrank($user->name,$db) . "</a> but nobody won...";
			addlog($enemy->id, $logmsg, $db);
		}
		
		$user=usr($session_key, $db); //Get new stats
		include("templates/member_header.php");
		echo $output;
		include("templates/member_footer.php");
		break;
	
	default:
		//you shouldn't get here..
		header("location: memberlist.php");
		break;
}
?>