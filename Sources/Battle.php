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

function battle()
{
    global $txt, $user_info, $context, $settings, $scripturl, $modSettings, $boardurl;

    // Denied if permissions fail or user is guest
    if (!AllowedTo('view_battle') || $context['user']['is_guest'])
        redirectexit('?action=forum');

    //We Need Our Template
    loadTemplate('Battle', array('forum', 'battle'));

    $context['template_layers'][] = 'battlemain';
    @ini_set('memory_limit','64M');

    // load some Stuff
    battle_init();
    battle_mode();
    battle_get_shouts(5);
    battle_get_hist(5);
    battle_whos_online();
    battle_didyouknow();

    // Here's our little sub-action array.
    $subActions = array(
	'main' => 'battle_prime',
	'search' => 'battle_search',
	'shop' => 'battle_shop',
	'fm' => 'battle_fight_monster',
	'battle' => 'battle_battle',
	'quest' => 'battle_quest',
	'explore' => 'battle_explore',
	'shout' => 'battleShout',
	'hosp' => 'battle_hosp',
	'fight' => 'battle_fight',
	'levelup' => 'battle_level',
	'gy' => 'battle_graveyard',
	'upgrade' => 'battle_stat_upgrade',
	'bhist' => 'battle_histlist',
	'stats' => 'battle_stats',
	'settings' => 'battle_memset',
	'monsters' => 'battle_monsters',
	'howto' => 'battle_howto',
	'leaders' => 'battle_leaders',
	'cleaders' => 'battle_campaign_leaders',
	'leaderboard' => 'battle_leaderboard'
	);

    // Default the sub-action to 'battle;sa=main'.
    $_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'main';

    // Set title and default sub-action.
    $context['page_title'] = $txt['battle_home'];
    $context['sub_action'] = $_REQUEST['sa'];

    if (empty($context['current_page']))
        $context['current_page'] = 1;

    $campaign_id = !empty($_REQUEST['id_campaign']) ? ';id_campaign=' . (int)$_REQUEST['id_campaign'] : '';
    $context['current_page'] = (!empty($_REQUEST['current_page']) ? (int)$_REQUEST['current_page'] : $context['current_page']) -1;

    $context['battle_sort'] = (!empty($_REQUEST['sort'])) && $_REQUEST['sort'] === 'id_member' ? 'id_member;' : 'real_name;';
    $context['battle_order'] = (!empty($_REQUEST['order'])) && $_REQUEST['order'] === 'DESC' ? 'DESC;' : 'ASC;';
    $context['battle_ErrorBack']['change'] = false;
    foreach (array('explore', 'battle', 'quest') as $errorBack)
	$context['battle_ErrorBack'][$errorBack] = '
<script type="text/javascript"><!-- // --><![CDATA[
    var hyperlinks = document.getElementsByTagName("a");
    for(var i=0;i<hyperlinks.length; i++)
    {
	if(hyperlinks[i].href == "javascript:history.go(-1)")
	{
	    hyperlinks[i].href = "' . $scripturl . '?action=battle;sa=' . $errorBack . $campaign_id . ';home;#battle_main";
	    break;
	}
    }
// ]]></script>';

    // auto level up if it is enabled
    if (!empty($modSettings['battle_auto_lvl']))
	battle_level('action=battle;sa=' . $_REQUEST['sa'] . ';#battle_main');

    // Check to see if the game has been flagged as ended
    if (in_array($_REQUEST['sa'], array('fm', 'battle', 'explore', 'fight')))
	battle_game_over();
    else
    	setcookie("battle_start_time", false, time()+3600, '/');


    // Call the right function for this sub-acton.
    $subActions[$_REQUEST['sa']]();
}

function battle_prime()
{
    // empty function
}

function battle_mode()
{
    global $context, $modSettings, $txt, $smcFunc;

    $points = !empty($modSettings['battle_points']) ? abs((int)$modSettings['battle_points']) : 0;
    $mode = !empty($modSettings['battle_players_lvl']) ? true : false;

    if (!$points)
	$context['battle_mode'] = $txt['battle_infinity'];
    elseif ($mode)
	$context['battle_mode'] = $txt['battle_victory'];
    else
	$context['battle_mode'] = $txt['battle_rivalry'];
}

function battle_leaders($allowed = 10)
{
    global  $smcFunc, $context, $user_profile, $scripturl;

    $context['sub_template']  = 'battle_champs';
    $context['battle_champs'] = array();

    $request = $smcFunc['db_query']('', "
			SELECT c.id_champ, c.id_slain, c.times_champ, c.date, m.real_name, m.mem_slays, m.mon_slays
			FROM {db_prefix}battle_champs AS c
			LEFT JOIN {db_prefix}members AS m  ON (c.id_champ = m.id_member)
		        ORDER BY m.mem_slays DESC
			LIMIT {int:allowed}",
			array('allowed' => $allowed)
                        );

    // Loop through all results
    while ($row = $smcFunc['db_fetch_assoc']($request))
    {
        loadMemberData(array($row['id_slain']),false, 'normal');
        $slain_name = strlen($user_profile[$row['id_slain']]['real_name']) > 28 ? substr($user_profile[$row['id_slain']]['real_name'], 0, 25) . '...' : $user_profile[$row['id_slain']]['real_name'];
        $name = strlen($row['real_name']) > 28 ? substr($row['real_name'], 0, 25) . '...' : $row['real_name'];

	$context['battle_champs'][] = array(
		'real_name' => $name,
		'date' => $row['date'],
		'id_member' => $row['id_champ'],
		'times_champ' => !empty($row['times_champ']) ? (int)$row['times_champ'] : 0,
                'mem_slays' => !empty($row['mem_slays']) ? (int)$row['mem_slays'] : 0,
                'mon_slays' => !empty($row['mon_slays']) ? (int)$row['mon_slays'] : 0,
                'who_slain' => '<span style="font-style:oblique;"><a href="' . $scripturl . '?action=profile;u=' . $row['id_slain'] . '">' . $slain_name . '</a></span>'
		);
    }
    $smcFunc['db_free_result']($request);
}

function battle_campaign_leaders($per_page = 10)
{
    global  $smcFunc, $context, $user_profile, $scripturl, $settings, $txt;

    $context['sub_template']  = 'battle_campaign_champs';
    $context['battle_campaign_champions'] = array();
    $context['campaign_id'] = !empty($_REQUEST['id_campaign']) ? (int)$_REQUEST['id_campaign'] : 0;
    $context['battle_display'] = array('page' => false, 'pages' => '0');
    $page = $context['current_page'] * $per_page;
    $table = 'battle_campaign_' . $context['campaign_id'];

    if ($context['campaign_id'] > 0)
    {
	$context['camp_list'] = battle_campaigns_list(0, $context['campaign_id']);

	if (empty($context['camp_list'][$context['campaign_id']]))
	    fatal_error($txt['battle_campaign_exist_error'], false);

	$request = $smcFunc['db_query']('', "
			SELECT c.id_warrior, c.id_campaign, c.campaign_name, c.score, c.start_time, c.end_time, c.image,
			qc.id_quest, qc.complete, q.campaign_id
			FROM {db_prefix}{$table} AS c
			LEFT JOIN {db_prefix}battle_quest AS q ON (q.campaign_id = c.id_campaign)
			LEFT JOIN {db_prefix}battle_quest_champs AS qc ON (qc.id_quest = q.id_quest AND qc.id_warrior = c.id_warrior)
			WHERE c.id_campaign = {int:campaign} AND c.id_warrior >= {int:warrior} AND qc.complete > {int:complete}
			ORDER BY c.score DESC, qc.complete DESC",
			array('campaign' => $context['campaign_id'], 'warrior' => 1, 'complete' => 0)
		    );

	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
	    loadMemberData(array($row['id_warrior']),false, 'normal');
	    $warriorName = strlen($user_profile[$row['id_warrior']]['real_name']) > 28 ? substr($user_profile[$row['id_warrior']]['real_name'], 0, 25) . '...' : $user_profile[$row['id_warrior']]['real_name'];
	    $campaignName = strlen($row['campaign_name']) > 28 ? substr($row['campaign_name'], 0, 25) . '...' : $row['campaign_name'];
	    $campaignImage = $row['image'] != 'blank.gif' ? '<img style="width:16px;height:16px;vertical-align:middle;" src="' . $settings['default_theme_url'] . '/images/battle/campaign/' . $row['image'] . '" alt="" title="' . $campaignName . '" />' : false;
	    $totalQuests[$row['id_warrior']] = (!empty($row['complete']) ? (int)$row['complete'] : 0) + (!empty($totalQuests[$row['id_warrior']]) ? $totalQuests[$row['id_warrior']] : 0);

	    $context['battle_campaign_champions'][$row['id_warrior']] = array(
		'real_name' => !empty($warriorName) ? $warriorName : '',
		'date' => !empty($row['end_time']) ? date('Y-m-d h:ia', $row['end_time']) : '',
		'id_member' => !empty($row['id_warrior']) ? (int)$row['id_warrior'] : 0,
		'times_quests' => !empty($totalQuests[$row['id_warrior']]) ? $totalQuests[$row['id_warrior']] : '',
                'campaign_name' => !empty($campaignName) ? $campaignImage . '&nbsp;<span style="vertical-align:middle;">' . $campaignName . '</span>' : '',
                'mem_score' => !empty($row['score']) ? (int)$row['score'] : 0,
                );
	}
	$smcFunc['db_free_result']($request);
    }
    else
    {
	$context['camp_list'] = battle_campaigns_list(0, 0);
	foreach($context['camp_list'] as $campaign => $data)
	{
	    $table = 'battle_campaign_' . $campaign;
	    $request = $smcFunc['db_query']('', "
			SELECT c.id_warrior, c.id_campaign, c.campaign_name, c.score, c.start_time, c.end_time, c.image,
			qc.id_quest, qc.complete, q.campaign_id
			FROM {db_prefix}{$table} AS c
			LEFT JOIN {db_prefix}battle_quest AS q ON (q.campaign_id = c.id_campaign)
			LEFT JOIN {db_prefix}battle_quest_champs AS qc ON (qc.id_quest = q.id_quest AND qc.id_warrior = c.id_warrior)
			WHERE c.id_campaign = {int:campaign} AND c.id_warrior >= {int:warrior} AND qc.complete > {int:complete}
			ORDER BY c.score DESC, qc.complete DESC
			LIMIT {int:allowed}",
			array('allowed' => 1, 'campaign' => $campaign, 'complete' => 0, 'warrior' => 1)
		    );

	    while ($row = $smcFunc['db_fetch_assoc']($request))
	    {
		loadMemberData(array($row['id_warrior']),false, 'normal');
	        $warriorName = strlen($user_profile[$row['id_warrior']]['real_name']) > 28 ? substr($user_profile[$row['id_warrior']]['real_name'], 0, 25) . '...' : $user_profile[$row['id_warrior']]['real_name'];
	        $campaignName = strlen($row['campaign_name']) > 28 ? substr($row['campaign_name'], 0, 25) . '...' : $row['campaign_name'];
	        $campaignImage = $row['image'] != 'blank.gif' ? '<img style="width:16px;height:16px;vertical-align:middle;" src="' . $settings['default_theme_url'] . '/images/battle/campaign/' . $row['image'] . '" alt="" title="' . $campaignName . '" />' : false;

	        $context['battle_campaign_champions'][$campaign] = array(
		    'real_name' => !empty($warriorName) ? $warriorName : '',
		    'date' => !empty($row['end_time']) ? date('Y-m-d h:ia', $row['end_time']) : '',
		    'id_member' => !empty($row['id_warrior']) ? (int)$row['id_warrior'] : 0,
		    'times_quests' => 0,
		    'campaign_name' => !empty($campaignName) ? $campaignImage . '&nbsp;<span style="vertical-align:middle;">' . $campaignName . '</span>' : '',
		    'mem_score' => !empty($row['score']) ? (int)$row['score'] : 0,
		);
	    }

	    $smcFunc['db_free_result']($request);

	    if (!empty($context['battle_campaign_champions'][$campaign]['id_member']))
	    {
		$request = $smcFunc['db_query']('', "
			SELECT qc.id_warrior, qc.complete, q.id_quest
			FROM {db_prefix}battle_quest AS q
			LEFT JOIN {db_prefix}battle_quest_champs AS qc ON (qc.id_quest = q.id_quest AND qc.complete > {int:complete})
			WHERE q.campaign_id = {int:campaign} AND qc.id_warrior = {int:warrior}",
			array('complete' => 0, 'warrior' => $context['battle_campaign_champions'][$campaign]['id_member'], 'campaign' => $campaign)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		    $context['battle_campaign_champions'][$campaign]['times_quests'] = $context['battle_campaign_champions'][$campaign]['times_quests'] + $row['complete'];

		$smcFunc['db_free_result']($request);
	    }
	}

    }

    $context['battle_campaign_champs'] = battle_pagination($context['battle_campaign_champions'], $per_page);
    $context['battle_display'] = battle_pages($txt['battle_page'], '#battle_main', $scripturl . '?action=battle;sa=campaign_leaders', $context['current_pages']);
}

function battle_leaderboard($per_page = 10)
{
    global $smcFunc, $context, $user_profile, $scripturl, $settings, $txt;

    $context['sub_template']  = 'battle_leaderboard';
    $context['battle_leaders'] = array();
    $context['battle_display'] = array('page' => false, 'pages' => '0');
    $page = $context['current_page'] * $per_page;

    $request = $smcFunc['db_query']('', "
	SELECT id_warrior, battle_title, score, date, level
	FROM {db_prefix}battle_scores
	ORDER BY score DESC
	LIMIT 100"
    );

    while ($row = $smcFunc['db_fetch_assoc']($request))
    {
	loadMemberData(array($row['id_warrior']),false, 'normal');
	$warriorName = strlen($user_profile[$row['id_warrior']]['real_name']) > 28 ? substr($user_profile[$row['id_warrior']]['real_name'], 0, 25) . '...' : $user_profile[$row['id_warrior']]['real_name'];
	$battleName = strlen($row['battle_title']) > 28 ? substr($row['battle_title'], 0, 25) . '...' : $row['battle_title'];

	$context['battle_leaders']['warrior_' . $row['id_warrior']] = array(
	    'real_name' => !empty($warriorName) ? $warriorName : '',
	    'date' => !empty($row['date']) ? date('Y-m-d h:ia', $row['date']) : '',
	    'level' => !empty($row['level']) ? (int)$row['level'] : 0,
	    'id_member' => !empty($row['id_warrior']) ? (int)$row['id_warrior'] : 0,
	    'battle_title' => !empty($battleName) ? '<span style="vertical-align:middle;">' . $battleName . '</span>' : '',
	    'score' => !empty($row['score']) ? (int)$row['score'] : 0,
        );
    }
    $smcFunc['db_free_result']($request);

    $context['battle_leaderboard'] = battle_pagination($context['battle_leaders'], $per_page);
    $context['battle_display'] = battle_pages($txt['battle_page'], '#battle_main', $scripturl . '?action=battle;sa=leaderboard', $context['current_pages']);
}

function battle_howto()
{
    global $txt, $context;

    $context['sub_template']  = 'howto';
    $context['page_title'] = $txt['battle_howto'];
}

function battle_memset()
{
    global $context, $txt, $user_info;

    $context['sub_template']  = 'battle_mem_set';
    $context['page_title'] = $txt['battle_game_set'];
    // Are we saving?
    if(isset($_GET['save']))
    {
        $battle_pm = isset($_REQUEST['battle_pm'])? 1 : 0;
        $battle_only_buddies_shout = isset($_REQUEST['battle_only_buddies_shout'])? 1 : 0;
        updateMemberData($user_info['id'], array('bpm' => $battle_pm, 'battle_only_buddies_shout' => $battle_only_buddies_shout));
	redirectexit('action=battle;sa=settings;#battle_main');
    }
}

function battle_level($page = 'action=battle;sa=main;done;#battle_main')
{
    global $modSettings, $txt, $user_info;

    // level Up
    $need_exp = $modSettings['exp_bef_level'] * pow(1.00, $user_info['max_exp']);

    if ($user_info['exp'] >= $user_info['max_exp'])
    {
        $final_value2 =  $user_info['max_exp'] + $need_exp;
        $level =  $user_info['level'] + 1;
        $stat_point =  $user_info['stat_point'] + $modSettings['exp_stat_level'];
        updateMemberData($user_info['id'], array('max_exp' => $final_value2, 'level' => $level, 'stat_point' => $stat_point, 'battle_last' => time()));
        $user_info['level'] = $level;
	$user_info['max_exp'] = $final_value2;
	$user_info['stat_point'] = $stat_point;

        if($modSettings['enable_battle_hist'])
        {
            $content = ''.$user_info['name'].' '.$txt['battle_hist26'].' '.$level.'';
            add_to_battle_hist($content);
        }

        redirectexit($page);
    }
}

function battle_init()
{
    global $modSettings, $user_info;

    //money amount to add for each round
    $add_money = $modSettings['battle_add_amount'];

    //coefficient to use (5% addon = 1.05)
    $k_money = 1.05;

    //time now, and round time in seconds (1h=60*60seconds=3600)
    $time_now = time();
    $round = $modSettings['battle_time'];

    //time of last update+time needed must be smaller then time now to update
    if (($user_info['lastupdate']+$round) <= $time_now)
    {
        //see how many rounds (hours) were there from last update
        $nr_rounds = floor(($time_now-$user_info['lastupdate'])/$round);
        $all_money = $nr_rounds * $add_money * pow($k_money, $user_info['max_hp']);

        //calculate how many rounds in seconds (how many hours in seconds)
        $add_time = $nr_rounds * $round;

        //lets update users table
        $final_value1 =  $user_info[$modSettings['bcash']] + $add_money;
        updateMemberData($user_info['id'], array($modSettings['bcash'] => $final_value1));
        $final_value2 =  $user_info['lastupdate'] + $add_time;
        updateMemberData($user_info['id'], array('lastupdate' => $final_value2));
    }
}

function battle_Insert_dead($id_member,$id_target,$name,$time)
{
    global $smcFunc,  $monster, $sourcedir, $damage, $settings, $scripturl, $context, $txt, $modSettings, $user_info;

    $smcFunc['db_insert']('','
		{db_prefix}battle_graveyard',
		array('id_mem' => 'string', 'id_memdef' => 'string', 'name' => 'string', 'date' => 'string'),
		array($id_member, $id_target, $name, $time),
		array()
            );
}

function battle_insert_champ($id_champ, $slain, $count = 0)
{
    global $smcFunc;

    if (empty($id_champ) || empty($slain))
        return false;

    $request = $smcFunc['db_query']('', "
			SELECT times_champ
			FROM {db_prefix}battle_champs
			WHERE id_champ = {int:champ} AND id_slain = {int:slain}
                        LIMIT 1",
                        array('champ' => $id_champ, 'slain' => $slain)
                    );

    while ($row = $smcFunc['db_fetch_assoc']($request))
        $count = !empty($row['times_champ']) ? $row['times_champ'] : 0;

    $smcFunc['db_free_result']($request);

    $smcFunc['db_insert'](
                'replace',
		'{db_prefix}battle_champs',
		array('id_champ' => 'int', 'id_slain' => 'int', 'times_champ' => 'int', 'date' => 'int'),
		array($id_champ, $slain, $count+1, time()),
		array('id_champ')
            );
}

function battle_game_over()
{
	global $modSettings, $scripturl, $smcFunc, $user_info, $sourcedir, $boardurl;

	// Check to see if the game is flagged as ended
	setcookie("battle_start_time", false, time()+3600, '/');
	$checkComplete = array();
	if (!empty($modSettings['battle_points']))
	{
		$checkThisLvl['level'] = $user_info['level'] >= $modSettings['battle_points'] ? true : false;
		list($checkEndLvl, $check, $check, $checkComplete, $countIds) = array(false, false, array('battle_last' => 0), array(), array());
		$reset_time = (!empty($modSettings['battle_reset_time']) ? abs((int)$modSettings['battle_reset_time']) : 0);
		require_once($sourcedir . '/Subs-Members.php');

		if (!empty($modSettings['battle_players_lvl']))
		{
		    $request = $smcFunc['db_query']('','
		    	SELECT level
			FROM {db_prefix}members
			WHERE level >= {int:end_level}
			LIMIT 1',
			array('end_level' => $modSettings['battle_points'])
		    );
		    $checkEndLvl = $smcFunc['db_fetch_assoc']($request);
		    $smcFunc['db_free_result']($request);
		}
		elseif (!empty($reset_time))
		{
		    $request = $smcFunc['db_query']('','
		    	SELECT level
			FROM {db_prefix}members
			WHERE level >= {int:end_level}',
			array('end_level' => $modSettings['battle_points'])
		    );
		    while ($row = $smcFunc['db_fetch_assoc']($request))
			$checkComplete[] = $row['level'];

		    $smcFunc['db_free_result']($request);

		    $countIds = membersAllowedTo('view_battle');

		    if (!empty($countIds) && count($checkComplete) >= count($countIds) && count($checkComplete) > 0)
			$checkEndLvl = true;
		}

		// auto reset game?
		if ($checkEndLvl && !empty($reset_time))
		{
		    $request = $smcFunc['db_query']('','
		    	SELECT battle_last
			FROM {db_prefix}members
			ORDER BY battle_last DESC
			LIMIT 1'
		    );
		    $check = $smcFunc['db_fetch_assoc']($request);
		    $smcFunc['db_free_result']($request);

		    setcookie("battle_start_time", date('Y-m-d h:ia', ($check['battle_last'] + round(abs($reset_time * 3600)))), '/');
		    if ($check['battle_last'] > 0 && (time() - $check['battle_last']) / 3600 >= round(abs($reset_time)))
		    {
			$context['battle_reset_points'] = true;
			require_once($sourcedir . '/BattleAdmin.php');
			battle_reset_points(true);
			battle_reset(true);

			redirectexit($scripturl . '?action=battle;sa=main;home;#battle_main');
		    }
		}

		if (!empty($checkThisLvl['level']) || !empty($checkEndLvl['level']))
		    redirectexit($scripturl . '?action=battle;sa=stats;#battle_main');

	}

	return false;
}

function battle_getQuest($questId, $questCamp)
{

    global $smcFunc, $context, $user_info, $txt, $settings;
    $questData = array();
    $request = $smcFunc['db_query']('', "
		SELECT b.id_quest, b.gold, b.itext, b.stext, b.ftext, b.stext, b.exp, b.is_final, b.min_gold, b.max_penalty, b.campaign_id,
		b.level, b.success, b.name, b.plays, b.energy, b.min_exp, b.hp, b.max_gain, b.limit,
		q.id_warrior, q.exp_points, q.warrior_gold, q.fail, q.complete
		FROM {db_prefix}battle_quest AS b
		LEFT JOIN {db_prefix}battle_quest_champs AS q ON (q.id_quest = b.id_quest AND q.id_warrior = {int:id_member})
		WHERE b.id_quest >= {int:quest}
		AND b.campaign_id = {int:camp}
		ORDER BY b.level ASC
		LIMIT {int:limit}",
		array(
			'id_member' => $user_info['id'],
			'limit' => 2,
			'quest' => (int)$questId,
			'camp' => (!empty($questCamp['id_campaign']) ? (int)$questCamp['id_campaign'] : 0)
		)
	);

	// Loop through all results and add them to the list
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
	    if (empty($questData))
	    {
	        $campaign = !empty($questCamp) ? $questCamp : array('id_campaign' => 0, 'timed_campaign' => 0, 'level_completion' => 0, 'end_time' => 0, 'start_time' => 0, 'img' => false, 'campaign_name' => false);
	        $currentLvlX = !empty($row['campaign_id']) ? battle_campaigns_list($user_info['id'], $row['campaign_id']) : 0;
	        $currentLvlY = (!empty($currentLvlX[$row['campaign_id']]['level_completion']) ? (int)$currentLvlX[$row['campaign_id']]['level_completion'] : 0);
	        $currentLvlZ = !empty($row['campaign_id']) ? battle_campaigns_query($row['campaign_id']) : array('level_completion' => 0);
	        $currentLvl = (!empty($row['campaign_id']) && empty($currentLvlY) ? $currentLvlZ['level_completion'] : $currentLvlY);

	        $questData = array(
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
			'start_time' => $campaign['start_time'],
			'end_time' => $campaign['end_time'],
			'exp_points' => !empty($row['exp_points']) ? (int)$row['exp_points'] : 0,
			'warrior_gold' => !empty($row['gold']) ? (int)$row['gold'] : 0,
			'thisLevel' => !empty($row['level']) ? (int)$row['level'] : 0
		);
	    }
	    else
	        $questData['nextLevel'] = !empty($row['level']) ? (int)$row['level'] : 0;
	}

	$smcFunc['db_free_result']($request);

	return $questData;
}
?>