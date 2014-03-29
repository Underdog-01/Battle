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

function battle_quest($per_page = 5)
{
	global $context, $user_info, $scripturl, $modSettings, $smcFunc, $txt, $settings;

	if (empty($modSettings['battle_enable_quests']))
		fatal_error($txt['battle_error9'], false);

	$context['current_page'] = abs($context['current_page']);
	list($context['battle_quest'], $context['user_campaign'], $campaign, $campaign_lvl, $context['sub_template'], $context['page_title'], $completeWarriorLvls) = array(array(), array(), array(), array(), 'battle_quest', $txt['battle_Quest'], array());
	list($context['battle_message'], $context['battleNoBattle'], $limitQuest, $where, $currentCampaign, $currentLimit, $questLimit, $context['battle_display']) = array(false, false, false, false, 0, 2, 'LIMIT {int:page}, {int:per}', array('page' => false, 'pages' => '0'));
	$page = $context['current_page'] * $per_page;
	$currentQuest = !empty($_GET['go']) ? (int)$_GET['go'] : 0;
	$currentQuest = !empty($_GET['do']) ? (int)$_GET['do'] : $currentQuest;
	$context['campaigns_id'] = !empty($_REQUEST['id_campaign']) ? (int)$_REQUEST['id_campaign'] : 0;
	$thisPage = !empty($_COOKIE['battle_page']) ? (int)$_COOKIE['battle_page'] : 0;
	$campLink = 'id_campaign=' . $context['campaigns_id'] . ';';
	$link = 'current_page=' . $thisPage . ';' . $campLink . ';';
	$initiate = false;
	$campaignsQuery = battle_campaigns_query();

	foreach ($campaignsQuery as $key => $campaignQuery)
	{
		if ((!empty($campaignQuery['quest_completions'])) && $campaignQuery['quest_completions'] != $campaignQuery['level_completion'])
			$completeWarriorLvls[$campaignQuery['id_campaign']][] = $campaignQuery['quest_completions'];
	}

	if ($user_info['hp'] < 1)
		$context['battleNoBattle'] = $txt['battle_deadplease'];

	if (!empty($context['campaigns_id']))
	{
		$where = 'WHERE b.campaign_id = {int:campaign}';
		$currentCampaign = $context['campaigns_id'];
	}

	// For pages we do this... all this in case of 1000+ quests?!
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(id_quest) FROM {db_prefix}battle_quest
		WHERE id_quest');

	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);
	$nRows = array_shift(array_values($row));

	$request = $smcFunc['db_query']('', "
		SELECT b.id_quest, b.gold, b.itext, b.stext, b.ftext, b.stext, b.exp, b.is_final, b.min_gold, b.max_penalty, b.campaign_id,
		b.level, b.success, b.name, b.plays, b.energy, b.min_exp, b.hp, b.max_gain, b.limit,
		q.id_warrior, q.exp_points, q.warrior_gold, q.fail, q.complete
		FROM {db_prefix}battle_quest AS b
		LEFT JOIN {db_prefix}battle_quest_champs AS q ON (q.id_quest = b.id_quest AND q.id_warrior = {int:id_member})
		" . $where . "
		ORDER BY b.campaign_id, b.level, q.complete
		" . $questLimit,
		array(
			'id_member' => $user_info['id'],
			'page' => $page,
			'per' => $per_page,
			'campaign' => $currentCampaign,
			'limit' => $currentLimit
		)
	);

	// Loop through all results and add them to the list
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$context['camp_list'] = (!empty($row['campaign_id'])) ? battle_campaigns_list(0, $row['campaign_id']) : array();

		$campaign = !empty($row['campaign_id']) ? $context['camp_list'][$row['campaign_id']] : array('id_campaign' => 0, 'timed_campaign' => 0, 'level_completion' => 0, 'quest_completions' => 0, 'end_time' => 0, 'start_time' => 0, 'img' => false, 'campaign_name' => false);
		$currentLvlX = !empty($row['campaign_id']) ? battle_campaigns_list($user_info['id'], $campaign['id_campaign']) : array($row['campaign_id'] => 0);
		$currentLvlY = (!empty($currentLvlX[$row['campaign_id']]['level_completion']) ? (int)$currentLvlX[$row['campaign_id']]['level_completion'] : 0);
		$currentLvlZ = (!empty($row['campaign_id'])) ? battle_check_campaigns($campaignsQuery, $row['campaign_id']) : array('level_completion' => 0, 'quest_completions' => 0);
		$currentLvl = (!empty($row['campaign_id']) && empty($currentLvlY) && !empty($currentLvlZ['level_completion']) ? $currentLvlZ['level_completion'] : $currentLvlY);
		$userCampaign = $currentLvlX;

		$context['battle_quest'][$row['id_quest']] = array(
			'id_quest' => $row['id_quest'],
			'fail' => !empty($row['fail']) ? (int)$row['fail'] : 0,
			'complete' => !empty($row['complete']) ? (int)$row['complete'] : 0,
			'quest_id' => $row['id_quest'],
			'id_member' => $row['id_warrior'],
			'name' => $row['name'],
			'itext' => html_entity_decode($row['itext']),
			'ftext' => html_entity_decode($row['ftext']),
			'stext' => html_entity_decode($row['stext']),
			'exp' => $row['exp'],
			'level' => $row['level'],
			'success' => $row['success'],
			'gold' => $row['gold'],
			'warrior_gold' => $row['warrior_gold'],
			'plays' => $row['plays'],
			'energy' => $row['energy'],
			'is_final' => $row['is_final'],
			'min_gold' => $row['min_gold'],
			'min_exp' => $row['min_exp'],
			'hp' => $row['hp'],
			'max_penalty' => $row['max_penalty'],
			'max_gain' => $row['max_gain'],
			'limit' => !empty($row['limit']) ? (int)$row['limit'] : 1,
			'campaign_id' => !empty($row['campaign_id']) ? (int)$row['campaign_id'] : 0,
			'campaign_name' => !empty($row['campaign_id']) ? $campaign['campaign_name'] : false,
			'campaign_img' => !empty($row['campaign_id']) ? $settings['default_images_url'] . '/battle/campaign/' . $campaign['img'] : false,
			'level_completion' => (!empty($currentLvl) ? (int)$currentLvl : 0),
			'status' => (!empty($row['campaign_id']) && allowedTo('battle_campaign_' . $row['campaign_id']) && $user_info['level'] >= $row['level']) ? $txt['battle_quest_granted'] : $txt['battle_quest_denial'],
			'user_status' => !empty($currentLvlX[$row['campaign_id']]) ? $currentLvlX[$row['campaign_id']] : array(),
			'next_lvl' => (!empty($row['campaign_id'])) ? battle_nextLvl($campaignsQuery, $row['campaign_id'], $row['id_quest']) : 0,
			'quest_completions' => (!empty($row['campaign_id']) && !empty($campaign['quest_completions'])) ? (int)$campaign['quest_completions'] : 0,
		);

		$previous[$row['id_quest']] = array(
				'exp_points' => !empty($row['exp_points']) ? (int)$row['exp_points'] : 0,
				'warrior_gold' => !empty($row['gold']) ? (int)$row['gold'] : 0,
				'fail' => !empty($row['fail']) ? (int)$row['fail'] : 0,
				'complete' => !empty($row['complete']) ? (int)$row['complete'] : 0,
			);

		$campaign_lvl[$row['id_quest']] = array('campaign_id' => $context['battle_quest'][$row['id_quest']]['campaign_id'], 'level' => $context['battle_quest'][$row['id_quest']]['level'], 'limit' => $context['battle_quest'][$row['id_quest']]['limit'], 'start' => $campaign['start_time'], 'end' => $campaign['end_time']);
	}
	$smcFunc['db_free_result']($request);

	foreach ($campaignsQuery as $campaignQuery)
	{
		if (empty($campaignQuery['id_campaign']))
			continue;

		$table = 'battle_campaign_' . $campaignQuery['id_campaign'];
		$countMembers = 0;
		$bypass = false;

		if ((!empty($context['camp_list'][$campaignQuery['id_campaign']]['level_completion']) ? true : false))
		{
		    $groupIds = battle_campaign_membergroups('campaign_' . $campaignQuery['id_campaign']);
		    foreach ($groupIds[1] as $group_id => $group)
		    {
		        if ($group_id != 1)
		            $countMembers += count(battle_fetchMembergroups($group_id));
		    }

		    $bypass = (!empty($completeWarriorLvls[$campaignQuery['id_campaign']])) && (count($completeWarriorLvls[$campaignQuery['id_campaign']])) >= $countMembers ? true : false;
		}

		if ((!empty($completeWarriorLvls[$campaignQuery['id_campaign']])) && $bypass && count($completeWarriorLvls[$campaignQuery['id_campaign']]) != 0)
		{
			$smcFunc['db_query']('', "
				UPDATE {db_prefix}{raw:table}
				SET quest_completions = {int:complete}
				WHERE id_warrior = {int:num}",
				array('complete' => $campaignQuery['quest_completions'], 'num' => 0, 'table' => $table)
			);

			$smcFunc['db_query']('', "
				UPDATE {db_prefix}{raw:table}
				SET level_completion = {int:level}
				WHERE id_warrior >= {int:warrior}",
				array('level' => $campaignQuery['quest_completions'], 'warrior' => 1, 'table' => $table)
			);

			if($modSettings['enable_battle_hist'] && $campaignQuery['quest_completions'] != 0 && $campaignQuery['level_completion'] > 0)
			{
				$content = str_replace('&%$@!#', $campaignQuery['campaign_name'], $txt['battle_hist27']) . '&nbsp;' . $campaignQuery['quest_completions'];
				add_to_battle_hist($content);
			}

			redirectexit('action=battle;sa=quest;home;current_page=' . $thisPage . ';' . (!empty($context['campaigns_id']) ? 'id_campaign=' . $context['campaigns_id'] . ';' : '') . '#battle_main');
		}
	}

	foreach ($context['battle_quest'] as $quest)
	{
		$checkSession = 9 * ((!empty($quest['level']) ? $quest['level'] : 9) + (!empty($quest['success']) ? $quest['success'] : 9) + (!empty($quest['fail']) ? $quest['fail'] : 9) + (!empty($user_info['energy']) ? $user_info['energy'] : 9));
		$table = 'battle_campaign_' . $quest['campaign_id'];
		$context['battle_quest'][$quest['id_quest']]['active'] = false;
		$initiate[$quest['id_quest']] = false;
		$campaign['quest_completions'] = !empty($campaign['quest_completions']) ? $campaign['quest_completions'] : 0;
		$quest['quest_completions'] = $quest['quest_completions'];
		$context['battle_nextLvl'][$quest['id_quest']] = $quest['next_lvl']['next_level'];
		$quest['user_status']['quest_completions'] = !empty($quest['user_status']['quest_completions']) ? $quest['user_status']['quest_completions'] : 0;
		if ($quest['complete'] == $quest['limit'] && $quest['limit'] > 0)
			$context['battle_quest'][$quest['id_quest']]['status'] = $txt['battle_quest_finished'];

		if ((!empty($quest['campaign_id'])) && allowedTo('battle_campaign_' . $quest['campaign_id']) && empty($completeWarriorLvls) && empty($context['user_campaign']) && empty($_REQUEST['initiate']) && empty($initiate['q_' . $quest['campaign_id']]))
			$initiate = array('q_' . $quest['campaign_id'] => true, $quest['id_quest'] => '<a href="' . $scripturl . '?action=battle;sa=quest;id_campaign=' . $quest['campaign_id'] . ';initiate=1;home;#battle_main"><img style="position:relative;padding-top:5px;" src="' . $settings['default_images_url'] . '/battle/campaign/go_quest.gif" title="' . $txt['battle_camp_init'] . '" alt="[*]" /></a>');
		elseif ((!empty($quest['campaign_id'])) && allowedTo('battle_campaign_' . $quest['campaign_id']) && empty($completeWarriorLvls) && !empty($context['campaigns_id']))
		{
			$context['user_campaign'] = !empty($context['user_campaign']) ? $context['user_campaign'] : battle_campaigns_data(array($quest['campaign_id']), $user_info['id']);

			if (empty($campaign['quest_completions']))
				$smcFunc['db_query']('', "
					UPDATE {db_prefix}{raw:table}
					SET quest_completions = {int:level}
					WHERE id_warrior = {int:warrior}",
					array('warrior' =>  0, 'level' => $quest['level'], 'table' => $table)
					);

			if (empty($context['user_campaign']))
			{
				$firstLvl[$quest['campaign_id']]['level'] = 0;
				$currentLvl = array($quest['campaign_id'] => array('quest_completions' => 0));
				$table = 'battle_campaign_' . $quest['campaign_id'];

				$request = $smcFunc['db_query']('', '
						SELECT quest_completions, id_warrior
						FROM {db_prefix}{raw:table}
						WHERE id_warrior = {int:warrior}
						ORDER BY id_warrior ASC
						LIMIT 1',
						array('warrior' => 0, 'table' => 'battle_campaign_' . $quest['campaign_id'])
					);

				$currentLvl = array($quest['campaign_id'] => $smcFunc['db_fetch_assoc']($request));
				$smcFunc['db_free_result']($request);

				$request = $smcFunc['db_query']('', '
						SELECT id_quest, level, campaign_id
						FROM {db_prefix}battle_quest
						WHERE campaign_id = {int:campaign}
						ORDER BY level ASC
						LIMIT 1',
						array('campaign' => $quest['campaign_id'])
					);

				$firstLvl[$quest['campaign_id']] = $smcFunc['db_fetch_assoc']($request);
				$context['battle_first'][$quest['campaign_id']] = $firstLvl[$quest['campaign_id']];
				$smcFunc['db_free_result']($request);

				// $firstLvl['level'] = !empty($currentLvl['quest_completions']) ? $currentLvl['quest_completions'] : $firstLvl['level'];

				if ($quest['level'] == $firstLvl[$quest['campaign_id']]['level'])
				{
					$smcFunc['db_insert']('insert',
						'{db_prefix}' . 'battle_campaign_' . $quest['campaign_id'],
						array('id_warrior' => 'int', 'id_campaign' => 'int', 'campaign_name' => 'string', 'score' => 'int', 'start_time' => 'int', 'end_time' => 'int', 'timed_campaign' => 'int', 'level_completion' => 'int', 'quest_completions' => 'int', 'image' => 'string'),
						array($user_info['id'], $quest['campaign_id'], $context['camp_list'][$quest['campaign_id']]['campaign_name'], 0, (!empty($context['camp_list'][$quest['campaign_id']]['start']) ? (int)$context['camp_list'][$quest['campaign_id']]['start'] : time()), time(), (int)$context['camp_list'][$quest['campaign_id']]['timed_campaign'], $firstLvl[$quest['campaign_id']]['level'], $firstLvl[$quest['campaign_id']]['level'], $context['camp_list'][$quest['campaign_id']]['img']),
						array('id_warrior')
					);

					$context['user_campaign'] = !empty($context['user_campaign']) ? $context['user_campaign'] : battle_campaigns_data(array($quest['campaign_id']), $user_info['id']);

					redirectexit('action=battle;sa=quest;home;current_page=' . $thisPage . ';id_campaign=' . $quest['campaign_id'] . ';#battle_main');
				}
			}
		}

		if ((!empty($quest['campaign_id'])) && (int)$campaign['timed_campaign'] > 0)
		{
			if (!AllowedTo('battle_campaign_' . (int)$quest['campaign_id']))
				$context['battle_quest'][$quest['id_quest']]['status'] = $txt['battle_quest_denial'];
			elseif (time() < $campaign['start_time'])
			{
				$startDate = !empty($campaign['start_time']) ? gmdate('d/m/y', $campaign['start_time']) : '';
				$startHours = !empty($campaign['start_time']) ? gmdate('H:i', $campaign['start_time']) : '';
				$startTime = $txt['battle_quest_date_start'] . '&nbsp;' . $startDate . '&nbsp;&nbsp;' . $txt['battle_quest_time_start'] . '&nbsp;' . $startHours . ' GMT';
				$context['battle_quest'][$quest['id_quest']]['status'] = '<span title="' . $startTime . '">' . $txt['battle_quest_pending'] . '</span>';
			}
			elseif (time() >= $campaign['end_time'])
				$context['battle_quest'][$quest['id_quest']]['status'] = $txt['battle_quest_expired'];
		}

		if (!empty($currentQuest))
			continue;

		if ($quest['is_final'] == 1)
			$context['battle_quest'][$quest['id_quest']]['finalText'] = $txt['battle_quest_final_1'];
		else
			$context['battle_quest'][$quest['id_quest']]['finalText'] = $txt['battle_quest_final_0'];

		if ($quest['complete'] >= $quest['limit'])
		{
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_questss'] . '
					<br />
					' . $txt['battle_quest_comp'] . '
					<br />
					' . $txt['battle_quest_time_passed'] . ':&nbsp;' . $quest['complete'] . ' / ' . $quest['limit'];

		}
		elseif ((!empty($quest['campaign_id'])) && !allowedTo('battle_campaign_' . $campaign['id_campaign']))
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_questss'] . '
					<br />
					' . $txt['battle_quest_restrict'] . '
					<br />
					' . $txt['battle_quest_acc_denial'];
		elseif (!$quest['status'])
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_questss'] . '
					<br />
					' . $txt['battle_quest_restrict'] . '
					<br />
					' . $txt['battle_quest_acc_denial'];
		elseif (!empty($quest['campaign_id']) && !empty($quest['user_status']['level_completion']) && empty($context['camp_list'][$quest['campaign_id']]['level_completion']) && $quest['user_status']['level_completion'] > $quest['level'])
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_questss'] . '
					<br />
					' . $txt['battle_quest_comp'] . '
					<br />
					' . $txt['battle_quest_lvl_comp'];
		elseif ($quest['complete'] < $quest['limit'] && $quest['complete'] > 0)
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_quest_final_incomplete'] . '
					<br />
					<a href="' . $scripturl . '?action=battle;sa=quest;do=' . $quest['id_quest'] . ';current_page=' . $thisPage . ';' . (!empty($quest['campaign_id']) ? 'id_campaign=' . $quest['campaign_id'] . ';' : '') . 'session=' . $context['session_id'] . $checkSession . ';#battle_main">
						' . $txt['battle_quest_do_again'] . '
					</a>
					<br />
					' . $txt['battle_quest_time_passed'] . ':&nbsp;' . $quest['complete'] . ' / ' . $quest['limit'];
		elseif (!empty($quest['campaign_id']) && !empty($context['camp_list'][$quest['campaign_id']]['level_completion']) && $quest['level_completion'] > $quest['level'])
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_questss'] . '
					<br />
					' . $txt['battle_quest_comp'] . '
					<br />
					' . $txt['battle_quest_lvl_comp'];
		elseif ((!empty($quest['campaign_id'])) && (int)$campaign['timed_campaign'] > 0 && time() >= $campaign['end_time'])
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_questss'] . '
					<br />
					' . $txt['battle_quest_comp'] . '
					<br />
					' . $txt['battle_quest_expired'];
		elseif ((!empty($quest['campaign_id'])) && (int)$campaign['timed_campaign'] > 0 && time() < $campaign['start_time'])
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_questss'] . '
					<br />
					' . $txt['battle_quest_imminent'] . '
					<br />
					' . $txt['battle_quest_lvl_denial'];
		elseif ((!empty($quest['campaign_id'])) && (int)$campaign['timed_campaign'] > 0 && !allowedTo('battle_campaign_' . $campaign['id_campaign']))
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_questss'] . '
					<br />
					' . $txt['battle_quest_restrict'] . '
					<br />
					' . $txt['battle_quest_acc_denial'];
		elseif (!empty($quest['complete']))
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_quest_final_incomplete'] . '
					<br />
					<a href="' . $scripturl . '?action=battle;sa=quest;do=' . $quest['id_quest'] . ';current_page=' . $thisPage . ';' . (!empty($quest['campaign_id']) ? 'id_campaign=' . $quest['campaign_id'] . ';' : '') . 'session=' . $context['session_id'] . $checkSession . ';#battle_main">
						' . $txt['battle_quest_do_again'] . '
					</a>
					<br />
					' . $txt['battle_quest_time_passed'] . ':&nbsp;' . $quest['complete'] . ' / ' . $quest['limit'];
		elseif($quest['level'] > $user_info['level'])
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_questss'] . '
					<br />
					' . $txt['battle_quest_restrict'] . '
					<br />
					' . $txt['battle_quest_lvl'] . '&nbsp;' . $quest['level'] . '&nbsp;' . $txt['battle_quest_rq'];
		elseif ((!empty($quest['campaign_id'])) && $quest['user_status']['quest_completions'] != $quest['level'] && $quest['level'] != $quest['next_lvl']['first_level'] && ($quest['user_status']['quest_completions'] == 0 && $quest['level'] == $quest['next_lvl']['first_level'] ? false : true))
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_questss'] . '
					<br />
					' . $txt['battle_quest_pending'] . '
					<br />
					' . (!empty($initiate[$quest['id_quest']]) ? $initiate[$quest['id_quest']] : $txt['battle_quest_lvl_denial']);
		elseif ((!empty($quest['campaign_id'])) && $quest['level'] >= $quest['next_lvl']['next_level'] && $quest['level'] != $quest['user_status']['quest_completions'])
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_questss'] . '
					<br />
					' . $txt['battle_quest_pending'] . '
					<br />
					' . (!empty($initiate[$quest['id_quest']]) ? $initiate[$quest['id_quest']] : $txt['battle_quest_lvl_denial']);
		else
		{
			$context['battle_quest'][$quest['id_quest']]['display'] = '
					' . $txt['battle_quest_final_new'] . '
					<br />
					<a href="' . $scripturl . '?action=battle;sa=quest;do=' . $quest['id_quest'] . ';current_page=' . $thisPage . ';' . (!empty($quest['campaign_id']) ? 'id_campaign=' . $quest['campaign_id'] . ';' : '') . 'session=' . $context['session_id'] . $checkSession . ';#battle_main">
						' . $txt['battle_quest_do'] . '
					</a>
					<br />
					' . $txt['battle_quest_final_limit'] . '
					&nbsp;' . $quest['limit'];

		}
	}

	// $context['battle_quest'] = battle_pagination($context['battle_quests'], 10);
	$context['current_pages'] = (($nRows-1) / $per_page) + 1;
	$context['battle_display'] = battle_pages($txt['battle_page'], '#battle_main', $scripturl . '?action=battle;sa=quest;' . (!empty($context['campaigns_id']) ? 'id_campaign=' . $context['campaigns_id'] . ';' : '') . 'home;', $context['current_pages']);

	if(!empty($_GET['do']))
	{
		$quest = !empty($_GET['do']) ? (int) $_GET['do']: 0;

		$r = !empty($context['battle_quest'][$quest]) ? $context['battle_quest'][$quest] : array();
		if (empty($r))
		{
			$r = battle_getQuest($quest, $context['camp_list'][$context['campaigns_id']]);
			$previous[$quest] = array(
				'exp_points' => $r['exp_points'],
				'warrior_gold' => $r['gold'],
				'fail' => $r['fail'],
				'complete' => $r['complete']
			);
			$campaign_lvl[$quest] = array('campaign_id' => $r['campaign_id'], 'level' => $r['level'], 'limit' => $r['limit'], 'start' => $r['start_time'], 'end' => $r['end_time']);
			$r['quest_completions'] = !empty($r['level']) ? (int)$r['level'] : 0;
			$r['next_lvl']['next_level'] = $r['quest_completions'];
		}

		if (empty($r))
			fatal_error($txt['battle_cheatrefresh'], false);

		$checkSession = 9 * ((!empty($r['level']) ? $r['level'] : 9) + (!empty($r['success']) ? $r['success'] : 9) + (!empty($r['fail']) ? $r['fail'] : 9) + (!empty($user_info['energy']) ? $user_info['energy'] : 9));
		$session = !empty($_REQUEST['session']) ? $_REQUEST['session'] : false;
		$context['battle_ErrorBack']['change'] = $context['battle_ErrorBack']['quest'];
		if($session !== $context['session_id'] . $checkSession)
			fatal_error($txt['battle_cheatrefresh'], false);

		$campaign = !empty($context['camp_list'][$r['campaign_id']]) ? $context['camp_list'][$r['campaign_id']] : array();
		$context['user_campaign'] = (empty($context['user_campaign'])) && !empty($context['camp_list'][$r['campaign_id']]) ? battle_campaigns_data(array($r['campaign_id']), $user_info['id']) : (!empty($context['user_campaign']) ? $context['user_campaign'] : array());

		if ($user_info['hp'] == 0)
			fatal_error($txt['battle_deadplease'], false);

		if ($user_info['energy'] < $r['energy'])
			fatal_error('<a href="' . $scripturl. '?action=battle;sa=shop;home;#battle_main">' . $txt['battle_upd1'] . '</a>', false);

		if ($r['complete'] >= $r['limit'])
			fatal_error($txt['battle_quest_e4'] . str_replace('&%#@!$', $link, $txt['battle_return_quest']), false);

		if (empty($r['itext']) && empty($r['ftext']) && empty($r['stext']))
			fatal_error($txt['battle_quest_e5'] . str_replace('&%#@!$', $link, $txt['battle_return_quest']), false);

		// Is this quest part of a campaign?
		if (!empty($r['campaign_id']) && !empty($context['user_campaign'][$r['campaign_id']]))
		{
			$check = battle_check_campaign($campaign, $r['level'], $r['quest_completions'], $context['battle_nextLvl'][$quest], $context['user_campaign'][$r['campaign_id']], $r['level_completion']);
			if ($check)
				fatal_error($check, false);
		}

		$smcFunc['db_query']('', '
			UPDATE {db_prefix}battle_quest
			SET
			plays = plays + 1
			WHERE id_quest = {int:id_quest}',
				array('id_quest' =>  $quest,
				)
			);


		if ($r['energy'] > $user_info['energy'])
			$context['quest2'] = $txt['battle_quest_e2'].' '.$r['energy'].' '.$txt['battle_quest_e3'];
	 	elseif ($r['level'] > $user_info['level'])
		{
			$context['quest2'] = $txt['battle_error15'];
			$context['quest3'] = str_replace('&%#@!$', 'id_campaign=' . $context['campaigns_id'] . ';current_page=' . $thisPage . ';', $txt['battle_return_quest']);
		}
	 	else
		{
			$context['quest2'] = $r['itext'];
			$context['quest3'] = '<a href="'. $scripturl. '?action=battle;sa=quest;go='.$quest.';current_page=' . $thisPage . ';' . (!empty($context['campaigns_id']) ? 'id_campaign=' . $context['campaigns_id'] . ';' : '') . 'session=' . $context['session_id'] . $checkSession . ';#battle_main">'.$txt['battle_quest_cont'].'</a>';
	 	}

	}

	if(!empty($_GET['go']))
	{
		$quest = !empty($_GET['go']) ? (int) $_GET['go']: 0;

		$r = !empty($context['battle_quest'][$quest]) ? $context['battle_quest'][$quest] : array();
		if (empty($r))
		{
			$r = battle_getQuest($quest, $context['camp_list'][$context['campaigns_id']]);
			$previous[$quest] = array(
				'exp_points' => $r['exp_points'],
				'warrior_gold' => $r['gold'],
				'fail' => $r['fail'],
				'complete' => $r['complete']
			);
			$campaign_lvl[$quest] = array('campaign_id' => $r['campaign_id'], 'level' => $r['level'], 'limit' => $r['limit'], 'start' => $r['start_time'], 'end' => $r['end_time']);
			$r['quest_completions'] = !empty($r['level']) ? (int)$r['level'] : 0;
			$r['next_lvl']['next_level'] = $r['quest_completions'];
		}

		if (empty($r))
			fatal_error($txt['battle_cheatrefresh'], false);

		$checkSession = 9 * ((!empty($r['level']) ? $r['level'] : 9) + (!empty($r['success']) ? $r['success'] : 9) + (!empty($r['fail']) ? $r['fail'] : 9) + (!empty($user_info['energy']) ? $user_info['energy'] : 9));
		$session = !empty($_REQUEST['session']) ? $_REQUEST['session'] : false;
		$context['battle_ErrorBack']['change'] = $context['battle_ErrorBack']['quest'];
		if($session !== $context['session_id'] . $checkSession)
			fatal_error($txt['battle_cheatrefresh'], false);

		if ($user_info['hp'] == 0)
			fatal_error($txt['battle_deadplease'] . '<a href="'. $scripturl. '?action=battle;sa=shop;#battle_main;"> '.$txt['battle_deadplease1'].'</a>.', false);

		if ($user_info['energy'] < $r['energy'])
			fatal_error('<a href="' . $scripturl. '?action=battle;sa=shop;home;#battle_main">' . $txt['battle_upd1'] . '</a>', false);

		$previous = !empty($previous[$quest]) ? $previous[$quest] : array('exp_points' => 0, 'gold' => 0, 'fail' => 0, 'complete' => 0);
		$campaign = !empty($context['camp_list'][$r['campaign_id']]) ? $context['camp_list'][$r['campaign_id']] : array();

		if (!empty($r))
		{
			$hp =  $user_info['energy'] - $r['energy'];
			$qcdate = time();
			$healthNeg = rand(0, ($r['hp'] < 1 ? 0 : $r['hp']));
			$health = $user_info['hp'] - $healthNeg < 1 ? 0 : $user_info['hp'] - $healthNeg;
			$context['user_campaign'] = !empty($r['campaign_id']) && (empty($context['user_campaign'])) ? battle_campaigns_data(array($r['campaign_id']), $user_info['id']) : (!empty($context['user_campaign']) ? $context['user_campaign'] : array());

			if ($previous['complete'] > ($r['limit']-1))
				fatal_error($txt['battle_cheatrefresh'], false);
			elseif((!empty($context['battleCheck'])) && $context['battleCheck'] == $quest)
				fatal_error($txt['battle_cheatrefresh'], false);

			$context['battleCheck'] = $quest;

			if(rand(1,100) <= $r['success'])
			{
				$context['quest'] = str_replace("{money}",$r['gold'],$r['stext']);
				if($modSettings['enable_battle_hist'])
				{
					$content = ''.$user_info['name'].' '.$txt['battle_hist19'].' '.$r['name'].'';
					add_to_battle_hist($content);
				}

				if($r['is_final'])
				{
					$changes = array();
					$penalties = array();
					$max_change = array(rand(0, ($r['max_gain'] < 1 ? 0 : $r['max_gain'])), rand(0, ($r['max_gain'] < 1 ? 0 : $r['max_gain'])));
					$statsArray = array('stat_point' => trim($txt['battle_syusp']), 'atk' => $txt['battle_atk'], 'def' => $txt['battle_def'], 'stamina' => $txt['battle_Stamina']);

					for ($i=0; $i<2; $i++)
					{
						$random = array_rand($statsArray);
						if ($random === 'stat_point')
							$change = $user_info[$random] + $max_change[$i];
						else
							$change = $user_info[$random] + $max_change[$i] >= $user_info['max_' . $random] ? $user_info['max_' . $random] : $user_info[$random] + $max_change[$i];

						$changes[$statsArray[$random]] = $max_change[$i];

						if (!empty($max_change[$i]))
							updateMemberData($user_info['id'], array($random => $change));

						unset($statsArray[$random]);

					}

					if ($r['complete'] >= $r['limit'])
						$context['battle_message'] .= $txt['battle_quest_final2'];
					else
						$context['battle_message'] .= $txt['battle_quest_final3'];

					$gold = rand($r['min_gold'], $r['gold']);
					$exp = rand($r['min_exp'], $r['exp']);
					$context['battle_message'] .= '<br />' . $txt['battle_quest_notFinal'];
					$context['battle_message'] .= $context['quest'];
					$context['sub_template']  = 'battle_quest_final';
					$changes[$txt['battle_exp']] = $exp;
					$changes[$modSettings['bcash']] = $gold;
					$penalties[$txt['battle_hp']] = $healthNeg;
					$penalties[$txt['battle_energy']] = $r['energy'];

					foreach ($changes as $change => $value)
					{
						if (!empty($value))
							$context['battle_message'] .= '<br />' . str_replace('!@#$&', $change, $txt['battle_won_stat']) . '&nbsp;' . $value;
					}

					foreach ($penalties as $penalty => $value)
					{
						if (!empty($value))
							$context['battle_message'] .= '<br />' . str_replace('!@#$&', $penalty, $txt['battle_lost_stat']) . '&nbsp;' . $value;
					}

					$smcFunc['db_insert'](
						      'replace',
						      '{db_prefix}battle_quest_champs',
						      array('id_warrior' => 'int', 'id_quest' => 'int', 'exp_points' => 'int', 'warrior_gold' => 'int', 'fail' => 'int', 'complete' => 'int', 'date' => 'int'),
						      array($user_info['id'], $quest, ($exp + $previous['exp_points']), ($gold + $previous['warrior_gold']) , $previous['fail'], ($previous['complete']+1), $qcdate),
						      array('id_warrior', 'id_quest')
					);

					updateMemberData($user_info['id'], array('hp' => $health, 'energy' => ($hp < 0 ? 0 : $hp), $modSettings['bcash'] => ($user_info[$modSettings['bcash']] + $gold), 'exp' => ($user_info['exp'] + $exp)));

					if (!empty($r['campaign_id']))
					{
						$maxComplete = (int)$r['complete'] <= 5 ? (int)$r['complete'] : 5;
						$score = ($gold + $exp + $max_change[0] + $max_change[1] + $r['level']) * ((int)$r['complete'] < 1 ? 1 : $maxComplete) * 10;
						$table = 'battle_campaign_' . $r['campaign_id'];

						if ((int)$campaign['start_time'] < 1)
							$smcFunc['db_query']('', "
								UPDATE {db_prefix}{raw:table}
								SET start_time = {int:start_time}
								WHERE id_warrior = {int:num}",
								array('start_time' => time(), 'num' => 0, 'table' => $table)
							);

						if (!empty($context['user_campaign'][$r['campaign_id']]))
							$smcFunc['db_insert']('replace',
								'{db_prefix}' . $table,
								array('id_warrior' => 'int', 'id_campaign' => 'int', 'campaign_name' => 'string', 'score' => 'int', 'start_time' => 'int', 'end_time' => 'int', 'timed_campaign' => 'int', 'level_completion' => 'int', 'quest_completions' => 'int', 'image' => 'string'),
								array($user_info['id'], $r['campaign_id'], $campaign['campaign_name'], $context['user_campaign'][$r['campaign_id']]['score'] + $score, (int)$context['user_campaign'][$r['campaign_id']]['start_time'], time(), (int)$campaign['timed_campaign'], $r['quest_completions'], ($previous['complete']+1 < $r['limit'] ? $r['quest_completions'] : $r['next_lvl']['next_level']), $campaign['img']),
								array('id_warrior')
							);
						else
							$smcFunc['db_insert']('insert',
								'{db_prefix}' . $table,
								array('id_warrior' => 'int', 'id_campaign' => 'int', 'campaign_name' => 'string', 'score' => 'int', 'start_time' => 'int', 'end_time' => 'int', 'timed_campaign' => 'int', 'level_completion' => 'int', 'quest_completions' => 'int', 'image' => 'string'),
								array($user_info['id'], $r['campaign_id'], $campaign['campaign_name'], $score, (!empty($campaign['start']) ? (int)$campaign['start'] : time()), time(), (int)$campaign['timed_campaign'], $r['quest_completions'], ($previous['complete']+1 < $r['limit'] ? $r['quest_completions'] : $r['next_lvl']['next_level']), $campaign['img']),
								array('id_warrior')
							);
					}

					unset($_GET['do']);
					unset($_GET['go']);
					unset($context['battle_quest']);
					unset($r);
				}
				else
				{
					$changes = array();
					$penalties = array();
					$max_change = array(rand(0, ($r['max_gain'] < 1 ? 0 : $r['max_gain'])), rand(0, ($r['max_gain'] < 1 ? 0 : $r['max_gain'])));
					$statsArray = array('stat_point' => trim($txt['battle_syusp']), 'atk' => $txt['battle_atk'], 'def' => $txt['battle_def'], 'stamina' => $txt['battle_Stamina']);

					for ($i=0; $i<2; $i++)
					{
						$random = array_rand($statsArray);
						if ($random === 'stat_point')
							$change = $user_info[$random] + $max_change[$i];
						else
							$change = $user_info[$random] + $max_change[$i] >= $user_info['max_' . $random] ? $user_info['max_' . $random] : $user_info[$random] + $max_change[$i];

						$changes[$statsArray[$random]] = $max_change[$i];

						if (!empty($max_change[$i]))
							updateMemberData($user_info['id'], array($random => $change));

						unset($statsArray[$random]);

					}

					if ($r['complete'] >= $r['limit'])
					{
						$gold = rand($r['min_gold'], $r['gold']);
						$exp = rand($r['min_exp'], $r['exp']);
						$context['battle_message'] .= $txt['battle_quest_final2'];
						$context['battle_message'] .= '<br />' . $txt['battle_quest_notFinal'];
					}
					else
					{
						$gold = rand(0, $r['min_gold']);
						$exp = rand(0, $r['min_exp']);
						$context['battle_message'] .= $txt['battle_quest_final3'];
						$context['battle_message'] .= '<br />' . $txt['battle_quest_notFinal'];
					}

					$context['battle_message'] .= $context['quest'];
					$context['sub_template']  = 'battle_quest_incomplete';
					$changes[$txt['battle_exp']] = $exp;
					$changes[$modSettings['bcash']] = $gold;
					$penalties[$txt['battle_hp']] = $healthNeg;
					$penalties[$txt['battle_energy']] = $r['energy'];

					foreach ($changes as $change => $value)
					{
						if (!empty($value))
							$context['battle_message'] .= '<br />' . str_replace('!@#$&', $change, $txt['battle_won_stat']) . '&nbsp;' . $value;
					}

					foreach ($penalties as $penalty => $value)
					{
						if (!empty($value))
							$context['battle_message'] .= '<br />' . str_replace('!@#$&', $penalty, $txt['battle_lost_stat']) . '&nbsp;' . $value;
					}

					$smcFunc['db_insert'](
					      'replace',
					      '{db_prefix}battle_quest_champs',
					      array('id_warrior' => 'int', 'id_quest' => 'int', 'exp_points' => 'int', 'warrior_gold' => 'int', 'fail' => 'int', 'complete' => 'int', 'date' => 'int'),
					      array($user_info['id'], $quest, ($exp + $previous['exp_points']), ($gold + $previous['warrior_gold']) , $previous['fail'], ($previous['complete']+1), $qcdate),
					      array('id_warrior', 'id_quest')
					);

					updateMemberData($user_info['id'], array('hp' => $health, 'energy' => ($hp < 0 ? 0 : $hp), $modSettings['bcash'] => ($user_info[$modSettings['bcash']] + $gold), 'exp' => ($user_info['exp'] + $exp)));

					if ((!empty($r['campaign_id'])))
					{
						$maxComplete = (int)$r['complete'] <= 5 ? (int)$r['complete'] : 5;
						$score = ($gold + $exp + $max_change[0] + $max_change[1] + $r['level']) * ((int)$r['complete'] < 1 ? 1 : $maxComplete) * 10;
						$table = 'battle_campaign_' . $r['campaign_id'];

						if ((int)$campaign['start_time'] < 1)
							$smcFunc['db_query']('', "
								UPDATE {db_prefix}{raw:table}
								SET start_time = {int:start_time}
								WHERE id_warrior = {int:num}",
								array('start_time' => time(), 'num' => 0, 'table' => $table)
							);

						if (!empty($context['user_campaign'][$r['campaign_id']]))
							$smcFunc['db_insert']('replace',
								'{db_prefix}' . $table,
								array('id_warrior' => 'int', 'id_campaign' => 'int', 'campaign_name' => 'string', 'score' => 'int', 'start_time' => 'int', 'end_time' => 'int', 'timed_campaign' => 'int', 'level_completion' => 'int', 'quest_completions' => 'int', 'image' => 'string'),
								array($user_info['id'], $r['campaign_id'], $campaign['campaign_name'], $context['user_campaign'][$r['campaign_id']]['score'] + $score, (int)$context['user_campaign'][$r['campaign_id']]['start_time'], time(), (int)$campaign['timed_campaign'], $r['quest_completions'], ($previous['complete']+1 < $r['limit'] ? $r['quest_completions'] : $r['next_lvl']['next_level']), $campaign['img']),
								array('id_warrior')
							);
						else
							$smcFunc['db_insert']('insert',
								'{db_prefix}' . $table,
								array('id_warrior' => 'int', 'id_campaign' => 'int', 'campaign_name' => 'string', 'score' => 'int', 'start_time' => 'int', 'end_time' => 'int', 'timed_campaign' => 'int', 'level_completion' => 'int', 'quest_completions' => 'int', 'image' => 'string'),
								array($user_info['id'], $r['campaign_id'], $campaign['campaign_name'], $score, (!empty($campaign['start']) ? (int)$campaign['start'] : time()), time(), (int)$campaign['timed_campaign'], $r['quest_completions'], ($previous['complete']+1 < $r['limit'] ? $r['quest_completions'] : $r['next_lvl']['next_level']), $campaign['img']),
								array('id_warrior')
							);

							$smcFunc['db_query']('', "
								UPDATE {db_prefix}{raw:table}
								SET end_time = {int:end_time}
								WHERE id_warrior = {int:num}",
								array('end_time' => time(), 'num' => 0, 'table' => $table)
							);
					}

					unset($_GET['do']);
					unset($_GET['go']);
					unset($context['battle_quest']);
					unset($r);
				}
			}
			else
			{
				// losing the quest results in penalties
				if($r['is_final'] || $r['complete'] >= $r['limit'])
				{
					$gold = rand($r['min_gold'], $r['gold']);
					$exp = rand($r['min_exp'], $r['exp']);
				}
				else
				{
					$gold = rand(0, $r['min_gold']);
					$exp = rand(0, $r['min_exp']);
				}


				$penalties = array();
				$max_penalty = array(rand(0, ($r['max_penalty'] < 1 ? 0 : $r['max_penalty'])), rand(0, ($r['max_penalty'] < 1 ? 0 : $r['max_penalty'])));
				$statsArray = array('stat_point' => trim($txt['battle_syusp']), 'atk' => $txt['battle_atk'], 'def' => $txt['battle_def'], 'stamina' => $txt['battle_Stamina']);

				for ($i=0; $i<2; $i++)
				{
					$random = array_rand($statsArray);
					$penalty = $user_info[$random] - $max_penalty[$i] < 1 ? 0 : $user_info[$random] - $max_penalty[$i];
					$penalties[$statsArray[$random]] = $user_info[$random] < 1 ? 0 : $max_penalty[$i];

					if (!empty($max_penalty[$i]))
						updateMemberData($user_info['id'], array($random => $penalty));

					unset($statsArray[$random]);

				}

				$penalties[$modSettings['bcash']] = $gold;
				$penalties[$txt['battle_hp']] = $healthNeg;
				$penalties[$txt['battle_energy']] = $r['energy'];

				$context['quest'] = $r['ftext'];
				$context['sub_template']  = 'battle_quest_incomplete';
				$context['battle_message'] .= $txt['battle_quest_lost'] . '<br />';
				$context['battle_message'] .= $context['quest'];

				foreach ($penalties as $penalty => $value)
				{
					if (!empty($value))
						$context['battle_message'] .= '<br />' . str_replace('!@#$&', $penalty, $txt['battle_lost_stat']) . '&nbsp;' . $value;
				}

				$smcFunc['db_insert'](
				      'replace',
				      '{db_prefix}battle_quest_champs',
				      array('id_warrior' => 'int', 'id_quest' => 'int', 'exp_points' => 'int', 'warrior_gold' => 'int', 'fail' => 'int', 'complete' => 'int', 'date' => 'int'),
				      array($user_info['id'], $quest, ($previous['exp_points'] - $exp < 0 ? 0 : $previous['exp_points'] - $exp), ($previous['warrior_gold'] - $gold < 0 ? 0 : $previous['warrior_gold'] - $gold) , ($previous['fail']+1), $previous['complete'], $qcdate),
				      array('id_warrior', 'id_quest')
				);

				updateMemberData($user_info['id'], array('hp' => $health, 'energy' => ($hp < 0 ? 0 : $hp), $modSettings['bcash'] => ($user_info[$modSettings['bcash']] - $gold < 0 ? 0 : $user_info[$modSettings['bcash']] - $gold)));

				if($modSettings['enable_battle_hist'])
				{
					$content = $user_info['name'].' '.$txt['battle_hist20'].' '.$r['name'];

					add_to_battle_hist($content);
				}

				unset($_GET['do']);
				unset($_GET['go']);
				unset($context['battle_quest']);
				unset($r);
			}
		}

		unset($_GET['do']);
		unset($_GET['go']);
		unset($context['battle_quest']);
		unset($r);
	}
}
?>