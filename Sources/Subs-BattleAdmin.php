<?php
/*
 * Battle was developed for SMF forums c/o SA, nend & Chen Zhen
 * Copyright 2009, 2010, 2011, 2012, 2013, 2014, 2018  SA | nend | Chen Zhen
 * Revamped and supported by Chen Zhen
 * This software package is distributed under the terms of its Creative Commons - Attribution No Derivatives License (by-nd) 3.0
 * License: https://creativecommons.org/licenses/by-nd/3.0/
 * Support thread: https://web-develop.ca/index.php?board=15.0 
 */

if (!defined('SMF'))
	die('Hacking attempt...');

function battle_reset_hist()
{
	global $smcFunc, $txt;
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$smcFunc['db_query']('', 'TRUNCATE {db_prefix}battle_history',array());

	redirectexit('action=admin;area=battle;sa=maintain;dhist=done');
}

function battle_reset_quest()
{
	global $smcFunc, $txt;
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	db_extend('packages');
	$campaigns = array();

	$smcFunc['db_query']('', 'TRUNCATE {db_prefix}battle_quest_champs',array());

	for ($i=1; $i<25; $i++)
	{
		if (battle_check_table_exists('battle_campaign_' . $i))
			$campaigns[$i] = 'battle_campaign_' . $i;
	}

	foreach ($campaigns as $campaign)
	{
		$smcFunc['db_query']('', "
			DELETE FROM {db_prefix}{$campaign}
			WHERE id_warrior >= {int:warrior}",
			array('warrior' => 1)
		);

		$smcFunc['db_query']('', "
			UPDATE {db_prefix}{$campaign}
			SET quest_completions = {int:num}
			WHERE id_warrior = {int:default}",
			array('default' => 0, 'num' => 0)
		);
	}

	$smcFunc['db_remove_column']('{db_prefix}battle_quest', 'plays');

	$smcFunc['db_add_column'](
		'{db_prefix}battle_quest',
		array(
	            'name' => 'plays',
	            'type' => 'int',
	            'size' => 10,
		    'unsigned' => true,
	            'null' => false,
		    'default' => 0
		),
	        array(),
	        'ignore',
	        'fatal'
	);

	unset($_SESSION['campaigns_query']);
	redirectexit('action=admin;area=battle;sa=maintain;dquest=done');
}

function battle_reset_shouts()
{
	global  $smcFunc, $txt;
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$smcFunc['db_query']('', 'TRUNCATE {db_prefix}battle_shouts',array());

	redirectexit('action=admin;area=battle;sa=maintain;dshout=done');
}

function battle_reset_leaderboard($truncate = true, $allowed = 50)
{
	global $smcFunc, $txt;
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	if ($truncate)
	{
		$smcFunc['db_query']('', "TRUNCATE TABLE {db_prefix}battle_champs");
		$smcFunc['db_query']('', "TRUNCATE TABLE {db_prefix}battle_scores");
	}
	else
	{
		$del = array();
		$request = $smcFunc['db_query']('', "
			SELECT id_champ
			FROM {db_prefix}battle_champs
			ORDER BY id_champ DESC
			LIMIT {int:allowed}",
			array('allowed' => $allowed+1)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
			$del[] = !empty($row['id_champ']) ? $row['id_champ'] : 0;

		$smcFunc['db_free_result']($request);

		foreach ($del as $delete)
		{
			if (!empty($delete))
				$smcFunc['db_query']('', "
					DELETE FROM {db_prefix}battle_champs
					WHERE id_champ < {int:champ}",
					array('champ' => $delete)
				);
		}
	}

	redirectexit('action=admin;area=battle;sa=maintain;dlead=done');
}

function battle_reset_game()
{
	global $context, $txt;

	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$context['battle_reset_points'] = true;
	battle_reset_points(true);
	battle_reset(true);
	redirectexit('action=admin;area=battle;sa=maintain;resetgame=done');
}

function battle_reset($auto = false)
{
	global $modSettings, $txt, $sourcedir, $smcFunc;
	if (!AllowedTo('admin_battle') && !$auto)
		fatal_error($txt['battle_admin_error1'], false);

	updateMemberData(null, array(
		'bpm' => 0,
		'gold' => !empty($modSettings['battle_add_amount']) ? (int)$modSettings['battle_add_amount'] + 100 : 1100,
		'is_dead' => 0,
		'stat_point' => 0,
		'lastupdate' => time(),
		'atk' => !empty($modSettings['battle_atk_reg']) ? (int)$modSettings['battle_atk_reg'] : 100,
		'max_atk' => !empty($modSettings['battle_atk_max_reg']) ? (int)$modSettings['battle_atk_max_reg'] : 100,
		'def' => !empty($modSettings['battle_def_reg']) ? (int)$modSettings['battle_def_reg'] : 100,
		'max_def' => !empty($modSettings['battle_def_max_reg']) ? (int)$modSettings['battle_def_max_reg'] : 100,
		'energy' => !empty($modSettings['battle_energy_reg']) ? (int)$modSettings['battle_energy_reg'] : 100,
		'max_energy' => !empty($modSettings['battle_energy_max_reg']) ? (int)$modSettings['battle_energy_max_reg'] : 100,
		'stamina' => !empty($modSettings['battle_stamina_reg']) ? (int)$modSettings['battle_stamina_reg'] : 100,
		'max_stamina' => !empty($modSettings['battle_stamina_max_reg']) ? (int)$modSettings['battle_stamina_max_reg'] : 100,
		'hp' => !empty($modSettings['battle_hp_reg']) ? (int)$modSettings['battle_hp_reg'] : 100,
		'max_hp' => !empty($modSettings['battle_hp_max_reg']) ? (int)$modSettings['battle_hp_max_reg'] : 100,
		'exp' => 0,
		'max_exp' => !empty($modSettings['exp_bef_level']) ? (int)$modSettings['exp_bef_level'] : 0,
		'level' => 0,
		'mem_slays' => 0,
		'battle_only_buddies_shout' => 0,
		'mon_slays' => 0,
		'battle_last' => 0
		)
	);

	//empty the grave yard
	$smcFunc['db_query']('', 'TRUNCATE {db_prefix}battle_graveyard',array());

	if($modSettings['enable_battle_hist'])
	{
		if (!$auto)
			$content = '<span class="error"><strong>'.$txt['battle_maintain7'].'</strong></span>';
		else
			$content = '<span class="error"><strong>'.$txt['battle_auto_reset'].'</strong></span>';

		add_to_battle_hist($content);
	}

	if (!$auto)
		redirectexit('action=admin;area=battle;sa=maintain;reset=done');
	else
		return;
}

function battle_cust_delete()
{
	global $smcFunc;

	$cust_id = !empty($_REQUEST['cust']) ? (int) $_REQUEST['cust'] : 0;
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}battle_explore
		WHERE id_explore = {int:q_id}',
		array('q_id' => $cust_id,)
	);

	redirectexit('action=admin;area=battle;sa=custom');
}

function getImageList($directory = 'monsters')
{
	global $settings;

	// Start with an empty array
	$imageList = array();
	$imageTypes = array('.gif', '.jpg', '.jpeg', '.png');

	// Try to open the images directory
	if ($handle = opendir($settings['default_theme_dir'] . '/' . basename($settings['default_images_url']) . '/battle/' . $directory))
	{
		// For each file in the directory...
		while (false !== ($file = readdir($handle)))
		{
			// ...if it's a valid file, add it to the list
			if (!in_array($file, array('.', '..', 'blank.gif', 'index.php')) && battle_strpos($file, $imageTypes, 1))
				$imageList[] = $file;
		}

		// Sort the list
		sort($imageList);
		return $imageList;
	}
	// Otherwise, if directory inaccessible, show an error
	else
		fatal_lang_error('cannot_open_images');

}

function battle_strpos($haystack, $needles=array(), $offset=0)
{
        $chars = array();
        foreach($needles as $needle)
	{
                $search = strpos($haystack, $needle, $offset);
                if ($search !== false)
			$chars[$needle] = $search;
        }
        if(empty($chars))
		return false;

        return min($chars);
}

function battle_monster_delete()
{
	global $smcFunc, $txt;
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$monster_id = !empty($_REQUEST['monster']) ? (int) $_REQUEST['monster'] : 0;
	if (empty($monster_id))
		fatal_error($txt['battle_nomon'], false);

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}battle_monsters
		WHERE id_monster = {int:mon_id}',
		array('mon_id' => $monster_id,)
	);

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}battle_monsters_fight
		WHERE id_monster = {int:mon_id}',
		array('mon_id' => $monster_id,)
	);

	redirectexit('action=admin;area=battle;sa=monsterlist');
}

function battle_shop_item_delete()
{

	global $smcFunc, $txt;
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$item_id = !empty($_REQUEST['item']) ? (int) $_REQUEST['item'] : 0;
	if (empty($item_id))
		fatal_error($txt['battle_shop_error1'], false);

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}battle_shop
		WHERE id_item = {int:item_id}',
		array(
			'item_id' => $item_id,
		)
	);

	redirectexit('action=admin;area=battle;sa=shop');
}

function battle_reset_points($auto = false)
{
	global $smcFunc, $context, $scripturl, $txt, $modSettings;

	if (!AllowedTo('admin_battle') && !$auto)
		fatal_error($txt['battle_admin_error1'], false);

	@ini_set('memory_limit', '128M');

	// Add current entries to the overall leaderboard
	$context['top_points'] = array();
	$title = !empty($modSettings['battle_map_name']) ? $modSettings['battle_map_name'] : $txt['battle_map_name'];
	battle_get_stats('battle_points', 'top_points');
	foreach ($context['top_points'] as $i => $file)
	{
		$score = (!empty($modSettings['battle_combine_pts']) ? battle_campaign_score($file['id_member']) + $file['battle_points'] : $file['battle_points']);
		if ($score > 0)
			$context['top_points'][$i]['total'] = $score;
	}

	foreach ($context['top_points'] as $data)
	{
		list($oldScore, $check, $level) = array(0, false, 0);

		$request = $smcFunc['db_query']('', '
			SELECT level
			FROM {db_prefix}members
			WHERE id_member = {int:warrior}
			LIMIT 1',
			array('warrior' => $data['id_member'])
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
			$level = !empty($row['level']) ? (int)$row['level'] : 0;

		$smcFunc['db_free_result']($request);

		$request = $smcFunc['db_query']('', '
			SELECT id_warrior, score, battle_title
			FROM {db_prefix}battle_scores
			WHERE id_warrior = {int:warrior}
			AND battle_title = {string:battle_title}
			LIMIT 1',
			array('warrior' => $data['id_member'], 'battle_title' => $title)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$oldScore = !empty($row['score']) ? (int)$row['score'] : 0;
			$check = true;

		}
		$smcFunc['db_free_result']($request);

		if ($data['total'] > $oldScore && $check)
			$smcFunc['db_insert']('replace', '{db_prefix}battle_scores',
				array(
					'id_warrior' => 'int',
					'battle_title' => 'string',
					'score' => 'int',
					'date' => 'int',
					'level' => 'int'
				),
				array(
					$data['id_member'],
					$title,
					$data['total'],
					time(),
					$level
				),
				array('id_warrior', 'battle_title')
			);
		elseif (!$check)
			$smcFunc['db_insert']('insert', '{db_prefix}battle_scores',
				array(
					'id_warrior' => 'int',
					'battle_title' => 'string',
					'score' => 'int',
					'date' => 'int',
					'level' => 'int'
				),
				array(
					$data['id_member'],
					$title,
					$data['total'],
					time(),
					$level
				),
				array('id_warrior', 'battle_title')
			);
	}

	// Delete campaign points?
	if (!empty($context['battle_reset_points']))
	{
		for ($i=1;$i<25;$i++)
		{
			if (battle_check_table_exists('battle_campaign_' . $i))
			{
				$newRow = array();
				$result = $smcFunc['db_query']('', '
					SELECT id_warrior, id_campaign, campaign_name, score, start_time, end_time, timed_campaign, level_completion, quest_completions, image
					FROM {db_prefix}{raw:table}',
					array('table' => 'battle_campaign_' . $i)
				);

				while ($row = $smcFunc['db_fetch_assoc']($result))
				{
					$newRow[$row['id_warrior']] = array(
							'id_warrior' => $row['id_warrior'],
							'id_campaign' => $row['id_campaign'],
							'campaign_name' => $row['campaign_name'],
							'score' => $row['score'],
							'start_time' => $row['start_time'],
							'end_time' => $row['end_time'],
							'timed_campaign' => $row['timed_campaign'],
							'level_completion' => $row['level_completion'],
							'quest_completions' => $row['quest_completions'],
							'image' => $row['image']
					);
				}

				$smcFunc['db_free_result']($result);

				foreach ($newRow as $row)
					$smcFunc['db_insert'](
						'replace',
						'{db_prefix}battle_campaign_' . $i,
						array('id_warrior' => 'int', 'id_campaign' => 'int', 'campaign_name' => 'string', 'score' => 'int', 'start_time' => 'int', 'end_time' => 'int', 'timed_campaign' => 'int', 'level_completion' => 'int', 'quest_completions' => 'int', 'image' => 'string'),
						array($row['id_warrior'], $row['id_campaign'], $row['campaign_name'], 0, $row['start_time'], $row['end_time'], $row['timed_campaign'], $row['level_completion'], $row['quest_completions'], $row['image']),
						array('id_warrior')
					);
			}
		}
	}

	updateMemberData(null, array('battle_points' => 0));

	if (!$auto)
		redirectexit($scripturl . '?action=admin;area=battle;sa=maintain;dpoints=done;');
	else
		return;

}

function battle_defaults($userid)
{
	global $modSettings;

	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	// Resets the selected user to default stat values
	$gold = !empty($modSettings['battle_gold_reg']) ? $modSettings['battle_gold_reg'] : 100;
	updateMemberData($userid, array(
			'bpm' => 0,
			'gold' => !empty($modSettings['battle_add_amount']) ? $modSettings['battle_add_amount'] + $gold : 1100,
			'is_dead' => 0,
			'stat_point' => 0,
			'lastupdate' => time(),
			'atk' => !empty($modSettings['battle_atk_reg']) ? $modSettings['battle_atk_reg'] : 100,
			'max_atk' => !empty($modSettings['battle_atk_max_reg']) ? $modSettings['battle_atk_max_reg'] : 100,
			'def' => !empty($modSettings['battle_def_reg']) ? $modSettings['battle_def_reg'] : 100,
			'max_def' => !empty($modSettings['battle_def_max_reg']) ? $modSettings['battle_def_max_reg'] : 100,
			'energy' => !empty($modSettings['battle_energy_reg']) ? $modSettings['battle_energy_reg'] : 100,
			'max_energy' => !empty($modSettings['battle_energy_max_reg']) ? $modSettings['battle_energy_max_reg'] : 100,
			'stamina' => !empty($modSettings['battle_stamina_reg']) ? $modSettings['battle_stamina_reg'] : 100,
			'max_stamina' => !empty($modSettings['battle_stamina_max_reg']) ? $modSettings['battle_stamina_max_reg'] : 100,
			'hp' => !empty($modSettings['battle_hp_reg']) ? $modSettings['battle_hp_reg'] : 100,
			'max_hp' => !empty($modSettings['battle_hp_max_reg']) ? $modSettings['battle_hp_max_reg'] : 100,
			'exp' => 0,
			'max_exp' => 0,
			'level' => 0,
			'mem_slays' => 0,
			'battle_only_buddies_shout' => 0,
			'mon_slays' => 0,
			'battle_points' => 0
		)
	);

}

function battle_UserId($nameid)
{
	global $smcFunc;

	$query = 'real_name = {string:nameid} OR member_name = {string:nameid}';
	if(ctype_digit($nameid))
		$query = 'id_member = {int:nameid}';

	$result = $smcFunc['db_query']('','
		SELECT id_member
		FROM {db_prefix}members
		WHERE '.$query.'
		LIMIT 1',
			array('nameid' => $nameid,
		      )
		);

	$row =  $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);
	return $row['id_member'];
}

function battle_gold($userid)
{
	global $modSettings, $smcFunc;

	$result = $smcFunc['db_query']('', '
			SELECT {raw:cash}
			FROM {db_prefix}members
			WHERE id_member = {int:userid}
			LIMIT 1',
				array(
				'userid' => $userid,
				'cash' => $modSettings['bcash'],
			)
		);

	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);
	return $row[$modSettings['bcash']];
}

function battle_query($userid, $column)
{
	global $smcFunc;

	$result = $smcFunc['db_query']('', "
			SELECT {$column}
			FROM {db_prefix}members
			WHERE id_member = {int:userid}
			LIMIT 1",
				array('userid' => $userid,
			)
		);

	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);
	return $row[$column];
}

function battle_shop_add_edit()
{
	global $smcFunc, $context, $txt;

	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$context['item']['id'] = !empty($_REQUEST['item']) ? (int) $_REQUEST['item'] : 0;
	$context['shop_images'] = getImageList('shop');

	if (!empty($_POST['submit']))
	{
		checkSession();
		$actions = array('hp','atk','def','stamina','energy');
		$fields = array('name', 'price', 'act', 'img', 'description', 'amount');
		foreach ($fields as $field)
		{
			if ($field === 'act')
			{
				$_POST[$field] = !empty($_POST[$field]) ? $smcFunc['strtolower']($_POST[$field]) : 'atk';
				$_POST[$field] = in_array($_POST[$field], $actions) ? $_POST[$field] : 'atk';
			}

			if (empty($_POST[$field]))
				fatal_error($field . ' ' . $txt['battle_Quest_error1'], false);
			elseif ($field === 'price' || $field === 'amount')
				$_POST[$field] = abs($_POST[$field]);
			else
				$_POST[$field] = $smcFunc['htmlspecialchars']($_POST[$field], ENT_QUOTES);

		}

		if (empty($context['item']['id']))
		{
			$smcFunc['db_insert']('insert',
				'{db_prefix}battle_shop',
				array('name' => 'string', 'price' => 'int', 'action' => 'string', 'img' => 'string', 'description' => 'string', 'amount' => 'int'),
				array($_POST['name'], $_POST['price'], $_POST['act'], $_POST['img'], $_POST['description'], $_POST['amount']),
				array('id_item')
			);
		}
		else
		{
			$smcFunc['db_insert']('replace',
				'{db_prefix}battle_shop',
				array('id_item' => 'int', 'name' => 'string', 'price' => 'int', 'action' => 'string', 'img' => 'string', 'description' => 'string', 'amount' => 'int'),
				array($context['item']['id'], $_POST['name'], $_POST['price'], $_POST['act'], $_POST['img'], $_POST['description'], $_POST['amount']),
				array('id_item')
			);
		}

		redirectexit('action=admin;area=battle;sa=shop');
	}

	if (!empty($context['item']['id']))
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_item, name, price, action, img, description, amount
			FROM {db_prefix}battle_shop
			WHERE id_item = {int:id_item}',
			array('id_item' => $context['item']['id'],)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$context['item'] += array(
				'id_item' => $row['id_item'],
				'name' => $row['name'],
				'price' => $row['price'],
				'action' => $row['action'],
				'img' => $row['img'],
				'description' => $row['description'],
				'amount' => $row['amount'],
			);
		}

		$smcFunc['db_free_result']($request);
	}
	else
	{
		$context['item'] += array(
		        'id_item' => '',
			'name' => '',
			'price' => '',
			'action' => '',
			'img' => '',
			'description' => '',
			'amount'=> '',
		);
	}

	$context['sub_template'] = 'shopEdit_Add';
	$context['page_title'] = $txt['battle_shop_item'];
}

function battle_cust_add_edit()
{
	global $smcFunc, $context, $txt;

	isAllowedTo('admin_battle');
	$context['cust']['id'] = !empty($_REQUEST['cust']) ? (int) $_REQUEST['cust'] : 0;
	if (!empty($_POST['submit']))
	{
		checkSession();
		$fields = array('outcome1', 'outcome1_reward', 'outcome1_action', 'outcome2', 'outcome2_reward', 'outcome2_action', 'start','price');
		$actions = array('hp','atk','def','stamina','energy','gold');
		foreach ($fields as $field)
		{
			if ($field === 'outcome1_action' || $field === 'outcome2_action')
			{
				$_POST[$field] = !empty($_POST[$field]) ? $smcFunc['strtolower']($_POST[$field]) : 'atk';
				$_POST[$field] = in_array($_POST[$field], $actions) ? $_POST[$field] : 'atk';
			}

			if ($field === 'outcome1_reward' || $field === 'outcome2_reward')
				$_POST[$field] = !empty($_POST[$field]) ? (int)($_POST[$field]) : 0;
			elseif ($field === 'price')
				$_POST[$field] = !empty($_POST[$field]) ? abs($_POST[$field]) : 0;
			elseif (empty($_POST[$field]))
				fatal_error($field . ' ' . $txt['battle_Quest_error1'], false);
			else
				$_POST[$field] = $smcFunc['htmlspecialchars']($_POST[$field], ENT_QUOTES);
		}

		if (empty($context['cust']['id']))
		{
			$smcFunc['db_insert']('insert',
				'{db_prefix}battle_explore',
				array('outcome1' => 'string', 'outcome2' => 'string', 'outcome1_reward' => 'int', 'outcome2_reward' => 'int', 'outcome1_action' => 'string', 'outcome2_action' => 'string', 'start' => 'string', 'price' => 'int'),
				array($_POST['outcome1'], $_POST['outcome2'], $_POST['outcome1_reward'], $_POST['outcome2_reward'], $_POST['outcome1_action'], $_POST['outcome2_action'], $_POST['start'], $_POST['price']),
				array('id_explore')
			);
		}
		else
		{
			$smcFunc['db_insert']('replace',
				'{db_prefix}battle_explore',
				array('id_explore' => 'int', 'outcome1' => 'string', 'outcome2' => 'string', 'outcome1_reward' => 'int', 'outcome2_reward' => 'int', 'outcome1_action' => 'string', 'outcome2_action' => 'string', 'start' => 'string', 'price' => 'int'),
				array($context['cust']['id'], $_POST['outcome1'], $_POST['outcome2'], $_POST['outcome1_reward'], $_POST['outcome2_reward'], $_POST['outcome1_action'], $_POST['outcome2_action'], $_POST['start'], $_POST['price']),
				array('id_explore')
			);
		}

		redirectexit('action=admin;area=battle;sa=custom');
	}

	if (!empty($context['cust']['id']))
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_explore, outcome1, outcome1_reward, outcome2, outcome2_reward, outcome2_action, outcome1_action, start, price
			FROM {db_prefix}battle_explore
			WHERE id_explore = {int:id_cust}',
			array('id_cust' => $context['cust']['id'],)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$context['cust'] += array(
				'id_explore' => $row['id_explore'],
				'outcome1' => $row['outcome1'],
				'outcome1_reward' => $row['outcome1_reward'],
				'outcome2' => $row['outcome2'],
				'outcome2_reward' => $row['outcome2_reward'],
				'outcome2_action' => $row['outcome2_action'],
				'outcome1_action' => $row['outcome1_action'],
				'start' => $row['start'],
				'price' => $row['price']
			);
		}

		$smcFunc['db_free_result']($request);
	}
	else
	{
		$context['cust'] += array(
		        'id_explore' => '',
			'outcome1' => '',
			'outcome2' => '',
			'outcome1_reward' => '',
			'outcome2_reward' => '',
			'outcome1_action' => '',
			'outcome2_action'=> '',
			'start' => '',
			'price' => '',
		);
	}

	$context['sub_template'] = 'custEdit_Add';
	$context['page_title'] = $txt['battle_ce'];
}
?>