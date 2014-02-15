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

function battleAdmin()
{
	global $context, $sourcedir, $txt;

	//Set title and default sub-action.
	loadTemplate('Battle_Admin');

	//Load the language strings
	loadLanguage('Battle');

	//Are you allowed to administrate Battle?
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	require_once($sourcedir . '/BattleAdminQuests.php');
	require_once($sourcedir . '/Subs-BattleAdmin.php');

	if (empty($context['current_page']))
		$context['current_page'] = 1;

	$context['page_title'] = $txt['battle_adminmmc'];
        $context['current_page'] = (!empty($_REQUEST['current_page']) ? (int)$_REQUEST['current_page'] : $context['current_page']) -1;
	$context[$context['admin_menu_name']]['tab_data']['title'] = $txt['battle_adminmm'];
	$context[$context['admin_menu_name']]['tab_data']['description'] = $txt['battle_adminmmd'];
	$context['battle_sort'] = (!empty($_REQUEST['sort'])) && $_REQUEST['sort'] === 'id_member' ? 'id_member;' : 'real_name;';
	$context['battle_order'] = (!empty($_REQUEST['order'])) && $_REQUEST['order'] === 'DESC' ? 'DESC;' : 'ASC;';
	$context['battle_reset_points'] = !empty($_REQUEST['battle_reset_points']) ? (int)$_REQUEST['battle_reset_points'] : 0;

	$subActions = array(
		'main' => 'battle_main',
		'config' => 'battle_settings',
		'campaigns' => 'battle_campaigns',
		'edit_campaign' => 'battle_campaign_edit',
		'save_campaign' => 'battle_campaign_edit',
		'del_campaign' => 'battle_campaign_delete',
		'add_campaign' => 'battle_campaign_add',
		'prune_campaign' => 'battle_campaign_prune',
		'shop' => 'battle_shops',
		'add_item' => 'battle_shop_add_edit',
		'edit_item' => 'battle_shop_add_edit',
		'save_item' => 'battle_shop_add_edit',
		'shop_del' => 'battle_shop_item_delete',
		'bmem' => 'battle_members',
		'monsterlist' => 'battle_monsterlist',
		'editm' => 'battle_monster',
		'monster' => 'battle_monster',
		'savemonster' => 'battle_monster',
		'del' => 'battle_monster_delete',
		'quest_edit' => 'battle_quest_add_edit',
	        'quest_save' => 'battle_quest_add_edit',
	        'quest_add' => 'battle_quest_add_edit',
		'quest' => 'battle_quests',
		'quest_del' => 'battle_quest_delete',
	        'custom' => 'battle_custom',
	        'custom_del' => 'battle_cust_delete',
	        'custom_edit' => 'battle_cust_add_edit',
	        'custom_save' => 'battle_cust_add_edit',
	        'custom_add' => 'battle_cust_add_edit',
	        'maintain' => 'battle_maintain',
	        'reset' => 'battle_reset',
	        'dshout' => 'battle_reset_shouts',
	        'dhist' => 'battle_reset_hist',
		'dlead' => 'battle_reset_leaderboard',
		'dquest' => 'battle_reset_quest',
		'dpoints' => 'battle_reset_points',
	);



	$_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'main';
	$subActions[$_REQUEST['sa']]();
}

function battle_main()
{
	// Connect to http://webdevelop.comli.com via socket for new messages concerning this modification
	global $context, $txt, $sourcedir;
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	require_once($sourcedir . '/Subs-Package.php');

	$url = 'http://webdevelop.comli.com/index.php?page=battle_news';
	$message = $txt['battle_news_connect'];
	$version = '??';
	$html = fetch_web_data($url) ? fetch_web_data($url) : false;

	if ($html)
	{
		if (strpos(trim($html), '<div id="news_battle"') !== false)
		{
			$dom = new DOMDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML($html);
			libxml_use_internal_errors(false);
			$vers = $dom->getElementById('battle_version');
			$version = !empty($vers->nodeValue) ? $vers->nodeValue : '??';
			$element = $dom->getElementById('news_battle');
			$message = !empty($element->nodeValue) ? $element->nodeValue : $txt['battle_news_connect'];
		}
	}

	$context['battle_news_connect'] = str_replace(array("\r\n", "\n", "\r"), '<br />', $message);
	$context['battle_version_connect'] = $version;
}

function battle_maintain()
{
	global $txt, $context;
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$context['battle_mcommands'] = array('reset' => 'breset', 'dquest' => 'breset', 'dpoints' => 'breset', 'dshout' => 'empty', 'dhist' => 'empty', 'dlead' => 'empty');

	foreach ($context['battle_mcommands'] as $key => $command)
		$context['battle_maintain_' . $key] = !empty($_REQUEST[$key]) ? $_REQUEST[$key] : false;

	$context['sub_template'] = 'maintain';
	$context['page_title'] = $txt['battle_tabaman'];
}

function battle_custom()
{
	global $context, $txt, $smcFunc, $scripturl;
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$context['page_title'] = 'Custom Actions';
	$context['sub_template'] = 'battle_custom';
	$context['battle_diaplay'] = array('page' => false, 'pages' => '0');
	$context['battle_custom'] = array();
	$request = $smcFunc['db_query']('', '
			SELECT id_explore, start
			FROM {db_prefix}battle_explore
			ORDER BY id_explore DESC'
		);

	// Loop through all results
	// And add them to the list
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$context['battle_custom'][] = array(
			'id_explore' => $row['id_explore'],
			'start' => $row['start']
		);

	$smcFunc['db_free_result']($request);

	$context['battle_cust'] = battle_pagination($context['battle_custom'], 10);
	$context['battle_display'] = battle_pages($txt['battle_page'], false, $scripturl . '?action=admin;area=battle;sa=custom;', $context['current_pages']);
}

function battle_monster()
{
	global $smcFunc, $context, $txt;
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$context['monster']['id'] = !empty($_REQUEST['monster']) ? (int) $_REQUEST['monster'] : 0;
	$context['battle_images'] = getImageList('monsters');

	if (!empty($_POST['submit']))
	{
		checkSession();
		$fields = array('atk', 'def', 'name', 'hp', 'img', 'max_hp', 'mon_range', 'mon_max_range', 'evolve', 'counter');
		foreach ($fields as $field)
		{
			if ($field === 'counter' || $field === 'evolve' || $field === 'mon_range')
				$_POST[$field] = !empty($_POST[$field]) ? (int)$_POST[$field] : 0;
			elseif (empty($_POST[$field]))
				fatal_error($field . ' ' . $txt['battle_Quest_error1'], false);
			else
			{
				if ($field === 'counter')
					$_POST[$field] = !empty($context['monster']['counter']) ? (int)$context['monster']['counter'] : 0;

				if ($field === 'evolve')
					$_POST[$field] = (int)$_POST[$field] > 1000 ? 1000 : (int)$_POST[$field];

				if ($field !== 'name' && $field !== 'img')
					$_POST[$field] = abs($_POST[$field]);
				else
					$_POST[$field] = $smcFunc['htmlspecialchars']($_POST[$field], ENT_QUOTES);

			}
		}

		if (empty($context['monster']['id']))
		{
			$smcFunc['db_insert']('',
				'{db_prefix}battle_monsters',
				array('atk' => 'int', 'def' => 'int', 'name' => 'string', 'hp' => 'int', 'img' => 'string', 'max_hp' => 'int', 'mon_range' => 'int', 'mon_max_range' => 'int', 'evolve' => 'int', 'counter' => 'int'),
				array($_POST['atk'], $_POST['def'], $_POST['name'], $_POST['hp'], $_POST['img'], $_POST['max_hp'], $_POST['mon_range'], $_POST['mon_max_range'], $_POST['evolve'], $_POST['counter']),
				array()
			);
		}
		else
		{
			$smcFunc['db_insert']('replace', '{db_prefix}battle_monsters',
				array(
					'id_monster' => 'int',
					'name' => 'string',
					'atk' => 'int',
					'def' => 'int',
					'hp' => 'int',
					'img' => 'string',
					'max_hp' => 'int',
					'mon_range' => 'int',
					'mon_max_range' => 'int',
					'evolve' => 'int',
					'counter' => 'int'
				),
				array(
					$context['monster']['id'],
					$_POST['name'],
					$_POST['atk'],
					$_POST['def'],
					$_POST['hp'],
					$_POST['img'],
					$_POST['max_hp'],
					$_POST['mon_range'],
					$_POST['mon_max_range'],
					$_POST['evolve'],
					$_POST['counter']
				),
				'id_monster'
			);

		}

		redirectexit('action=admin;area=battle;sa=monsterlist');
	}

	if (!empty($context['monster']['id']))
	{
		$request = $smcFunc['db_query']('', '
			SELECT name, atk, def, hp, img, max_hp, mon_range, mon_max_range, evolve, counter
			FROM {db_prefix}battle_monsters
			WHERE id_monster = {int:id_monster}',
			array('id_monster' => $context['monster']['id'],)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$context['monster'] += array(
				'name' => $row['name'],
				'atk' => $row['atk'],
				'def' => $row['def'],
				'hp' => $row['hp'],
				'img' => $row['img'],
				'max_hp' => $row['max_hp'],
				'mon_range' => $row['mon_range'],
				'mon_max_range' => $row['mon_max_range'],
				'evolve' => $row['evolve'],
				'counter' => !empty($row['counter']) ? (int)$row['counter'] : 0
			);
		}

		$smcFunc['db_free_result']($request);
	}
	else
	{
		$context['monster'] += array(
			'name' => '',
			'atk' => '',
			'def' => '',
			'hp' => '',
			'img' => '',
			'max_hp' => '',
			'mon_range' => '',
			'mon_max_range' => '',
			'evolve' => '',
			'counter' => ''
		);
	}

	$context['sub_template'] = 'monsterEdit_Add';
	$context['page_title'] = $txt['battle_monsters'];
}

function battle_monsterlist()
{
	global $smcFunc, $scripturl, $txt, $request, $context;

	$context['sub_template'] = 'battle_admin_monsters';
	$context['page_title'] = $txt['battle_monsters'];
	$context['battle_diaplay'] = array('page' => false, 'pages' => '0');
	$output = array();

	// Get the data.
	$request = $smcFunc['db_query']('', '
		SELECT id_monster, atk, def, hp, name, img, max_hp, mon_range, mon_max_range, evolve, counter
		FROM {db_prefix}battle_monsters
		ORDER BY atk DESC'
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
			'mon_range' => $row['mon_range'],
			'mon_max_range' => $row['mon_max_range'],
			'evolve' => $row['evolve'],
			'counter' => !empty($row['counter']) ? $row['counter'] : 0
			);


	}

	$smcFunc['db_free_result']($request);

	$context['get_monsters'] = battle_pagination($output, 10);
	$context['battle_display'] = battle_pages($txt['battle_page'], false, $scripturl . '?action=admin;area=battle;sa=monsterlist;', $context['current_pages']);
}

function battle_shops($per_page = 10)
{
	global $smcFunc, $scripturl, $txt, $context;

	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$context['sub_template'] = 'battle_shop';
	$context['page_title'] = $txt['battle_shop_item'];
	$context['shop'] = array();
	$context['battle_diaplay'] = array('page' => false, 'pages' => '0');
	$page = $context['current_page'] * $per_page;

	$request = $smcFunc['db_query']('', '
		SELECT COUNT(id_item) FROM {db_prefix}battle_shop
		WHERE id_item');

	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	$nRows = array_shift(array_values($row));

	$battleQ = $smcFunc['db_query']('', '
			SELECT id_item, name, price, action, img, description, amount
			FROM {db_prefix}battle_shop
			LIMIT {int:page}, {int:per}',
			array(
				'page' => $page,
				'per' => $per_page,
			)
		);

		// And add them to the list
		while ($row = $smcFunc['db_fetch_assoc']($battleQ))
			$context['shop'][] = array(
				'id_item' => $row['id_item'],
				'name' => $row['name'],
				'price' => $row['price'],
				'action' => $row['action'],
				'img' => $row['img'],
				'amount' => $row['amount'],
				'description' => $row['description'],
			);

	$smcFunc['db_free_result']($battleQ);

	// $context['shop'] = battle_pagination($context['battle_shop'], 10);
	$context['current_pages'] = (($nRows-1) / $per_page) + 1;
	$context['battle_display'] = battle_pages($txt['battle_page'], false, $scripturl . '?action=admin;area=battle;sa=shop;', $context['current_pages']);
}

function battle_settings($return_config = false)
{
	global $txt, $scripturl, $context, $sourcedir, $modSettings;

	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	require_once($sourcedir.'/ManageServer.php');

	// ensure there are no negative int values less one setting
	$intArray = array(
			'battle_map_across',
			'battle_map_down',
			'exp_bef_level',
			'exp_stat_level',
			'battle_level_mem',
			'battle_mem_battle_limit',
			'battle_mem_kill_limit',
			'exp_def_mem',
			'exp_def_mon',
			'battle_time',
			'battle_add_amount',
			'battle_how_much_reviv_user',
			'battle_how_much_hp',
			'battle_gold_reg',
			'battle_hp_reg',
			'battle_hp_max_reg',
			'battle_atk_reg',
			'battle_atk_max_reg',
			'battle_def_reg',
			'battle_def_max_reg',
			'battle_energy_reg',
			'battle_energy_max_reg',
			'battle_stamina_reg',
			'battle_stamina_max_reg',
			'battle_auto_lvl',
			'battle_players_lvl',
			'battle_reset_time'
		);

	foreach ($intArray as $int)
	{
		$modSettings[$int] = !empty($modSettings[$int]) ? $modSettings[$int] : 0;

		if ($int === 'battle_level_mem' && (!empty($modSettings['battle_level_mem']) ? (int)$modSettings['battle_level_mem'] : -1) < -1)
			$modSettings['battle_level_mem'] = -1;
		else
			$modSettings[$int] = abs($modSettings[$int]);

		$setArray[$int] = $modSettings[$int];
		updateSettings($setArray);
	}

	$context['sub_template'] = 'show_settings';

	$txt['battle_add_amount'] = sprintf($txt['battle_add_amount'],$modSettings['battle_time']);
	$config_vars = array(
		array('check', 'enable_battle', 'db', 'text'),
		array('check', 'enable_img_menu', 'db', 'text'),
		array('check', 'enable_battle_shoutbox', 'db', 'text'),
		array('check', 'enable_show_who_battle', 'db', 'text'),
		array('check', 'enable_battle_hist', 'db', 'text'),
		array('check', 'battle_enable_membattle', 'db', 'text'),
		array('check', 'battle_exp_restrict_membattle', 'db', 'text'),
		array('check', 'battle_enable_quests', 'db', 'text'),
		array('check', 'enable_battle_range', 'db', 'text'),
		array('check', 'enable_sts_post', 'db', 'text'),
		array('check', 'enable_sts_pm', 'db', 'text'),
		array('check', 'enable_sts_profile', 'db', 'text'),
		array('check', 'battle_auto_lvl', 'db', 'text'),
		array('check', 'battle_players_lvl', 'db', 'text'),
		array('check', 'battle_combine_pts', 'db', 'text'),
		'',
		array('text', 'battle_enemy_designation', 'db', 'text'),
		array('text', 'battle_enemy_name_plural', 'db', 'text'),
		array('text', 'bcash', 'db', 'text'),
		array('text', 'battle_cash', 'db', 'text'),
		array('int', 'battle_points', 'db', 'text'),
		array('int', 'battle_reset_time', 'db', 'text'),
		'',
		array('text', 'battle_map_name', 'db', 'text'),
		array('int', 'battle_map_across', 'db', 'text'),
		array('int', 'battle_map_down', 'db', 'text'),
		'',
		array('int', 'exp_bef_level', 'db', 'text'),
		array('int', 'exp_stat_level', 'db', 'text'),
		'',
		array('int', 'battle_level_mem', 'db', 'text'),
		array('int', 'battle_mem_battle_limit', 'db', 'text'),
		array('int', 'battle_mem_kill_limit', 'db', 'text'),
		'',
		array('int', 'exp_def_mem', 'db', 'text'),
		array('int', 'exp_def_mon', 'db', 'text'),
		'',
		array('int', 'battle_time', 'db', 'text'),
		array('int', 'battle_add_amount', 'db', 'text'),
		'',
		array('int', 'battle_how_much_reviv_user', 'db', 'text'),
		array('int', 'battle_how_much_hp', 'db', 'text'),
		'',
		array('int', 'battle_gold_reg', 'db', 'text'),
		array('int', 'battle_hp_reg', 'db', 'text'),
		array('int', 'battle_hp_max_reg', 'db', 'text'),
		array('int', 'battle_atk_reg', 'db', 'text'),
		array('int', 'battle_atk_max_reg', 'db', 'text'),
		array('int', 'battle_def_reg', 'db', 'text'),
		array('int', 'battle_def_max_reg', 'db', 'text'),
		array('int', 'battle_energy_reg', 'db', 'text'),
		array('int', 'battle_energy_max_reg', 'db', 'text'),
		array('int', 'battle_stamina_reg', 'db', 'text'),
		array('int', 'battle_stamina_max_reg', 'db', 'text'),
	);

	if (isset($_GET['save']))
	{
		saveDBSettings($config_vars);
		redirectexit('action=admin;area=battle;sa=config');
	}

	$context['post_url'] = $scripturl .'?action=admin;area=battle;save;sa=config';
	$context['settings_title'] = $txt['battle_config'];
	prepareDBSettingContext($config_vars);
}

function battle_members($per_page = 20)
{
	global $context, $smcFunc, $b, $txt, $userid, $scripturl, $sourcedir, $user_profile, $modSettings, $user_info;

	if (!allowedTo('admin_battle'))
		redirectexit('action=battle;sa=main;home;#battle_main');

	$context['sub_template'] = 'bmembers';
	$context['page_title'] = $txt['battle_mem'];
	$context['battle_warriors'] = array();
	$context['battle_list'] = array();
	$context['battle_members'] = array();
	$context['battle_userid'] = false;
	$context['battle_diaplay'] = array('page' => false, 'pages' => '0');
	$count = 0;
	$query_info = array('atk', 'max_atk', 'def', 'max_def', 'energy', 'max_energy', 'stamina', 'max_stamina', 'hp', 'max_hp', 'level', 'gold');
	require_once('SSI.php');
	db_extend('packages');

	if (!empty($_REQUEST['sc']))
	{
		foreach ($query_info as $info)
			$_POST[$info] = !empty($_POST[$info]) && (int)$_POST[$info] > 0 ? (int)$_POST[$info] : 0;
	}

	if ($context['battle_order'] == 'DESC')
		$order = 'DESC';
	else
		$order = 'ASC';

	if($context['battle_sort'] == 'real_name')
		$sort = 'real_name';
	else
		$sort = 'id_member';

	require_once($sourcedir . '/Subs-Members.php');
	$allowedBattle = groupsAllowedTo('view_battle');
	$_POST['thename'] = !empty($_POST['thename']) ? $smcFunc['htmlspecialchars']($_POST['thename'], ENT_QUOTES) : '';
	if(isset($_REQUEST['next']) && isset($_REQUEST['thename']))
	{
		$userid = battle_UserId($_REQUEST['thename']);
		if(!isset($userid))
			$b['b_message'] = $txt['battle_mem_error'];
		else
		{
			loadMemberData(array($userid),false, 'normal');
			$context['battle_userlink'] = '<a href="' . $scripturl . '?action=profile;u=' . $userid . '">' . $user_profile[$userid]['real_name'] . '</a>';
			$context['battle_userid'] = ';thename=' . $userid;
			foreach ($query_info as $info)
			{
				if ($info === 'gold')
					$context['battle_warriorInfo'][$userid][$info] = battle_gold($userid);
				else
					$context['battle_warriorInfo'][$userid][$info] = battle_query($userid, $info);
			}

		}
	}

	if(isset($_REQUEST['update']))
	{
		//check the users session
		checkSession();
		$cash = !empty($_POST['cash']) ? (int) $_POST['cash'] : 0;
		$atk = !empty($_POST['atk']) ? (int) $_POST['atk'] : 0;
		$def = !empty($_POST['def']) ? (int) $_POST['def'] : 0;
		$stam = !empty($_POST['stam']) ? (int) $_POST['stam'] : 0;
		$max_stam = !empty($_POST['max_stam']) ? (int) $_POST['max_stam'] : 0;
		$hp = !empty($_POST['hp']) ? (int) $_POST['hp'] : 0;
		$max_hp = !empty($_POST['max_hp']) ? (int) $_POST['max_hp'] : 0;
		$energy = !empty($_POST['energy']) ? (int) $_POST['energy'] : 0;
		$level = !empty($_POST['level']) ? (int) $_POST['level'] : 0;
		$exp = !empty($_POST['exp']) ? (int)$_POST['exp'] : 0;
		$max_exp = !empty($_POST['max_exp']) ? (int)$_POST['max_exp'] : 0;
		$max_land = !empty($_POST['max_land']) ? (int) $_POST['max_land'] : 0;
		$max_atk = !empty($_POST['max_atk']) ? (int) $_POST['max_atk'] : 0;
		$max_def = !empty($_POST['max_def']) ? (int) $_POST['max_def'] : 0;
		$max_energy = !empty($_POST['max_energy']) ? (int) $_POST['max_energy'] : 0;
		$defaults = !empty($_POST['defaults']) ? (int) $_POST['defaults'] : 0;
		$userid = !empty($_POST['thename']) ? $_POST['thename'] : 0;
		$resetAll = !empty($_POST['reset_all']) ? $_POST['reset_all'] : 0;
		$resetAllNew = !empty($_POST['reset_all_new']) ? $_POST['reset_all_new'] : 0;

		if (!empty($userid))
		{
			$userid = battle_UserId($userid);
			loadMemberData(array($userid),false, 'normal');
			$context['battle_userid'] = ';thename=' . $userid;
			$context['battle_userlink'] = '<a href="' . $scripturl . '?action=profile;u=' . $userid . '">' . $user_profile[$userid]['real_name'] . '</a>';
			$exp = !empty($user_profile[$userid]['exp']) ? (int)$user_profile[$userid]['exp'] : 0;
			$max_exp = !empty($user_profile[$userid]['max_exp']) ? (int)$user_profile[$userid]['max_exp'] : 0;
		}

		// Reset this user to default stats?
		if ($defaults > 0)
		{
			battle_defaults($userid);
			updateMemberData($userid, array('is_dead' => 0));
			$smcFunc['db_query']('', "
				DELETE FROM {db_prefix}battle_graveyard
				WHERE id_memdef = {int:warrior}",
				array('warrior' => $userid)
			);
		}
		elseif ($resetAll > 0)
		{
			@ini_set('memory_limit', '128M');
			if ($hp > 0)
			{
				updateMemberData(null, array('is_dead' => 0));
				$smcFunc['db_query']('', "
					DELETE FROM {db_prefix}battle_graveyard
					WHERE id_memdef = {int:warrior}",
					array('warrior' => $userid)
				);
			}
			else
				updateMemberData(null, array('is_dead' => 1));

			updateMemberData(null, array(
				'stamina' => $stam,
				'energy' => $energy,
				'max_energy' => $max_energy,
				'max_stamina' => $max_stam,
				'max_def' => $max_def,
				'max_atk' => $max_atk,
				'exp' => $exp,
				'max_exp' => $max_exp,
				'level' => $level,
				'hp' => $hp,
				'max_hp' => $max_hp,
				'def' => $def,
				$modSettings['bcash'] => $cash,
				'atk' => $atk
				)
			);

		}
		elseif ($resetAllNew > 0)
		{
			@ini_set('memory_limit', '128M');
			$membersData = array();

			$request = $smcFunc['db_query']('','
				SELECT id_member, hp, gold, max_hp, atk, max_atk, def, max_def, energy, max_energy, stamina, max_stamina,
				exp, max_exp, level, bpm, is_dead, stat_point, mem_slays, mon_slays, battle_only_buddies_shout
				FROM {db_prefix}members
				WHERE id_member >= {int:member}
				ORDER BY id_member ASC',
				array('member' => 1)
			);

			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
			    $max_exp = !empty($row['max_exp']) ? $row['max_exp'] : 0;

			    if (empty($row['level']) && empty($row['exp']) && empty($row['max_exp']))
			    {
			        $membersData[$row['id_member']] = array(
			            $modSettings['bcash'] => !empty($cash) ? $cash : 0,
			            'hp' => !empty($hp) ? $hp : 0,
			            'max_hp' => !empty($max_hp) ? $max_hp : 0,
			            'atk' => !empty($atk) ? $atk : 0,
			            'max_atk' => !empty($max_atk) ? $max_atk : 0,
			            'def' => !empty($def) ? $def : 0,
			            'max_def' => !empty($max_def) ? $max_def : 0,
			            'energy' => !empty($energy) ? $energy : 0,
			            'max_energy' => !empty($max_energy) ? $max_energy : 0,
			            'stamina' => !empty($stam) ? $stam : 0,
			            'max_stamina' => !empty($max_stam) ? $max_stam : 0,
			            'level' => !empty($level) ? $level : 0,
			            'exp' => !empty($exp) ? $exp : 0,
				    'battle_points' => 0
			        );
			    }
			    elseif (empty($row['max_hp']) && empty($row['hp']))
			        $membersData[$row['id_member']] = array('hp' => 100, 'max_hp' => 100, 'battle_points' => 0);
			}

			$smcFunc['db_free_result']($request);

			foreach ($membersData as $member => $data)
			    updateMemberData($member, $data);
			$currency = !empty($modSettings['bcash']) ? $modSettings['bcash'] : 'gold';

			$updateTables = array(
				'members' => array(
					$currency => array(
					        'type' => 'int',
					        'size' => 11,
					        'unsigned' => true,
					        'null' => false,
					        'default' => $cash,
					),
					'lastupdate' => array(
					        'type' => 'int',
					        'size' => 11,
					        'unsigned' => true,
					        'null' => false,
					        'default' => time(),
					),
					'atk' => array(
					        'type' => 'int',
					        'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $atk,
					),
					'max_atk' => array(
						'type' => 'int',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $max_atk,
					),
					'def' => array(
						'type' => 'int',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $def,
					),
					'max_def' => array(
						'type' => 'int',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $max_def,
					),
					'energy' => array(
						'type' => 'int',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $energy,
					),
					'max_energy' => array(
						'type' => 'int',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $max_energy,
					),
					'stamina' => array(
						'type' => 'int',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $stam,
					),
					'max_stamina' => array(
						'type' => 'int',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $max_stam,
					),
					'hp' => array(
						'type' => 'int',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $hp,
					),
					'max_hp' => array(
						'type' => 'int',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $max_hp,
					),
					'exp' => array(
						'type' => 'int',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $exp,
					),
					'level' => array(
						'type' => 'int',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => $level,
					),
					'battle_points' => array(
						'type' => 'bigint',
						'size' => 11,
						'unsigned' => true,
						'null' => false,
						'default' => 0,
					),
				),
			);

			foreach ($updateTables as $table => $data)
			{
				foreach ($data as $key => $datum)
				{
					$smcFunc['db_change_column']('{db_prefix}' . $table,
						$key,
						$datum
					);
				}
			}
		}
		else
		{
			updateMemberData($userid, array(
				'stamina' => $stam,
				'energy' => $energy,
				'max_energy' => $max_energy,
				'max_stamina' => $max_stam,
				'max_def' => $max_def,
				'max_atk' => $max_atk,
				'exp' => $exp,
				'level' => $level,
				'hp' => $hp,
				'max_hp' => $max_hp,
				'def' => $def,
				$modSettings['bcash'] => $cash,
				'atk' => $atk
				)
			);

			if ((int)$hp > 0)
			{
				updateMemberData($userid, array('is_dead' => 0));
				$smcFunc['db_query']('', "
					DELETE FROM {db_prefix}battle_graveyard
					WHERE id_memdef = {int:warrior}",
					array('warrior' => $userid)
				);
			}
			else
			{
				$row['memdef'] = 0;
				$request = $smcFunc['db_query']('', '
					SELECT id_memdef FROM {db_prefix}battle_graveyard
					WHERE id_memdef = {int:warrior}
					LIMIT {int:limit}',
					array('warrior' => $userid, 'limit' => 1));

				$row = $smcFunc['db_fetch_assoc']($request);
				$smcFunc['db_free_result']($request);

				updateMemberData($userid, array('is_dead' => 1));
				if ((int)$row['id_memdef'] != $userid)
					$smcFunc['db_insert']('insert',
						'{db_prefix}battle_graveyard',
						array('id_mem' => 'int', 'id_memdef' => 'int', 'name' => 'string', 'date' => 'int'),
						array($user_info['id'], $userid, $user_profile[$userid]['real_name'], time()),
						array('id_grave')
					);
			}
		}

		foreach ($query_info as $info)
		{
			if ($info === 'gold')
				$context['battle_warriorInfo'][$userid][$info] = battle_gold($userid);
			else
				$context['battle_warriorInfo'][$userid][$info] = battle_query($userid, $info);
		}
	}
	elseif (AllowedTo('view_mlist') && empty($_REQUEST['thename']))
	{
		$groups = join(',', $allowedBattle['allowed']);
		$request = $smcFunc['db_query']('', '
			SELECT COUNT(id_member) FROM {db_prefix}members
			WHERE id_group IN ({raw:groups}) OR id_group = 1',
			array('groups' => $groups));

		$row = $smcFunc['db_fetch_assoc']($request);
		$smcFunc['db_free_result']($request);
		$nRows = array_shift(array_values($row));
		$page = $context['current_page'] * $per_page;

		$context['battle_members'] = ssi_queryMembers($query_where = 'id_member > 0 AND id_group IN ('.$groups.')', $query_where_params = array(), $query_limit = $page . ',' . $per_page, $query_order = $sort . ' ' . $order, $output_method = '');

		foreach ($context['battle_members'] as $key => $member)
		{
			$context['battle_warriors'][] = array(
							'name' => strlen($member['username']) > 50 ? substr($member['username'], 0, 47) . '...' : $member['username'],
							'id' => $member['id'],
							'href' => $member['href'],
							'stats' => $scripturl . '?action=admin;area=battle;sa=bmem;next;order=' . $order. ';sort=' . $sort . ';current_page=' . ($context['current_page']+1) . ';thename=' . $member['id'] . ';'
			);
		}

		// $context['battle_warriors'] = battle_pagination($context['battle_list'], 20);
		$context['current_pages'] = (($nRows-1) / $per_page) + 1;
		$context['battle_display'] = battle_pages($txt['battle_page'], false, $scripturl . '?action=admin;area=battle;sa=bmem;', $context['current_pages']);
	}
}
?>