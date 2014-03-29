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

function battle_quests()
{
	global $context, $txt, $smcFunc, $scripturl;
	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$context['page_title'] = $txt['battle_Quest'];
	$context['sub_template'] = 'battle_quest';
	$context['battle_diaplay'] = array('page' => false, 'pages' => '0');
	$campaigns = battle_campaigns_list(0, 0);
	// $campaignNames = array_keys($campaigns);
	$context['battle_quest'] = array();
	$request = $smcFunc['db_query']('', '
		SELECT b.id_quest, b.gold, b.itext, b.stext, b.ftext, b.stext, b.exp, b.level,
		b.success, b.name, b.plays, b.energy, b.max_penalty, b.max_gain, b.limit, b.campaign_id
		FROM {db_prefix}battle_quest AS b
		ORDER BY campaign_id AND name DESC'
	);

	// Loop through all results
	// And add them to the list
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$context['battle_quests'][] = array(
			'id_quest' => $row['id_quest'],
			'name' => $row['name'],
			'itext' => $row['itext'],
			'ftext' => $row['ftext'],
			'stext' => $row['stext'],
			'exp' => $row['exp'],
			'level' => $row['level'],
			'success' => $row['success'],
			'gold' => $row['gold'],
			'plays' => $row['plays'],
			'energy' => $row['energy'],
			'max_penalty' => $row['max_penalty'],
			'max_gain' => $row['max_gain'],
			'limit' => $row['limit'],
			'campaign_id' => $row['campaign_id'],
			'campaign' => $row['campaign_id'] > 0 ? $campaigns[$row['campaign_id']]['campaign_name'] : $txt['battle_campaign_none'],
		);

	$smcFunc['db_free_result']($request);
	$context['battle_quest'] = battle_pagination($context['battle_quests'], 10);
	$context['battle_display'] = battle_pages($txt['battle_page'], false, $scripturl . '?action=admin;area=battle;sa=quest;', $context['current_pages']);
}

function battle_quest_delete()
{
	global $smcFunc, $txt;
	if (!allowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$quest_id = !empty($_REQUEST['quest']) ? (int) $_REQUEST['quest'] : 0;

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}battle_quest
		WHERE id_quest = {int:q_id}',
		array('q_id' => $quest_id,)

	);

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}battle_quest_champs
		WHERE id_quest = {int:q_id}',
		array('q_id' => $quest_id,)

	);

	redirectexit('action=admin;area=battle;sa=quest');
}

function battle_quest_add_edit()
{
	global $smcFunc, $context, $txt;

	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$check_quests = array();
	$plays = 0;
	$context['quest']['id'] = !empty($_REQUEST['quest']) ? (int) $_REQUEST['quest'] : 0;
	$context['battle_campaigns'] = battle_campaigns_list(0, 0);
	$zeroes = array('energy', 'min_gold', 'gold', 'min_exp', 'exp', 'hp', 'max_penalty', 'max_gain', 'campaign_id');

	if (!empty($_POST['submit']))
	{
		checkSession();
		$fields = array('name', 'min_gold', 'gold', 'itext', 'stext', 'ftext', 'min_exp', 'exp', 'energy', 'success', 'hp', 'max_penalty', 'max_gain', 'limit', 'campaign_id');
		foreach ($fields as $field)
		{
			if (($field === 'campaign_id') && ((int)$_POST['campaign_id'] < 1 || (int)$_POST['campaign_id'] > 24))
				$_POST['campaign_id'] = 0;

			if (in_array($field, $zeroes))
				$_POST[$field] = !empty($_POST[$field]) ? abs($_POST[$field]) : 0;
			elseif ($field === 'limit' || $field === 'success')
			{
				if ((empty($_POST[$field])) || $_POST[$field] < 1)
					$_POST[$field] = 1;
			}
			elseif (empty($_POST[$field]))
				fatal_error($field . ' ' . $txt['battle_Quest_error1'], false);
			elseif ($field !== 'name' && strpos($field, 'text') === false)
				$_POST[$field] = (int)$_POST[$field];
			else
				$_POST[$field] = $smcFunc['htmlspecialchars']($_POST[$field], ENT_QUOTES);
		}

		// Make it a value between 1 and 0 checked = 1
		$_POST['is_final'] = isset($_POST['is_final'])? 1 : 0;

		$request = $smcFunc['db_query']('', '
			SELECT b.id_quest, b.campaign_id, b.level
			FROM {db_prefix}battle_quest AS b
			WHERE id_quest != {int:id_quest} AND id_quest > 0',
			array('id_quest' => $context['quest']['id'])
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$check_quests[] = array('campaign_id' => $row['campaign_id'], 'level' => $row['level']);
		}

		$smcFunc['db_free_result']($request);

		foreach ($check_quests as $quest)
		{
			if ($quest['campaign_id'] == $_POST['campaign_id'] && $quest['level'] == $_POST['level'] && $_POST['campaign_id'] > 0)
			{
				$_POST['campaign_id'] = 0;
				fatal_error($txt['battle_Quest_error2'], false);
			}

		}

		if (empty($context['quest']['id']))
		{
			$smcFunc['db_insert']('insert',
				'{db_prefix}battle_quest',
				array('name' => 'string', 'min_gold' => 'int', 'gold' => 'int', 'itext' => 'string', 'stext' => 'string', 'ftext' => 'string', 'min_exp' => 'int', 'exp' => 'int', 'energy' => 'int', 'success' => 'int', 'level' => 'int', 'is_final' => 'int', 'hp' => 'int', 'plays' => 'int', 'max_penalty' => 'int', 'max_gain' => 'int', 'limit' => 'int', 'campaign_id' => 'int'),
				array($_POST['name'], $_POST['min_gold'], $_POST['gold'], $_POST['itext'], $_POST['stext'], $_POST['ftext'], $_POST['min_exp'], $_POST['exp'], $_POST['energy'], $_POST['success'], $_POST['level'], $_POST['is_final'], $_POST['hp'], 0, $_POST['max_penalty'], $_POST['max_gain'], $_POST['limit'], $_POST['campaign_id']),
				array()
			);
		}
		else
		{
			$request = $smcFunc['db_query']('', '
				SELECT id_quest, plays
				FROM {db_prefix}battle_quest
				WHERE id_quest = {int:id_quest}',
				array('id_quest' => $context['quest']['id'])
			);

			while ($row = $smcFunc['db_fetch_assoc']($request))
				$plays = $row['plays'];

			$smcFunc['db_free_result']($request);

			$smcFunc['db_insert']('replace',
				'{db_prefix}battle_quest',
				array('id_quest' => 'int', 'name' => 'string', 'min_gold' => 'int', 'gold' => 'int', 'itext' => 'string', 'stext' => 'string', 'ftext' => 'string', 'min_exp' => 'int', 'exp' => 'int', 'energy' => 'int', 'success' => 'int', 'level' => 'int', 'is_final' => 'int', 'hp' => 'int', 'plays' => 'int', 'max_penalty' => 'int', 'max_gain' => 'int', 'limit' => 'int', 'campaign_id' => 'int'),
				array($context['quest']['id'], $_POST['name'], $_POST['min_gold'], $_POST['gold'], $_POST['itext'], $_POST['stext'], $_POST['ftext'], $_POST['min_exp'], $_POST['exp'], $_POST['energy'], $_POST['success'], $_POST['level'], $_POST['is_final'], $_POST['hp'], $plays, $_POST['max_penalty'], $_POST['max_gain'], $_POST['limit'], $_POST['campaign_id']),
				array('id_quest')
			);
		}

		redirectexit('action=admin;area=battle;sa=quest');
	}

	if (!empty($context['quest']['id']))
	{
		$request = $smcFunc['db_query']('', '
			SELECT b.id_quest, b.gold, b.itext, b.stext, b.ftext, b.stext, b.min_exp, b.max_penalty, b.hp, b.campaign_id,
			b.exp, b.level, b.success, b.name, b.plays, b.energy, b.is_final, b.min_gold, b.max_gain, b.limit
			FROM {db_prefix}battle_quest AS b
			WHERE id_quest = {int:id_quest}',
			array('id_quest' => $context['quest']['id'])
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$context['quest'] += array(
				'id_quest' => $row['id_quest'],
				'name' => $row['name'],
				'itext' => $row['itext'],
				'ftext' => $row['ftext'],
				'stext' => $row['stext'],
				'exp' => $row['exp'],
				'level' => $row['level'],
				'success' => $row['success'],
				'gold' => $row['gold'],
				'plays' => $row['plays'],
				'energy' => $row['energy'],
				'is_final' => $row['is_final'],
				'min_gold' => $row['min_gold'],
				'min_exp' => $row['min_exp'],
				'max_penalty' => $row['max_penalty'],
				'max_gain' => $row['max_gain'],
				'limit' => $row['limit'],
				'hp' => $row['hp'],
				'campaign_id' => $row['campaign_id'],
			);
		}

		$smcFunc['db_free_result']($request);
	}
	else
	{
		$context['quest'] += array(
		        'id_quest' => '',
			'name' => '',
			'itext' => '',
			'ftext' => '',
			'stext' => '',
			'exp' => '',
			'level'=> '',
			'success' => '',
			'gold' => '',
			'plays' => '',
			'energy' => '',
			'is_final' => '',
			'min_gold' => '',
			'min_exp' => '',
			'max_penalty' => '',
			'max_gain' => '',
			'limit' => '',
			'hp' => '',
			'campaign_id' => 0,
		);
	}

	$context['sub_template'] = 'questEdit_Add';
	$context['page_title'] = $txt['battle_Quest'];
}

function battle_campaign_prune()
{
	global $smcFunc, $txt, $context;

	$campaignId = !empty($_REQUEST['id_campaign']) ? (int) $_REQUEST['id_campaign'] : 0;
	$table = 'battle_campaign_' . $campaignId;
	$quests = array();
	$level = array();

	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	if (($campaignId < 1) || !battle_check_table_exists($table))
		fatal_error($txt['battle_campaign_error2'], false);

	$request = $smcFunc['db_query']('', '
			SELECT id_quest, campaign_id, level
			FROM {db_prefix}battle_quest
			WHERE campaign_id = {int:campaign}
			ORDER BY level ASC',
			array('campaign' => $campaignId)
		);

	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$quests[] = $row['id_quest'];
		$level[] = $row['level'];
	}

	$smcFunc['db_free_result']($request);

	foreach ($quests as $quest)
	{
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}battle_quest_champs
			WHERE id_quest = {int:quest}',
			array('quest' => $quest)
		);
	}

	$smcFunc['db_query']('', "
		DELETE FROM {db_prefix}{$table}
		WHERE id_warrior >= {int:warrior}",
		array('warrior' => 1)
	);

	if (!empty($level))
		$smcFunc['db_query']('', "
			UPDATE {db_prefix}{$table}
			SET quest_completions = {int:level}
			WHERE id_warrior = {int:warrior}",
			array('warrior' =>  0, 'level' => $level[0])
			);

	$smcFunc['db_query']('', "
		UPDATE {db_prefix}{$table}
		SET start_time = {int:start}
		WHERE id_warrior = {int:warrior}",
		array('warrior' =>  0, 'start' => 0)
	);

	$_SESSION['battle_campaign_prune'] = $campaignId;
	redirectexit('action=admin;area=battle;sa=campaigns');
}

function battle_campaign_delete()
{
	global $smcFunc, $txt;

	$campaignId = !empty($_REQUEST['id_campaign']) ? (int) $_REQUEST['id_campaign'] : 0;
	$quests = array();

	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	if ($campaignId < 1 || $campaignId > 24)
		fatal_error($txt['battle_campaign_error1'], false);

	db_extend('packages');
	$smcFunc['db_drop_table'] ('{db_prefix}battle_campaign_' . $campaignId);
	unset($_SESSION['battle_campaign_' . $campaignId]);

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}permissions
		WHERE permission = {string:campaign_id}',
		array(
			'campaign_id' => 'battle_campaign_' . $campaignId,
		)
	);

	$request = $smcFunc['db_query']('', '
			SELECT id_quest, campaign_id
			FROM {db_prefix}battle_quest
			WHERE campaign_id = {int:campaign}
			ORDER BY id_quest',
			array('campaign' => $campaignId)
		);

	while ($row = $smcFunc['db_fetch_assoc']($request))
		$quests[] = $row['id_quest'];

	$smcFunc['db_free_result']($request);

	foreach ($quests as $quest)
	{
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}battle_quest_champs
			WHERE id_quest = {int:quest}',
			array('quest' => $quest)
		);

		$smcFunc['db_query']('', "
			UPDATE {db_prefix}battle_quest
			SET campaign_id = {int:num}
			WHERE id_quest = {int:quest}",
			array('quest' => $quest, 'num' => 0)
		);
	}



	redirectexit('action=admin;area=battle;sa=campaigns');
}

function battle_campaigns($per_page = 10)
{
	global  $smcFunc, $scripturl, $txt, $context;

	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$context['sub_template'] = 'battle_campaigns';
	$context['page_title'] = $txt['battle_campaign_title'];
	$context['battle_campaign'] = array();
	$context['battle_diaplay'] = array('page' => false, 'pages' => '0');
	$page = $context['current_page'] * $per_page;

	$campaigns = battle_campaigns_list(0, 0);

	if (!empty($_SESSION['battle_campaign_prune']))
	{
		$context['campaign_pruned'][$_SESSION['battle_campaign_prune']] = $txt['battle_campaign_pruned'];
		$_SESSION['battle_campaign_prune'] = false;
	}

	$context['campaign'] = battle_pagination(battle_campaigns_data(array_keys($campaigns)), $per_page);
	$context['battle_display'] = battle_pages($txt['battle_page'], false, $scripturl . '?action=admin;area=battle;sa=campaigns;', $context['current_pages']);
}

function battle_campaign_add()
{
	global $smcFunc, $txt;

	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$campaign = battle_campaigns_list(0, 0);
	$id = 0;

	if (count($campaign) > 23)
		fatal_error($txt['battle_campaign_exceed'], false);

	db_extend('packages');

	foreach ($campaign as $key => $data)
	{
		$id++;
		$campaignId = $id+1;
		if ($id != $data['id_campaign'])
		{
			$campaignId = $id;
			break;
		}
	}

	if (empty($campaignId))
		$campaignId = 1;

	$smcFunc['db_create_table']('{db_prefix}battle_campaign_' . $campaignId,
	array(
		array(
			'name' => 'id_warrior',
			'type' => 'int',
                        'size' => 10,
			'unsigned' => true,
			'null' => false,
			'auto' => false
		),
		array(
			'name' => 'id_campaign',
			'type' => 'int',
                        'size' => 10,
			'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'campaign_name',
			'type' => 'varchar',
                        'size' => 255,
			'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'score',
			'type' => 'int',
                        'size' => 10,
			'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'start_time',
			'type' => 'int',
                        'size' => 10,
			'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'end_time',
			'type' => 'int',
                        'size' => 10,
			'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'timed_campaign',
			'type' => 'int',
                        'size' => 10,
			'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'level_completion',
			'type' => 'int',
                        'size' => 10,
			'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'quest_completions',
			'type' => 'int',
                        'size' => 10,
			'unsigned' => true,
			'null' => false
		),
                array(
			'name' => 'image',
			'type' => 'varchar',
                        'default' => 'blank.gif',
                        'size' => 255,
			'unsigned' => true,
			'null' => false
		),
	),
	array(
		array(
			'type' => 'primary',
			'columns' => array('id_warrior')
		),
	),
		array(),
	'ignore');

	    $smcFunc['db_insert']('replace',
        '{db_prefix}battle_campaign_' . $campaignId,
	array(
            'id_warrior' => 'int',
            'id_campaign' => 'int',
            'campaign_name' => 'string',
            'score' => 'int',
            'start_time' => 'int',
            'end_time' => 'int',
            'timed_campaign' => 'int',
            'level_completion' => 'int',
            'quest_completions' => 'int',
            'image' => 'string'
	),
        array(
            'id_warrior' => 0,
            'id_campaign' => $campaignId,
            'campaign_name' => $txt['battle_campaign_id'] . '_' . $campaignId,
            'score' => 0,
            'start_time' => 0,
            'end_time' => 0,
            'timed_campaign' => 0,
            'level_completion' => 0,
            'quest_completions' => 0,
            'image' => 'blank.gif'
        ),
	array('id_warrior')
    );

	redirectexit('action=admin;area=battle;sa=edit_campaign;id_campaign=' . $campaignId);
}

function battle_campaign_edit()
{
	global $smcFunc, $context, $txt;

	if (!AllowedTo('admin_battle'))
		fatal_error($txt['battle_admin_error1'], false);

	$context['campaign_id'] = !empty($_REQUEST['id_campaign']) ? (int)$_REQUEST['id_campaign'] : 0;
	$context['campaign_images'] = getImageList('campaign');
	$_SESSION['battle_campaign_' . $context['campaign_id']] = false;

	isAllowedTo('admin_battle');
	$dates = array('start_date', 'start_hours', 'end_date', 'end_hours');
	list($startTime, $endTime) = array(false, false);

	if (!battle_check_table_exists('battle_campaign_' . $context['campaign_id']))
		fatal_error($txt['battle_campaign_timed_error4'], false);

	if (!empty($_POST['submit']))
	{
		checkSession();
		$fields = array('id_campaign', 'campaign_name', 'timed_campaign', 'level_completion', 'image', 'start_date', 'start_hours', 'end_date', 'end_hours');
		$ints = array('id_campaign', 'timed_campaign', 'level_completion');
		// Format inputs
		foreach ($fields as $field)
		{
			if ($field === 'campaign_name' && empty($_POST['campaign_name']))
				fatal_error($field . ' ' . $txt['battle_Quest_error1'], false);
			elseif (in_array($field, $dates) && empty($_POST[$field]))
				$_POST[$field] = '';
			elseif ($field === 'image' && empty($_POST['image']))
				$_POST['image'] = 'blank.gif';
			elseif (empty($_POST[$field]))
				$_POST[$field] = 0;
			elseif (!in_array($field, $ints))
				$_POST[$field] = $smcFunc['htmlspecialchars']($_POST[$field], ENT_QUOTES);
			else
				$_POST[$field] = (int)$_POST[$field];
		}

		// Format date/time inputs to timestamp
		if (!empty($_POST['timed_campaign']))
		{
			foreach ($dates as $field)
			{
				switch($field)
				{
					case 'start_date':
						@list($day, $month, $year) = explode('/', $_POST['start_date']);
						if (!checkdate((int)$month, (int)$day, (int)$year))
							fatal_error('start_date ' . $txt['battle_Quest_error1'], false);
						$_POST['start_date'] = implode('-', array($year,$month,$day));
						$startTime = strtotime($_POST['start_date']);
						continue 2;
					case 'start_hours':
						if (strpos(substr($_POST['start_hours'],0,2), ':') !== false && (substr((int)$_POST['start_hours'],0,1) > 0 && substr((int)$_POST['start_hours'],0,1) <= 9))
							$_POST['start_hours'] = '0' . $_POST['start_hours'];
						if (!preg_match("/(2[0-3]|[01][0-9]):[0-5][0-9]/", $_POST['start_hours']))
							fatal_error('start_hours ' . $txt['battle_Quest_error1'], false);
						$_POST['start_hours'] = explode(':', $_POST['start_hours']);
						$startTime = ($startTime + (3600*$_POST['start_hours'][0]) + (60*$_POST['start_hours'][1]));
						if ($startTime - time() < 300)
							fatal_error($txt['battle_campaign_timed_error2'], false);
						continue 2;
					case 'end_date':
						@list($day, $month, $year) = explode('/', $_POST['end_date']);
						if (!checkdate((int)$month, (int)$day, (int)$year))
							fatal_error('end_date ' . $txt['battle_Quest_error1'], false);
						$_POST['end_date'] = implode('-', array($year,$month,$day));
						$endTime = strtotime($_POST['end_date']);
						continue 2;
					case 'end_hours':
						if (strpos(substr($_POST['end_hours'],0,2), ':') !== false && (substr((int)$_POST['end_hours'],0,1) > 0 && substr((int)$_POST['end_hours'],0,1) <= 9))
							$_POST['end_hours'] = '0' . $_POST['end_hours'];
						if (!preg_match("/(2[0-3]|[01][0-9]):[0-5][0-9]/", $_POST['end_hours']))
							fatal_error('end_hours ' . $txt['battle_Quest_error1'], false);
						$_POST['end_hours'] = explode(':', $_POST['end_hours']);
						$endTime = ($endTime + (3600*$_POST['end_hours'][0]) + (60*$_POST['end_hours'][1]));
						if ($endTime - time() < 3600)
							fatal_error($txt['battle_campaign_timed_error3'], false);
						continue 2;
				}
			}
		}

		if (!empty($startTime) && !empty($endTime) && (int)$endTime - (int)$startTime < 3600)
			fatal_error($txt['battle_campaign_timed_error1'], false);

		$campaigns = battle_campaigns_data(array($context['campaign_id']), 0);
		$campaigns = $campaigns[$context['campaign_id']];

		if (empty($campaigns['campaign_name']))
		{
			$smcFunc['db_insert']('insert',
				'{db_prefix}' . 'battle_campaign_' . $context['campaign_id'],
				array('id_warrior' => 'int', 'id_campaign' => 'int', 'campaign_name' => 'string', 'score' => 'int', 'start_time' => 'int', 'end_time' => 'int', 'timed_campaign' => 'int', 'level_completion' => 'int', 'quest_completions' => 'int', 'image' => 'string'),
				array(0, $context['campaign_id'], $_POST['campaign_name'], 0, (int)$startTime, (int)$endTime, (int)$_POST['timed_campaign'], (int)$_POST['level_completion'], 0, $_POST['image']),
				array('id_warrior')
			);
		}
		else
		{
			$smcFunc['db_insert']('replace',
				'{db_prefix}' . 'battle_campaign_' . $context['campaign_id'],
				array('id_warrior' => 'int', 'id_campaign' => 'int', 'campaign_name' => 'string', 'score' => 'int', 'start_time' => 'int', 'end_time' => 'int', 'timed_campaign' => 'int', 'level_completion' => 'int', 'quest_completions' => 'int', 'image' => 'string'),
				array(0, $context['campaign_id'], $_POST['campaign_name'], 0, (int)$startTime, (int)$endTime, (int)$_POST['timed_campaign'], (int)$_POST['level_completion'], 0, $_POST['image']),
				array('id_warrior')
			);
		}

		redirectexit('action=admin;area=battle;sa=campaigns');
	}

	if (!empty($context['campaign_id']))
	{
		$campaigns = battle_campaigns_data(array($context['campaign_id']), 0);
		$context['campaign'] = $campaigns[$context['campaign_id']];
		$context['campaign']['start_date'] = !empty($context['campaign']['start_time']) ? date('d/m/y', $context['campaign']['start_time']) : '';
		$context['campaign']['start_hours'] = !empty($context['campaign']['start_time']) ? date('H:i', $context['campaign']['start_time']) : '';
		$context['campaign']['end_date'] = !empty($context['campaign']['end_time']) ? date('d/m/y', $context['campaign']['end_time']) : '';
		$context['campaign']['end_hours'] = !empty($context['campaign']['end_time']) ? date('H:i', $context['campaign']['end_time']) : '';
		$context['campaign']['current_time'] = $txt['battle_campaign_currentDate'] . ' ' . date('d/m/y', time()) . '&nbsp;&nbsp;' . $txt['battle_campaign_currentTime'] . ' ' . date('H:i', time());

		if (time() < $context['campaign']['start_time'] && $context['campaign']['timed_campaign'] > 0)
			$context['campaign']['current_status'] = $txt['battle_campaign_isPending'];
		elseif (time() >= $context['campaign']['start_time'] && time() <= $context['campaign']['end_time'] && $context['campaign']['timed_campaign'] > 0)
			$context['campaign']['current_status'] = $txt['battle_campaign_isCurrent'];
		elseif ($context['campaign']['timed_campaign'] > 0)
			$context['campaign']['current_status'] = $txt['battle_campaign_isExpired'];
		else
			$context['campaign']['current_status'] = $txt['battle_campaign_na'];

		$context['campaign']['membergroups'] = battle_campaign_membergroups('campaign_' . (int)$context['campaign']['id_campaign']);
		$context['campaign']['membergroups'] = $context['campaign']['membergroups'][0];
	}
	else
		fatal_error($txt['battle_campaign_timed_error4'], false);

	$context['sub_template'] = 'campaignEdit';
	$context['page_title'] = $txt['battle_campaign_title'];
}
?>