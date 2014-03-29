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

function battle_check_campaign($campaign, $level, $current, $next, $warrior, $lvlComp, $check = false)
{
	// Check time setting and level for quest in a specific campaign
	global $user_info, $txt;

	if (empty($campaign['id_campaign']))
		return false;

	if (!AllowedTo('battle_campaign_' . (int)$campaign['id_campaign']))
		$check = $txt['battle_quest_error1'];
	elseif (time() < $campaign['start_time'] && $campaign['timed_campaign'] > 0)
		$check = $txt['battle_quest_error2'];
	elseif (time() >= $campaign['end_time'] && $campaign['timed_campaign'] > 0)
		$check = $txt['battle_quest_error3'];
	elseif($level > $current && $campaign['level_completion'] > 0)
		$check = $txt['battle_quest_error4'];
	elseif($level < $lvlComp && $campaign['level_completion'] > 0)
		$check = $txt['battle_quest_error6'];
	elseif ($warrior['quest_completions'] == $campaign['quest_completions'] && (int)$warrior['level_completion'] != $warrior['quest_completions'] && $campaign['level_completion'] > 0)
		$check = $txt['battle_quest_error6'];
	elseif ($warrior['quest_completions'] > $campaign['quest_completions']  && $campaign['level_completion'] > 0)
		$check = $txt['battle_quest_error4'];
	elseif ($warrior['quest_completions'] < $lvlComp  && $campaign['level_completion'] > 0)
		$check = $txt['battle_quest_error7'];

	return $check;
}

function battle_campaigns_query($id_campaign = 0)
{
	global $smcFunc, $txt;
	$defaults = array($txt['battle_campaign_alpha'], $txt['battle_campaign_beta'], $txt['battle_campaign_gamma']);
	$battle_campaigns = array();
	$campaignCount = array();

	// Is this a specific campaign or all of them?
	if ($id_campaign < 1)
	{
		for ($i=1; $i<25; $i++)
		{
			if (battle_check_table_exists('battle_campaign_' . $i))
				$campaignCount[$i] = 'battle_campaign_' . $i;
		}
	}
	else
		$campaignCount[$id_campaign] = 'battle_campaign_' . $id_campaign;

	foreach ($campaignCount as $key => $campaign)
	{
		$quests = array();

		if ($key > 2)
			$default[$key] = $txt['battle_campaign_id'] . '_' . ($key+1);

		$request = $smcFunc['db_query']('', "
			SELECT id_quest, level
			FROM {db_prefix}battle_quest
			WHERE campaign_id = {int:camp}
			ORDER BY level ASC",
			array('camp' => (int)$key)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
			$quests[] = array(
				'id_quest' => $row['id_quest'],
				'level' => $row['level']
			);

		$smcFunc['db_free_result']($request);

		$request = $smcFunc['db_query']('', "
			SELECT id_warrior, id_campaign, campaign_name, score, start_time,
			end_time, timed_campaign, level_completion, quest_completions, image
			FROM {db_prefix}{raw:thiscampaign}
			WHERE id_campaign = {int:campaign} AND id_warrior >= {int:warrior}
			ORDER BY score AND id_warrior ASC",
			array('campaign' => $key, 'warrior' => 1, 'thiscampaign' => $campaign)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
			$battle_campaigns[] = array(
				'id_warrior' => $row['id_warrior'],
				'id_campaign' => $row['id_campaign'],
				'campaign_name' => !empty($row['campaign_name']) ? $row['campaign_name'] : $defaults[$key],
				'score' => $row['score'],
				'start_time' => $row['start_time'],
				'end_time' => $row['end_time'],
				'timed_campaign' => $row['timed_campaign'],
				'level_completion' => $row['level_completion'],
				'quest_completions' => $row['quest_completions'],
				'img' => !empty($row['image']) ? $row['image'] : 'blank.gif',
				'quests' => $quests,
				'membergroups' => !empty($row['id_campaign']) ? battle_campaign_membergroups('campaign_' . (int)$row['id_campaign']) : false
			);

		$smcFunc['db_free_result']($request);
	}

	return $battle_campaigns;
}

function battle_campaigns_list($id_warrior, $campaign = 0)
{
	global $smcFunc, $txt, $context;
	$defaults = array($txt['battle_campaign_alpha'], $txt['battle_campaign_beta'], $txt['battle_campaign_gamma']);
	$battle_campaigns = array();
	$campaignCount = array();
	$campaignx = abs((int)$campaign);
	$id_warrior = !empty($id_warrior) ? (int)$id_warrior : 0;
	$timedCamp = -1;

	if (!empty($campaignx) && battle_check_table_exists('battle_campaign_' . $campaignx))
	{
		$campaignCount[$campaignx] = 'battle_campaign_' . $campaignx;
		$checkTimed = !empty($_COOKIE['battle_camp_timed_' . $campaignx]) ? (int)$_COOKIE['battle_camp_timed_' . $campaignx] : false;
		if (empty($checkTimed))
		{
			$request = $smcFunc['db_query']('', "
				SELECT timed_campaign
				FROM {db_prefix}{raw:campaign}
				WHERE id_warrior = {int:nowarrior}
				LIMIT 1",
				array('nowarrior' => 0, 'campaign' => 'battle_campaign_' . $campaignx)
			);
			while ($row = $smcFunc['db_fetch_assoc']($request))
				$timedCamp = $row['timed_campaign'];

			$smcFunc['db_free_result']($request);

			$checkTimed = $timedCamp;
			setcookie('battle_camp_timed_' . $campaignx, $checkTimed, time()+3600, '/');
		}
		else
			$timedCamp = $checkTimed;
	}
	elseif(!empty($campaignx))
	    return array();
	else
	{
		for ($i=1; $i<25; $i++)
		{
			if (battle_check_table_exists('battle_campaign_' . $i))
				$campaignCount[$i] = 'battle_campaign_' . $i;
		}
	}

	foreach ($campaignCount as $key => $campaign)
	{
		if ($key > 2)
			$default[$key] = $txt['battle_campaign_id'] . '_' . ($key+1);

		$request = $smcFunc['db_query']('', "
			SELECT id_warrior, id_campaign, campaign_name, score, start_time,
			end_time, timed_campaign, level_completion, quest_completions, image
			FROM {db_prefix}{raw:campaign}
			WHERE id_warrior = {int:warrior}
			ORDER BY id_campaign AND level_completion DESC",
			array('warrior' => $id_warrior, 'campaign' => $campaign)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
			$battle_campaigns[$row['id_campaign']] = array(
				'id_campaign' => $row['id_campaign'],
				'campaign_name' => !empty($row['campaign_name']) ? $row['campaign_name'] : $defaults[$key],
				'score' => $row['score'],
				'start_time' => $row['start_time'],
				'end_time' => $row['end_time'],
				'timed_campaign' => (int)$timedCamp > -1 ? $timedCamp : $row['timed_campaign'],
				'level_completion' => $row['level_completion'],
				'quest_completions' => $row['quest_completions'],
				'img' => !empty($row['image']) ? $row['image'] : 'blank.gif',
				'membergroups' => !empty($row['id_campaign']) ? battle_campaign_membergroups('campaign_' . (int)$row['id_campaign']) : false
			);

		$smcFunc['db_free_result']($request);
	}

	return $battle_campaigns;
}

function battle_campaigns_data($campaigns, $id_warrior = 0)
{
	global $smcFunc, $txt;

	$battle_campaigns = array();
	$defaults = array($txt['battle_campaign_alpha'], $txt['battle_campaign_beta'], $txt['battle_campaign_gamma']);
	$campaigns = !empty($campaigns) ? $campaigns : array(1, 2, 3);

	foreach ($campaigns as $key => $campaign)
	{
		if ($key > 2)
			$default[$key] = $txt['battle_campaign_id'] . '_' . ($key+1);

		$table = 'battle_campaign_' . $campaign;
		$request = $smcFunc['db_query']('', "
			SELECT id_warrior, id_campaign, campaign_name, score, start_time,
			end_time, timed_campaign, level_completion, quest_completions, image
			FROM {db_prefix}{raw:table}
			WHERE id_warrior = {int:warrior}
			ORDER BY id_campaign ASC",
			array('warrior' => $id_warrior, 'table' => $table)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
			$battle_campaigns[$campaign] = array(
				'id_warrior' => $row['id_warrior'],
				'id_campaign' => $row['id_campaign'],
				'campaign_name' => !empty($row['campaign_name']) ? $row['campaign_name'] : $defaults[$key],
				'score' => $row['score'],
				'start_time' => $row['start_time'],
				'end_time' => $row['end_time'],
				'timed_campaign' => $row['timed_campaign'],
				'level_completion' => !empty($row['level_completion']) ? $row['level_completion'] : 0,
				'quest_completions' => !empty($row['quest_completions']) ? $row['quest_completions'] : 0,
				'img' => !empty($row['image']) ? $row['image'] : 'blank.gif'
			);

		$smcFunc['db_free_result']($request);
	}

	return $battle_campaigns;
}

function battle_campaign_membergroups($permission)
{
	global $smcFunc, $txt, $user_info;
	$groups = array();
	$groupIds = array();
	if (!$permission)
		return false;

	$checkPermStat = !empty($_SESSION['battle_perm_status_' . $user_info['id']]) ? $_SESSION['battle_perm_status_' . $user_info['id']] : false;
	if (empty($checkPermStat))
	{
		$request = $smcFunc['db_query']('', "
			SELECT p.id_group, p.permission, p.add_deny, m.group_name
			FROM {db_prefix}permissions as p
			LEFT JOIN {db_prefix}membergroups as m ON (m.id_group = p.id_group)
			WHERE permission = {string:perm} AND add_deny = {int:num}
			ORDER BY id_group ASC",
			array('perm' => 'battle_' . $permission, 'num' => 1)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			if (!in_array($row['group_name'], $groups))
				$groups[$row['id_group']] = $row['group_name'];

		}

		$smcFunc['db_free_result']($request);
		$checkPermStat = array(implode(', ', $groups), $groups);

		$_SESSION['battle_perm_status_' . $user_info['id']] = $checkPermStat;
	}

	return $checkPermStat;

}

function battle_check_campaigns($campaigns, $id)
{
   foreach($campaigns as $key => $campaign)
   {
      if ($campaign['id_campaign'] == (int)$id)
         return $campaigns[$key];
   }
   return array('level_completion' => 0, 'quest_completions' => 0);
}

function battle_nextLvl($campaigns, $id, $quest)
{
	global $user_info;

	$thisCampaign = battle_check_campaigns($campaigns, $id);
	if (!empty($thisCampaign['quests']))
	{
		$first = 0;
		foreach ($thisCampaign['quests'] as $key => $quest)
		{
			$first = !empty($first) ? $first : $quest['level'];
			if ($quest['id_quest'] == $id)
				$check = 1;
			elseif (!empty($check))
				return array('next_level' => $quest['level'], 'first_level' => $first);
		}

		return array('next_level' => $quest['level'], 'first_level' => $first);
	}

	return array('next_level' => -1, 'first_level' => -1);
}

/*  Check if campaign table exists  */
function battle_check_table_exists($table, $checkval=false)
{
	global $smcFunc, $db_prefix;

	$checkTableStat = !empty($_SESSION[$table]) ? $_SESSION[$table] : false;
	if (empty($checkTableStat))
	{
		$check = $smcFunc['db_query']('', "
			SHOW TABLE STATUS
			LIKE {string:table}",
			array('table' => $db_prefix . $table)
		);
		$checkval = $smcFunc['db_num_rows']($check);
		$smcFunc['db_free_result']($check);

		if (empty($checkval))
			$checkval = -1;

		$_SESSION[$table] = (int)$checkval;
	}
	else
		$checkval = $checkTableStat;

	if ((int)$checkval > 0)
		return true;

	return false;
}
?>