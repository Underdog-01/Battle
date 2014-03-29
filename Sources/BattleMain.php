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

function battle_battle($per_page = 7)
{
	global $context, $txt, $scripturl, $smcFunc, $user_info, $modSettings;

	if (empty($modSettings['battle_enable_membattle']))
		fatal_error($txt['battle_error9'], false);

	$context['atk'] = array();
	$context['sub_template']  = 'battle_battle';
	$context['page_title'] = $txt['battle'];
	$context['battle_display'] = array('page' => false, 'pages' => '0');
	$context['battle_checkSession'] = 9 * ((!empty($user_info['hp']) ? $user_info['hp'] : 9) + (!empty($user_info['energy']) ? $user_info['energy'] : 9) + (!empty($user_info['stamina']) ? $user_info['stamina'] : 9) + (!empty($user_info['exp']) ? $user_info['exp'] : 9));
	$queryUser = !empty($_REQUEST['u']) ? ' AND j.id_member = ' . (int)$_REQUEST['u'] : '';
	$page = $context['current_page'] * $per_page;
	$context['battle_mem_sort'] = !empty($context['battle_mem_sort']) ? $context['battle_mem_sort'] : 'hp';
	$context['battle_mem_order'] = !empty($context['battle_mem_order']) ? $context['battle_mem_order'] : 'DESC';
	$order = !empty($_REQUEST['order']) ? $_REQUEST['order'] : '';
	$sort = !empty($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
	$context['battle_mem_order'] = $order === 'ASC' ? 'ASC' : $context['battle_mem_order'];
	$context['battle_mem_sort'] = ($sort === 'real_name' || $sort === 'id_member' || $sort === 'atk' || $sort === 'def' || $sort === 'hp') ? $sort : $context['battle_mem_sort'];

	$request = $smcFunc['db_query']('', '
		SELECT COUNT(j.id_member) FROM {db_prefix}members AS j
		WHERE j.id_member <> {int:userid} AND (j.hp > 0 AND j.atk > 0)' . $queryUser . '
		ORDER BY ' . $context['battle_mem_sort'] . ' ' . $context['battle_mem_order'],
		array('userid' => $context['user']['id']));

	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	$nRows = array_shift(array_values($row));

	$battleQ = $smcFunc['db_query']('', '
		SELECT j.id_member, j.real_name, j.hp, j.atk, j.def, j.member_name,
		j.avatar, IFNULL(a.id_attach, 0) AS id_attach, a.filename, a.attachment_type, mg.online_color
		FROM {db_prefix}members AS j
		LEFT JOIN {db_prefix}attachments AS a ON (a.id_member = j.id_member)
		LEFT JOIN {db_prefix}membergroups AS mg ON (mg.id_group = CASE WHEN j.id_group = {int:reg_mem_group} THEN j.id_post_group ELSE j.id_group END)
		WHERE j.id_member <> {int:userid} AND (j.hp > {int:hp} AND j.atk > {int:atk})' . $queryUser . '
		ORDER BY ' . $context['battle_mem_sort'] . ' ' . $context['battle_mem_order'] . '
		LIMIT {int:page},{int:per}',
			array(
				'hp' => 0,
				'atk' => 0,
				'userid' => $context['user']['id'],
				'def' => 0,
				'reg_mem_group' => 0,
				'level'=> $user_info['level'],
				'page' => $page,
				'per' => $per_page,
			)
	);

	while ($row = $smcFunc['db_fetch_assoc']($battleQ))
	{
		$name = !empty($row['member_name']) ? $row['member_name'] : $txt['battle_mem'] . '_' . $row['id_member'];
		// And add them to the list
		$context['atk'][] = array(
			'id_member' => $row['id_member'],
			'real_name' => !empty($row['real_name']) ? $row['real_name'] : $name,
			'atk' => $row['atk'],
			'def' => $row['def'],
			'hp' => $row['hp'],
			'id_attach' => $row['id_attach'],
			'filename' => $row['filename'],
			'attachment_type' => $row['attachment_type'],
			'avatar' => $row['avatar'],
			'online_color' => $row['online_color'],
		);
	}
	$smcFunc['db_free_result']($battleQ);
	$context['current_pages'] = (($nRows-1) / $per_page) + 1;
	$context['battle_display'] = battle_pages($txt['battle_page'], '#battle_main', $scripturl . '?action=battle;sa=battle;sort=' . $context['battle_mem_sort']. ';order=' . $context['battle_mem_order'] . ';home', $context['current_pages']);

}

function battle_explore()
{
	global  $context, $txt, $user_info, $modSettings, $map, $settings, $scripturl;
	$context['sub_template']  = 'battle_explore';
	$context['page_title'] = $txt['battle_expl'];

	// use the same map cache for an hour max (this will be expanded to a completion of the map game in the future)
	$map = cache_get_data('battle_map_' . $user_info['id'], 3600);
	if (empty($map))
	{
		if (!$dir = @opendir($settings['theme_dir'].'/images/battle/'))
			$dir = @opendir($settings['default_theme_dir'].'/images/battle/');

		while ($file = readdir($dir))
		{
			if(substr($file, 0, 5) == "tile_" )
				$terrain[] = $file;
		}
		closedir($dir);
		$map = array();

		//tiles down
		for ($row = 0; $row < $modSettings['battle_map_down']; $row++)
		{
			$map[] = array();
			//tiles across
			for ($column = 0; $column < $modSettings['battle_map_across']; $column++)
			{
				$pool = $terrain;
				if (isset($map[$row-1]))
				{
					if (isset($map[$row-1][$column-1]))
					{
						$pool[] = $map[$row-1][$column-1];
						$pool[] = $map[$row-1][$column-1];
					}
					$pool[] = $map[$row-1][$column];
					$pool[] = $map[$row-1][$column];
					if (isset($map[$row-1][$column+1]))
					{
						$pool[] = $map[$row-1][$column+1];
						$pool[] = $map[$row-1][$column+1];
					}
				}
				if (isset($map[$row][$column-1]))
				{
					$pool[] = $map[$row][$column-1];
					$pool[] = $map[$row][$column-1];
				}
				shuffle($pool);
				$map[$row][$column] = $pool[0];
			}
		}

		cache_put_data('battle_map_' . $user_info['id'], $map, 3600);
	}
}

function battle_stat_upgrade()
{
    global $txt, $context, $smcFunc, $modSettings, $user_info;

    //We Need Our Template
    $context['sub_template']  = 'battle_stat_up';

    //Page title is good
    $context['page_title'] = $txt['battle_upgrade_ce'];

    battle_stat_do_upgrade('max_atk','1','1','max_atk');
    battle_stat_do_upgrade('max_atk5','5','5','max_atk');
    battle_stat_do_upgrade('max_atk50','50','50','max_atk');
    battle_stat_do_upgrade('max_atk100','100','100','max_atk');
    battle_stat_do_upgrade('max_def','1','1','max_def');
    battle_stat_do_upgrade('max_def5','5','5','max_def');
    battle_stat_do_upgrade('max_def50','50','50','max_def');
    battle_stat_do_upgrade('max_def100','100','100','max_def');
    battle_stat_do_upgrade('max_energy','1','1','max_energy');
    battle_stat_do_upgrade('max_energy5','5','5','max_energy');
    battle_stat_do_upgrade('max_energy50','50','50','max_energy');
    battle_stat_do_upgrade('max_energy100','100','100','max_energy');
    battle_stat_do_upgrade('max_stamina','1','1','max_stamina');
    battle_stat_do_upgrade('max_stamina5','5','5','max_stamina');
    battle_stat_do_upgrade('max_stamina50','50','50','max_stamina');
    battle_stat_do_upgrade('max_stamina100','100','100','max_stamina');
    battle_stat_do_upgrade('max_hp','1','1','max_hp');
    battle_stat_do_upgrade('max_hp5','5','5','max_hp');
    battle_stat_do_upgrade('max_hp50','50','50','max_hp');
    battle_stat_do_upgrade('max_hp100','100','100','max_hp');
}

function battle_shop($per_page = 10)
{
    global  $smcFunc, $txt, $scripturl, $modSettings, $user_info, $context;

    $context['sub_template']  = 'battle_shop';
    $context['page_title'] = $txt['battle_shop2'];
    $context['battle_display'] = array('page' => false, 'pages' => '0');
    $context['shop'] = array();

    $battleQ = $smcFunc['db_query']('', '
		SELECT id_item, name, price, action, img, description, amount
		FROM {db_prefix}battle_shop'
	    );

    // And add them to the list
    while ($row = $smcFunc['db_fetch_assoc']($battleQ))
	$context['battle_shop'][] = array(
		'id_item' => $row['id_item'],
		'name' => $row['name'],
		'price' => $row['price'],
		'action' => $row['action'],
		'img' => $row['img'],
		'amount' => $row['amount'],
		'description' => $row['description'],
	);

    $smcFunc['db_free_result']($battleQ);

    $context['shop'] = battle_pagination($context['battle_shop'], $per_page);
    $context['battle_display'] = battle_pages($txt['battle_page'], '#battle_main', $scripturl . '?action=battle;sa=shop;home', $context['current_pages']);

    battle_shop_do_buy();
}

function battle_hosp()
{
	global  $smcFunc, $txt, $user_info, $modSettings, $context;

	$context['sub_template']  = 'battle_hosp';
	$context['page_title'] = $txt['battle_hosp'];
	$hp = $user_info['max_hp'] *  pow(1.00, $user_info['max_hp']);
	$price =  $user_info[$modSettings['bcash']] - $modSettings['battle_how_much_reviv_user'];

	if ($user_info[$modSettings['bcash']] >= $modSettings['battle_how_much_reviv_user'])
	{
		//Check to see if they have enough cash.
		$request = $smcFunc['db_query']('','
				SELECT real_name
				FROM {db_prefix}members
				WHERE id_member = {int:attack}
				LIMIT 1',
					array('attack' => $_GET['heal2'],)
			);

		list ($name) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		if($modSettings['enable_battle_hist'])
		{
			$content = $user_info['name'].' '.$txt['battle_hist22'].' '.$name;
			add_to_battle_hist($content);
		}

		updateMemberData($_GET['heal2'], array('hp' => $modSettings['battle_how_much_hp'], 'is_dead' => 0));
		updateMemberData($user_info['id'], array($modSettings['bcash'] => $price ));

		//Update cash for display before the template gets phased, since we changed it.
		$user_info[$modSettings['bcash']] = $price;
		$smcFunc['db_query']('','
			DELETE FROM {db_prefix}battle_graveyard
			WHERE id_memdef = {int:id}',
				array('id' => $_GET['heal2'],)
		);

		redirectexit('action=battle;sa=gy;done;#battle_main');

	}
	else
		fatal_error($txt['battle_game_cnhu'], false);
}

function battle_graveyard($per_page = 8)
{
	global $smcFunc,  $txt, $context, $modSettings, $need_money, $name, $scripturl, $user_info;

	//We Need Our Template
	$context['sub_template']  = 'battle_grave';

	//Page title is good
	$context['page_title'] = $txt['battle_gy'];
	$context['battle_grave'] = array();
	$context['battle_display'] = array('page' => false, 'pages' => '0');
	$page = $context['current_page'] * $per_page;

	$request = $smcFunc['db_query']('', '
		SELECT COUNT(id_grave) FROM {db_prefix}battle_graveyard
		WHERE id_grave');

	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	$nRows = array_shift(array_values($row));

	$request = $smcFunc['db_query']('', '
		SELECT g.id_grave, g.id_mem, g.name, g.id_memdef, m.real_name, m.buddy_list, m.level, g.date, mg.online_color
			FROM {db_prefix}battle_graveyard AS g
			LEFT JOIN {db_prefix}members AS m  ON (g.id_memdef = m.id_member)
			LEFT JOIN {db_prefix}membergroups AS mg ON (mg.id_group = CASE WHEN m.id_group = {int:reg_mem_group} THEN m.id_post_group ELSE m.id_group END)
			ORDER BY g.id_grave DESC
			LIMIT {int:page},{int:per}',
			array(
				'reg_mem_group' => 0,
				'page' => $page,
				'per' => $per_page,
			)
		);

	// Loop through all results
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$context['battle_grave'][] = array(
			'real_name' => $row['real_name'],
			'name' => $row['name'],
			'date' => $row['date'],
			'id_mem' => $row['id_mem'],
			'id_memdef' => $row['id_memdef'],
			'online_color' => $row['online_color'],
			'level' => $row['level'],
		);
	}

	$smcFunc['db_free_result']($request);

	// $context['battle_grave'] = battle_pagination($context['battle_graveyard'], 8);
	$context['current_pages'] = (($nRows-1) / $per_page) + 1;
	$context['battle_display'] = battle_pages($txt['battle_page'], '#battle_main', $scripturl . '?action=battle;sa=gy;home;', $context['current_pages']);
}

function battle_stats()
{
	global $smcFunc, $context, $max_views, $txt, $modSettings, $scripturl, $sourcedir;

	//We Need Our Template
	$context['sub_template']  = 'battle_stats';
	$context['page_title'] = $txt['battle_game_stats'];
	$context['battle_winner'] = array();
	$perc = 0;
	$first = 0;
	require_once($sourcedir . '/Subs-Members.php');

	//top member slayers
	battle_get_stats('mem_slays', 'top_ms');
	foreach ($context['top_ms'] as $i => $file)
		$context['top_ms'][$i]['percent'] = round(($file['mem_slays'] * 100) / $max_views);

	 // Strongest attackers by attack
	battle_get_stats('max_atk', 'top_atk');
	foreach ($context['top_atk'] as $i => $file)
		$context['top_atk'][$i]['percent'] = round(($file['max_atk'] * 100) / $max_views);

	// Richest attackers
	battle_get_stats($modSettings['bcash'], 'top_gold');
	foreach ($context['top_gold'] as $i => $file)
		$context['top_gold'][$i]['percent'] = round(($file[$modSettings['bcash']] * 100) / $max_views);

	// Strongest attackers by defence
	battle_get_stats('max_def', 'top_def');
	foreach ($context['top_def'] as $i => $file)
		$context['top_def'][$i]['percent'] = round(($file['max_def'] * 100) / $max_views);

	// top level battler
	battle_get_stats('level', 'top_level');
	foreach ($context['top_level'] as $i => $file)
		$context['top_level'][$i]['percent'] = round(($file['level'] * 100) / $max_views);

	// top level battler
	battle_get_stats('mon_slays', 'top_mos');
	foreach ($context['top_mos'] as $i => $file)
		$context['top_mos'][$i]['percent'] = round(($file['mon_slays'] * 100) / $max_views);

	// top point getters
	battle_get_stats('battle_points', 'top_points');
	foreach ($context['top_points'] as $i => $file)
	{
		$score = (!empty($modSettings['battle_combine_pts']) ? battle_campaign_score($file['id_member']) + $file['battle_points'] : $file['battle_points']);
		$context['top_points'][$i]['total'] = $score;

	}

	foreach ($context['top_points'] as $keyx => $valuex)
	{
		$scorex[$keyx] = $valuex['total'];
		$namex[$keyx] = $valuex['real_name'];
	}

	if (!empty($context['top_points']))
		array_multisort($scorex, SORT_DESC, $namex, SORT_ASC, $context['top_points']);


	foreach ($context['top_points'] as $i => $file)
	{
		if (empty($first))
		{
			$first = (int)$context['top_points'][$i]['total'];
			$context['top_points'][$i]['percent'] = 100;
		}
		elseif ($first != (int)$context['top_points'][$i]['total'])
			$context['top_points'][$i]['percent'] = round(($context['top_points'][$i]['total'] / $first) * 100) > 1 ? round(($context['top_points'][$i]['total'] / $first) * 100) : 1;
		else
			$context['top_points'][$i]['percent'] = 100;

	}

	if (!empty($modSettings['battle_points']))
	{
		$checkThisLvl['level'] = false;
		$request = $smcFunc['db_query']('','
			SELECT level, battle_points, id_member, real_name, member_name, mon_slays,
				mem_slays, max_atk, max_def, max_exp, max_stamina, max_energy, max_hp
			FROM {db_prefix}members
			WHERE level >= {int:end_level}
			ORDER BY battle_points DESC
			LIMIT 1',
			array('end_level' => $modSettings['battle_points'])
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$context['battle_winner'] = array(
				'id' => $row['id_member'],
				'name' => !empty($row['real_name']) ? $row['real_name'] : $row['member_name'],
				'points' => $row['battle_points'],
				'camp_points' => battle_campaign_score($row['id_member']),
				'exp' => $row['max_exp'],
				'atk' => $row['max_atk'],
				'def' => $row['max_def'],
				'stamina' => $row['max_stamina'],
				'energy' => $row['max_energy'],
				'mon_slays' => $row['mon_slays'],
				'mem_slays' => $row['mem_slays'],
				'level' => $row['level'],
				'hp' => $row['max_hp']
			);
		}
		$smcFunc['db_free_result']($request);


	}
}

function battle_monsters($per_page = 10)
{
    global $smcFunc, $scripturl, $txt, $request, $context;

    $context['sub_template'] = 'battle_monsters';
    $context['page_title'] = $txt['battle_monsters'];
    $context['battle_display'] = array('page' => false, 'pages' => '0');
    $output = array();

    // Get the data.
    $request = $smcFunc['db_query']('', "
		SELECT id_monster, atk, def, hp, name, img, max_hp, mon_range, mon_max_range, evolve, counter
		FROM {db_prefix}battle_monsters
		ORDER BY atk DESC"
		);

    while ($row = $smcFunc['db_fetch_assoc']($request))
    {
	$output[] = array(
	    'id_monster' => $row['id_monster'],
	    'name' => $row['name'],
	    'atk' => $row['atk'],
	    'def' => $row['def'],
	    'hp' => $row['hp'],
	    'img' => $row['img'],
	    'max_hp' => $row['max_hp'],
	    'mon_range' => !empty($row['mon_range']) ? (int)$row['mon_range'] : 1,
	    'mon_max_range' => !empty($row['mon_max_range']) ? (int)$row['mon_max_range'] : ((!empty($row['mon_range']) ? (int)$row['mon_range'] : 1) + 1),
	    'evolve' => !empty($row['evolve']) ? (int)$row['evolve'] : 0,
	    'counter' => !empty($row['counter']) ? (int)$row['counter'] : 0,
	    );
    }

    $smcFunc['db_free_result']($request);

    $context['get_monsters'] = battle_pagination($output, $per_page);
    $context['battle_display'] = battle_pages($txt['battle_page'], '#battle_main', $scripturl . '?action=battle;sa=monsters', $context['current_pages']);

}

function battle_whos_online()
{
	global $txt, $context, $settings, $smcFunc, $user_info;

	// only check when 10 seconds has elapsed
	$who = !empty($_SESSION['battle_whos_online_' . $user_info['id']]['data']) ? $_SESSION['battle_whos_online_' . $user_info['id']] : array();

	if (!empty($who) && time() - $who['time'] < 10)
		$online = $who['data'];
	else
	{
		$online = battle_whos_online_func('array');
		$_SESSION['battle_whos_online_' . $user_info['id']] = array('time' => time(), 'data' => $online);
	}

	$b_users = array();
	$context['battle_whos_online'] = false;
	$actions = array(
			array(
			 'action' => 'main',
			 'img' => 'battle.png',
			 'text' => $txt['Battle_who_home'],
			),
			array(
			 'action' => 'explore',
			 'img' => 'world.png',
			 'text' => $txt['Battle_who_explore'],
			),
			array(
			 'action' => 'shop',
			 'img' => 'cart.png',
			 'text' => $txt['Battle_who_shop'],
			),
			array(
			 'action' => 'battle',
			 'img' => 'bomb.png',
			 'text' => $txt['Battle_who_battle'],
			),
			array(
			 'action' => 'quest',
			 'img' => 'book.png',
			 'text' => $txt['Battle_who_quest'],
			),
			array(
			 'action' => 'upgrade',
			 'img' => 'bug.png',
			 'text' => $txt['Battle_who_upgrade'],
			),
			array(
			 'action' => 'gy',
			 'img' => 'box.png',
			 'text' => $txt['Battle_who_graveyard'],
			),
			array(
			 'action' => 'howto',
			 'img' => 'help.png',
			 'text' => $txt['Battle_who_howto'],
			),
			array(
			 'action' => 'monsters',
			 'img' => 'bug.png',
			 'text' => $txt['Battle_who_monster'],
			),
			array(
			 'action' => 'stats',
			 'img' => 'chart_pie.png',
			 'text' => $txt['Battle_who_stats'],
			),
			array(
			 'action' => 'settings',
			 'img' => 'bullet_wrench.png',
			 'text' => $txt['Battle_who_settings'],
			),
			array(
			 'action' => 'search',
			 'img' => 'star.png',
			 'text' => $txt['Battle_who_search'],
			),
			array(
			 'action' => 'fm',
			 'img' => 'shield.png',
			 'text' => $txt['Battle_who_fm'],
			),
			array(
			 'action' => 'fight',
			 'img' => 'shield.png',
			 'text' => $txt['Battle_who_fight'],
			),
			array(
			 'action' => 'leaders',
			 'img' => 'medal.png',
			 'text' => $txt['Battle_who_leaders'],
			),
			array(
			 'action' => 'cleaders',
			 'img' => 'cmedal.png',
			 'text' => $txt['Battle_who_cleaders'],
			),
			array(
			 'action' => 'leaderboard',
			 'img' => 'bcrosette.png',
			 'text' => $txt['Battle_who_overall'],
			),
		);

	foreach($online['users'] as $user)
	{
		$result = $smcFunc['db_query']('', '
			SELECT url
			FROM {db_prefix}log_online
			WHERE id_member = {int:name}',
			array('name' => $user['id'],)
		);

		$checkit = $smcFunc['db_fetch_assoc']($result);
		$data = @unserialize($checkit['url']);


		// make sure we are in battle and not the forum or anywhere else!!!!
		if((!empty($data['action'])) && $data['action'] === 'battle')
		{
			$b_users[] = $user;
			if (!isset($data['sa']))
				$context['battle_whos_online'] .= '<img style="position:relative;vertical-align:bottom;" border="0" src="' . $settings['images_url'] . '/battle/door.png" alt="' . $txt['Battle_who_home'] . '" title="' . $txt['Battle_who_home'] . '" />&nbsp;' . ($user['hidden'] ? '<i>' . $user['link'] . '</i>,&nbsp;&nbsp;' : $user['link'] . ',&nbsp;&nbsp;');
			else
			{
				foreach ($actions as $action)
				{
					if ($data['sa'] == $action['action'])
						$context['battle_whos_online'] .= '<img style="position:relative;vertical-align:bottom;" border="0" src="' . $settings['images_url'] . '/battle/' . $action['img'] . '" alt="' . $action['text'] . '" title="' . $action['text'] . '" />&nbsp;' . ($user['hidden'] ? '<i>' . $user['link'] . '</i>,&nbsp;&nbsp;' : $user['link'] . ',&nbsp;&nbsp;');
				}

			}

			if (!$context['battle_whos_online'])
				$context['battle_whos_online'] .= '<img style="position:relative;vertical-align:bottom;" border="0" src="' . $settings['images_url'] . '/battle/door.png" alt="' . $txt['Battle_who_home'] . '" title="' . $txt['Battle_who_home'] . '" />&nbsp;' . ($user['hidden'] ? '<i>' . $user['link'] . '</i>,&nbsp;&nbsp;' : $user['link'] . ',&nbsp;&nbsp;');
		}

		//tidyup!!!!!!
		unset($data);
	}

	// remove trailing comma
	$context['battle_whos_online'] = rtrim($context['battle_whos_online'], '&nbsp;');
	$context['battle_whos_online'] = rtrim(rtrim($context['battle_whos_online']), ',');

	if(empty($b_users))
		$context['battle_whos_online'] .= $txt['battle_online_search'];
}

function battle_get_hist($limit = 5)
{
	global $smcFunc, $context;

	$context['battle_hist'] = array();
	$result = $smcFunc['db_query']('', '
		SELECT id_hist, content, time
		FROM {db_prefix}battle_history
		ORDER BY id_hist DESC
		LIMIT {int:limit}',
		array('limit' => $limit)
	);

	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		// And add them to the list
		$context['battle_hist'][] = array(
			'id_hist' => $row['id_hist'],
			'content' => $row['content'],
			'time' => $row['time'],
		);
	}

	$smcFunc['db_free_result']($result);
}
?>