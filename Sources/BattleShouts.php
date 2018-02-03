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

function battleShout($per_page = 10)
{
	global $smcFunc, $modSettings, $user_info, $context, $scripturl, $txt;

	$context['battle_shout_hist'] = array();
	$context['battle_diaplay'] = array('page' => false, 'pages' => '0');
	$current = !empty($_REQUEST['ca']) ? 'sa=' . $_REQUEST['ca'] . ';' : '';

	if($user_info['battle_only_buddies_shout'])
			$where = 'WHERE FIND_IN_SET({int:idmem}, mem.buddy_list) OR {int:idmem} = s.id_member';
		else
			$where = '';

	if(isset($_GET['hist']))
	{
		//We Need Our Template
		$context['sub_template']  = 'battle_shout';
		$request = $smcFunc['db_query']('', '
			SELECT COUNT(id_shout)
			FROM {db_prefix}battle_shouts
			' . $where
		);

		$row = $smcFunc['db_fetch_assoc']($request);
		$smcFunc['db_free_result']($request);
		$tempArray = array_values($row);
		$nRows = array_shift($tempArray);
		$page = $context['current_page'] * $per_page;

		// Now create the page index.
		$result = $smcFunc['db_query']('', '
			SELECT s.id_shout, s.id_member, s.content, s.time, mem.real_name, mem.buddy_list, mg.online_color
			FROM {db_prefix}battle_shouts AS s
			LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = s.id_member)
			LEFT JOIN {db_prefix}membergroups AS mg ON (mg.id_group = CASE WHEN mem.id_group = {int:reg_mem_group} THEN mem.id_post_group ELSE mem.id_group END)
			' . $where . '
			ORDER BY id_shout DESC
			LIMIT {int:page}, {int:per}',
			array(
				'reg_mem_group' => 0,
				'idmem' => $user_info['id'],
				'per' => $per_page,
				'page' => $page
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($result))
		{
			// And add them to the list
			$context['battle_shout_hist'][] = array(
				'id_member' => $row['id_member'],
				'online_color' => $row['online_color'],
				'id_shout' => $row['id_shout'],
				'real_name' => $row['real_name'],
				'time' => $row['time'],
				'content' => $row['content']
			);
		}

		$smcFunc['db_free_result']($result);

		$context['current_pages'] = (($nRows-1) / $per_page) + 1;
		$context['battle_display'] = battle_pages($txt['battle_page'], '#battle_main', $scripturl . '?action=battle;sa=shout;hist;', $context['current_pages']);
	}

	if(isset($_REQUEST['del']))
	{
		// Do we have permission to moderate shouts?
		isAllowedTo('battle_shouts_mod');
		$id = (int)$_REQUEST['del'];

		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}battle_shouts
			WHERE id_shout = {int:ids}',
			array('ids' => $id,)
		);

		redirectexit('action=battle;' . $current);
	}

	if(isset($_GET['go']))
	{
		if (!$user_info['is_guest'])
		{
			isAllowedTo('battle_shouts');
			$shout = '' .htmlspecialchars($_REQUEST['the_shout'], ENT_QUOTES);
			add_to_battle_shoutbox($shout);
		}

		redirectexit('action=battle;' . $current);
	}
}

function add_to_battle_shoutbox($shout)
{
	global $user_info, $smcFunc, $txt;

	if ((strlen($shout) < 1))
		fatal_error($txt['battle_shoutbox_input_empty'], false);

	$smcFunc['db_insert']('replace',
		'{db_prefix}battle_shouts',
		array('id_member' => 'int','content' => 'string-255', 'time' => 'int'),
		array($user_info['id'],$shout,time()),
		array('id_shout')
	);
}

function battle_get_shouts($limit = 5)
{
	global  $sourcedir, $modSettings, $context, $smcFunc, $user_info;
	require_once($sourcedir . '/Subs.php');
	$context['battle_shout'] = array();

	if($user_info['battle_only_buddies_shout'])
		$where = 'WHERE FIND_IN_SET({int:idmem}, mem.buddy_list) OR {int:idmem} = s.id_member';
	else
		$where = '';

	$result = $smcFunc['db_query']('', '
		SELECT s.id_shout, s.id_member, s.content, s.time, mem.real_name, mem.buddy_list, mg.online_color
		FROM {db_prefix}battle_shouts AS s
		LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = s.id_member)
		LEFT JOIN {db_prefix}membergroups AS mg ON (mg.id_group = CASE WHEN mem.id_group = {int:reg_mem_group} THEN mem.id_post_group ELSE mem.id_group END)
		'.$where.'
		ORDER BY id_shout DESC
		LIMIT {int:limit}',
		array(
			'reg_mem_group' => 0,
			'idmem' => $user_info['id'],
			'limit' => $limit
		)
	);

	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		// And add them to the list
		$context['battle_shout'][] = array(
			'id_member' => $row['id_member'],
			'online_color' => $row['online_color'],
			'id_shout' => $row['id_shout'],
			'real_name' => $row['real_name'],
			'time' => $row['time'],
			'content' => $row['content']
		);
	}

	$smcFunc['db_free_result']($result);
}
?>