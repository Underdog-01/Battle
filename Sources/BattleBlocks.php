<?php
/*
 * Battle was developed for SMF forums c/o SA, nend & Underdog
 * Copyright 2009, 2010, 2011, 2012, 2013, 2014  SA | nend | Underdog
 * Revamped and supported by -Underdog-
 * This software package is distributed under the terms of its Creative Commons - Attribution No Derivatives License (by-nd) 3.0
 * http://creativecommons.org/licenses/by-nd/3.0/
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/////////////////////////////////////////////////////STATS BLOCKS//////////////////////////////////////////////////////////////////


/////////////////////////////////////


//IMPORTANT USAGE PLEASE READ BELOW//


//TESTED ON SIMPLE PORTAL 2.2.3//////


//THESE CODES GO INTO A CUSTOM PHP///


//BLOCK YOU ALSO NEED TO UPLOAD//////


//THIS FILE TO SOURCES/BATTLE////////


//MAY ALSO WORK WITH OTHER PORTALS///


/////////////////////////////////////


/*here is some blocks for your portal


Examples follow you need to call this file to use


eg


global $sourcedir;


require_once($sourcedir . '/Battle/Battle_Blocks.php');


then follwed by the code you want to use





///////////////////////top member slayers//////////////////////////////////////////


    global $smcFunc, $context, $max_views, $settings, $txt, $modSettings, $scripturl;


	battle_get_Block('mem_slays', 'top_ms');


	foreach ($context['top_ms'] as $i => $file)


	{$context['top_ms'][$i]['percent'] = round(($file['mem_slays'] * 100) / $max_views);}





	echo'<table border="0" cellpadding="1" cellspacing="0" width="100%">';





	foreach ($context['top_ms'] as $ms) {


		echo '


							<tr>


								<td width="60%" valign="top"><a href="', $scripturl, '?action=profile;u=', $ms['id_member'], '">', $ms['real_name'], '</a></td>


								<td width="20%" align="left" valign="top">', $ms['mem_slays'] > 0 ? '<img src="' . $settings['images_url'] . '/bar_stats.png" width="' . $ms['percent'] . '" height="15" alt="" />' : '&nbsp;', '</td>


								<td width="20%" align="right" valign="top">', $ms['mem_slays'], '</td>


							</tr>';


	}





	echo '


						</table>';





/////////////////////// Strongest attackers bt attack ///////////////////////////////////


	global $smcFunc, $context, $max_views, $settings, $txt, $modSettings, $scripturl;


	battle_get_Block('atk', 'top_atk');


	foreach ($context['top_atk'] as $i => $file)


	{$context['top_atk'][$i]['percent'] = round(($file['atk'] * 100) / $max_views);}





	echo'<table border="0" cellpadding="1" cellspacing="0" width="100%">';


	foreach ($context['top_atk'] as $atk) {


		echo '


							<tr>


								<td width="60%" valign="top"><a href="', $scripturl, '?action=profile;u=', $atk['id_member'], '">', $atk['real_name'], '</a></td>


								<td width="20%" align="left" valign="top">', $atk['atk'] > 0 ? '<img src="' . $settings['images_url'] . '/bar_stats.png" width="' . $atk['percent'] . '" height="15" alt="" />' : '&nbsp;', '</td>


								<td width="20%" align="right" valign="top">', $atk['atk'], '</td>


							</tr>';


	}


	echo '


						</table>';





/////////////////////////// Richest attackers /////////////////////////////////////////////


global $smcFunc, $context, $max_views, $settings, $txt, $modSettings, $scripturl;


	battle_get_Block($modSettings['bcash'], 'top_gold');


	foreach ($context['top_gold'] as $i => $file)


	{$context['top_gold'][$i]['percent'] = round(($file[$modSettings['bcash']] * 100) / $max_views);}





	echo'<table border="0" cellpadding="1" cellspacing="0" width="100%">';


	foreach ($context['top_gold'] as $gold) {


		echo '


							<tr>


								<td width="60%" valign="top"><a href="', $scripturl, '?action=profile;u=', $gold['id_member'], '">', $gold['real_name'], '</a></td>


								<td width="20%" align="left" valign="top">', $gold[$modSettings['bcash']] > 0 ? '<img src="' . $settings['images_url'] . '/bar_stats.png" width="' . $gold['percent'] . '" height="15" alt="" />' : '&nbsp;', '</td>


								<td width="20%" align="right" valign="top">', $gold[$modSettings['bcash']], '</td>


							</tr>';


	}


	echo '


						</table>';





////////////////// Strongest attackers by defence//////////////////////////////


	global $smcFunc, $context, $max_views, $settings, $txt, $modSettings, $scripturl;


	battle_get_Block('def', 'top_def');


	foreach ($context['top_def'] as $i => $file)


	{$context['top_def'][$i]['percent'] = round(($file['def'] * 100) / $max_views);}





	echo'<table border="0" cellpadding="1" cellspacing="0" width="100%">';


	foreach ($context['top_def'] as $def) {


		echo '


							<tr>


								<td width="60%" valign="top"><a href="', $scripturl, '?action=profile;u=', $def['id_member'], '">', $def['real_name'], '</a></td>


								<td width="20%" align="left" valign="top">', $def['def'] > 0 ? '<img src="' . $settings['images_url'] . '/bar_stats.png" width="' . $def['percent'] . '" height="15" alt="" />' : '&nbsp;', '</td>


								<td width="20%" align="right" valign="top">', $def['def'], '</td>


							</tr>';


	}


	echo '


						</table>';





/////////////////////// top level batler/////////////////////////////////////////////////


	global $smcFunc, $context, $max_views, $settings, $txt, $modSettings, $scripturl;


	battle_get_Block('level', 'top_level');


	foreach ($context['top_level'] as $i => $file)


	{$context['top_level'][$i]['percent'] = round(($file['level'] * 100) / $max_views);}





	echo'	<table border="0" cellpadding="1" cellspacing="0" width="100%">';


	foreach ($context['top_level'] as $level) {


		echo '


							<tr>


								<td width="60%" valign="top"><a href="', $scripturl, '?action=profile;u=', $level['id_member'], '">', $level['real_name'], '</a></td>


								<td width="20%" align="left" valign="top">', $level['level'] > 0 ? '<img src="' . $settings['images_url'] . '/bar_stats.png" width="' . $level['percent'] . '" height="15" alt="" />' : '&nbsp;', '</td>


								<td width="20%" align="right" valign="top">', $level['level'], ' </td>


							</tr>';


	}


	echo '


						</table>';





/////////////////////////top monster slayer//////////////////////////////////////


	global $smcFunc, $context, $max_views, $settings, $txt, $modSettings, $scripturl;


	battle_get_Block('mon_slays', 'top_mos');


	foreach ($context['top_mos'] as $i => $file)


	{$context['top_mos'][$i]['percent'] = round(($file['mon_slays'] * 100) / $max_views);}





	echo'	<table border="0" cellpadding="1" cellspacing="0" width="100%">';


	foreach ($context['top_mos'] as $mos) {


		echo '


							<tr>


								<td width="60%" valign="top"><a href="', $scripturl, '?action=profile;u=', $mos['id_member'], '">', $mos['real_name'], '</a></td>


								<td width="20%" align="left" valign="top">', $mos['mon_slays'] > 0 ? '<img src="' . $settings['images_url'] . '/bar_stats.png" width="' . $mos['percent'] . '" height="15" alt="" />' : '&nbsp;', '</td>


								<td width="20%" align="right" valign="top">', $mos['mon_slays'], '</td>


							</tr>';


	}


	echo '


						</table>';





your see there is 6 different ones strongest attack, strongest defence, richest member, top level, top memslayer and top monster slayer*/





/*nothing here for you this function just gets the stats do not edit*/


function battle_get_Block($cname, $attribute) {





	global $smcFunc, $context, $txt, $max_views, $modSettings, $scripturl;





	$result =  $smcFunc['db_query']('', '


		SELECT {raw:cname}, real_name, id_member


		FROM {db_prefix}members


		WHERE {raw:cname} <> 0


		ORDER BY {raw:cname} DESC, real_name


		LIMIT 10',


		array(


			'cname' => $cname,


			)


		);


	$context[$attribute] = array();


	$max_views = 1;


	while ($row = $smcFunc['db_fetch_assoc']($result)) {


		$context[$attribute][] = array(


			    'id_member' => $row['id_member'],


				'real_name' => $row['real_name'],


				$cname => $row[$cname]


		);





		if ($max_views < $row[$cname])


			$max_views = $row[$cname];


	}


	$smcFunc['db_free_result']($result);


}


/////////////////////////////////////////////////////STATS BLOCKS END//////////////////////////////////////////////////////////////////





/////////////////////////////////////////////////////RANDOM MOSTER BLOCK//////////////////////////////////////////////////////////////////


/////////////////////////////////////


//IMPORTANT USAGE PLEASE READ BELOW//


//TESTED ON SIMPLE PORTAL 2.2.3//////


//THESE CODES GO INTO A CUSTOM PHP///


//BLOCK YOU ALSO NEED TO UPLOAD//////


//THIS FILE TO SOURCES/BATTLE////////


//MAY ALSO WORK WITH OTHER PORTALS///


/////////////////////////////////////


/*here is some blocks for your portal


Examples follow you need to call this file to use


eg


global $sourcedir;


require_once($sourcedir . '/Battle/Battle_Blocks.php');


followed by


random_monster(); */





/*nothing here for you this function just gets the monster do not edit*/


function random_monster()


{


   global $scripturl, $settings, $txt, $smcFunc;





	$request = $smcFunc['db_query']('', "


		SELECT


			id_monster, atk, def, hp, name, img, max_hp


		FROM {db_prefix}battle_monsters


		ORDER BY RAND()


		");





	$random = array();


	$row = $smcFunc['db_fetch_assoc']($request);


	censorText($row['name']);





	$random[] = array(


		'name' => '',


	 );


	$smcFunc['db_free_result']($request);





	foreach ($random as $monster)


		echo'


		<div align="center"><img border="0" src="',$settings['images_url'],'/battle/monsters/'.$row['img'].'" alt="'.$row['name'].'">


		<br /> '.$row['name'].'<br /><a href="'. $scripturl. '?action=battle;sa=fm;mon='.$row['id_monster'].'"><strong>'.$txt['battle_atk'].'</strong></a></div>';


}


/////////////////////////////////////////////////////RANDOM MOSTER BLOCK END//////////////////////////////////////////////////////////////////





/////////////////////////////////////////////////////RANDOM QUEST BLOCK//////////////////////////////////////////////////////////////////////


/////////////////////////////////////


//IMPORTANT USAGE PLEASE READ BELOW//


//TESTED ON SIMPLE PORTAL 2.2.3//////


//THESE CODES GO INTO A CUSTOM PHP///


//BLOCK YOU ALSO NEED TO UPLOAD//////


//THIS FILE TO SOURCES/BATTLE////////


//MAY ALSO WORK WITH OTHER PORTALS///


/////////////////////////////////////


/*here is some blocks for your portal


Examples follow you need to call this file to use


eg


global $sourcedir;


require_once($sourcedir . '/Battle/Battle_Blocks.php');


followed by


random_quest(); */





/*nothing here for you this function just gets the quest do not edit*/


function random_quest()


{


   global $scripturl, $settings, $txt, $smcFunc, $user_info;





	$request = $smcFunc['db_query']('', "


		SELECT  id_quest, gold, itext, stext, ftext, stext, exp,


		level, success, name, plays, energy


		FROM {db_prefix}battle_quest


		ORDER BY RAND()


		");





	$random = array();


	$row = $smcFunc['db_fetch_assoc']($request);


	censorText($row['name']);





	$random[] = array(


		'name' => '',


	 );


	$smcFunc['db_free_result']($request);





	foreach ($random as $monster)


		echo'


		<div align="center"><strong>'.$row['name'].'</strong>


		<br /> Level Needed '.$row['level'].'


		<br /> Energy Needed '.$row['energy'].'


		<br /> Plays '.$row['plays'].'


		<br />';


		if ($user_info['level'] >= $row['level']) {


						echo'


					<a href="', $scripturl, '?action=battle;sa=quest;do=', $row['id_quest'],'">'.$txt['battle_quest_do'].'</a>';


					} else {


						echo'


					'.$txt['battle_quest_lvl'] .' '.$row['level'].' '.$txt['battle_quest_rq'].'';


					}


		echo'</div>';


}


/////////////////////////////////////////////////////RANDOM QUEST BLOCK END//////////////////////////////////////////////////////////////////





/////////////////////////////////////////////////////USERS IN BATTLE BLOCK///////////////////////////////////////////////////////////////////





/////////////////////////////////////


//IMPORTANT USAGE PLEASE READ BELOW//


//TESTED ON SIMPLE PORTAL 2.2.3//////


//THESE CODES GO INTO A CUSTOM PHP///


//BLOCK YOU ALSO NEED TO UPLOAD//////


//THIS FILE TO SOURCES/BATTLE////////


//MAY ALSO WORK WITH OTHER PORTALS///


/////////////////////////////////////


/*here is some blocks for your portal


Examples follow you need to call this file to use


eg


global $sourcedir;


require_once($sourcedir . '/Battle/Battle_Blocks.php');


followed by


battle_whos_online_block(); */





/*only edit if you know what you are doing*/


function battle_whos_online_block(){





   global $txt, $context,$settings, $smcFunc;


$online = battle_whos_online_block_func('array');


$b_users = array();


foreach($online['users'] as $user)


{


   $result = $smcFunc['db_query']('', '


      SELECT url


      FROM {db_prefix}log_online


      WHERE id_member = {int:name}',


		array(


			'name' => $user['id'],


			)


		);





   $checkit = $smcFunc['db_fetch_assoc']($result);


   $data = @unserialize($checkit['url']);





        //make sure we are in battle and not forum or any were else!!!!


        if(isset($data['action']) && $data['action'] == 'battle'){





        //index


		if (!isset($data['sa']) || $data['sa'] == 'main'){


		      echo '<img border="0" src="',$settings['images_url'],'/battle/door.png" alt="'.$txt['Battle_who_home'].'" title="'.$txt['Battle_who_home'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//exploring


		elseif ($data['sa'] == 'explore'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/world.png" alt="'.$txt['Battle_who_explore'].'" title="'.$txt['Battle_who_explore'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





	    //shop


		elseif ($data['sa'] == 'shop'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/cart.png" alt="'.$txt['Battle_who_shop'].'"  title="'.$txt['Battle_who_shop'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//battle list


		elseif ($data['sa'] == 'battle'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/bomb.png" alt="'.$txt['Battle_who_battle'] .'" title="'.$txt['Battle_who_battle'] .'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//quest


		elseif ($data['sa'] == 'quest'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/book.png" alt="'.$txt['Battle_who_quest'].'" title="'.$txt['Battle_who_quest'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//upgrade


		elseif ($data['sa'] == 'upgrade'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/bug.png" alt="'.$txt['Battle_who_upgrade'] .'" title="'.$txt['Battle_who_upgrade'] .'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//graveyard


		elseif ($data['sa'] == 'gy'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/box.png" alt="'.$txt['Battle_who_graveyard'].'" title="'.$txt['Battle_who_graveyard'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//howto


		elseif ($data['sa'] == 'howto'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/help.png" alt="'.$txt['Battle_who_howto'].'" title="'.$txt['Battle_who_howto'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//monster list


		elseif ($data['sa'] == 'monsters'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/bug.png" alt="'.$txt['Battle_who_monster'].'" title="'.$txt['Battle_who_monster'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//stats


		elseif ($data['sa'] == 'stats'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/chart_pie.png" alt="'.$txt['Battle_who_stats'].'" title="'.$txt['Battle_who_stats'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//settings


		elseif ($data['sa'] == 'settings'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/bullet_wrench.png" alt="'.$txt['Battle_who_settings'].'" title="'.$txt['Battle_who_settings'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//searching


		elseif ($data['sa'] == 'search'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/star.png" alt="'.$txt['Battle_who_search'].'" title="'.$txt['Battle_who_search'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//mon fight


		elseif ($data['sa'] == 'fm'){


			  echo '<img border="0" src="',$settings['images_url'],'/battle/shield.png" alt="'.$txt['Battle_who_fm'].'" title="'.$txt['Battle_who_fm'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


              $b_users[] = $user;}





		//mem fight


		elseif ($data['sa'] == 'fight'){


			 echo '<img border="0" src="',$settings['images_url'],'/battle/shield.png" alt="'.$txt['Battle_who_fight'].'" title="'.$txt['Battle_who_fight'].'" /> ' , $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


             $b_users[] = $user;}





		// Something else index maybe?


	    else{


			echo '<img border="0" src="',$settings['images_url'],'/battle/door.png" alt="'.$txt['Battle_who_home'].'" title="'.$txt['Battle_who_home'].'" /> ', $user['hidden'] ? '<i>' . $user['link'] . '</i>' : $user['link'];  echo '<br />';


            $b_users[] = $user;}


		}


 //tidyup!!!!!!


   unset($data);


}


// some times this is slow so just show searching for members hoping there refresh at some point :P


if(empty($b_users))


   echo $txt['battle_online_search1'];


}


/*nothing here for you this function just gets the Members to display in battle do not edit*/


function battle_whos_online_block_func($func_method = 'echo')


{


global $sourcedir;


	require_once($sourcedir . '/Subs-MembersOnline.php');


	$members = array(


		'show_hidden' => allowedTo('moderate_forum'),


		'sort' => 'log_time',


		'reverse_sort' => true,


	);


	$get = getMembersOnlineStats($members);


	if ($func_method != 'echo')


		return $get + array('users' => $get['users_online']);


}


/////////////////////////////////////////////////////USERS IN BATTLE BLOCK END//////////////////////////////////////////////////////////////////


?>