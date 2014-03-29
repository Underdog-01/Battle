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

function battle_fight()
{
	global $smcFunc,  $monster, $sourcedir, $damage, $settings, $scripturl, $context, $txt, $modSettings, $user_info, $user_profile;
	//We Need Our Template
	$context['sub_template']  = 'battle_fight';
	$context['page_title'] = $txt['battle'];
	$context['battle_message'] = false;
	$checkSession = 9;
	$gamePoints = 0;
	$gameOppPoints = 0;
	$session = !empty($_REQUEST['session']) ? $_REQUEST['session'] : false;
	$context['battle_ErrorBack']['change'] = $context['battle_ErrorBack']['battle'];

	$attack = !empty($_REQUEST['attack']) ? (int) $_REQUEST['attack'] : 0;
	if ($attack == $user_info['id'])
		fatal_error($txt['battle_error5'] , false);

	// Query the database for the opponent information.
	$request = $smcFunc['db_query']('','
		SELECT def, atk, hp, real_name, id_member, energy, stamina, level, exp, stat_point, member_name, battle_points,
		bm.member_id, bm.opponent_id, bm.battles, bm.kills, bm.battles_date, bm.kills_date
		FROM {db_prefix}members
		LEFT JOIN {db_prefix}battle_members as bm ON (bm.member_id = {int:member} AND bm.opponent_id = id_member)
		WHERE id_member = {int:attack}
		LIMIT {int:limit}',
		array('member' => $user_info['id'], 'limit' => 1, 'attack' => $attack)
	);

	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$memberName = !empty($row['member_name']) ? $row['member_name'] : $txt['battle_mem'] . '_' . $row['id_member'];
		$context['battle_target'] = array(
			'def' => $row['def'],
			'atk' => $row['atk'],
			'hp' => $row['hp'],
			'real_name' => !empty($row['real_name']) ? $row['real_name'] : $memberName,
			'id_member' => $row['id_member'],
			'energy' => $row['energy'],
			'stamina' => $row['stamina'],
			'level' => $row['level'],
			'exp' => $row['exp'],
			'stat_point' => $row['stat_point'],
			'battles' => !empty($row['battles']) ? $row['battles'] : 0,
			'kills' => !empty($row['kills']) ? $row['kills'] : 0,
			'battles_date' => !empty($row['battles_date']) ? $row['battles_date'] : 0,
			'kills_date' => !empty($row['kills_date']) ? $row['kills_date'] : 0,
			'battle_points' => !empty($row['battle_points']) ? $row['battle_points'] : 0
		);
	}
	$smcFunc['db_free_result']($request);

	$killsLimit = !empty($modSettings['battle_mem_kill_limit']) ? $modSettings['battle_mem_kill_limit'] : 0;
	$battlesLimit = !empty($modSettings['battle_mem_battle_limit']) ? $modSettings['battle_mem_battle_limit'] : 0;

	if (empty($context['battle_target']))
		fatal_error($txt['battle_error7'], false);

	if (!empty($context['battle_target']['battles_date']) && time() - $context['battle_target']['battles_date'] >= 86400)
	{
		$smcFunc['db_insert']('replace',
			'{db_prefix}battle_members',
			array('member_id' => 'int', 'opponent_id' => 'int', 'battles' => 'int', 'kills' => 'int', 'battles_date' => 'int', 'kills_date' => 'int'),
			array($user_info['id'], $context['battle_target']['id_member'], 1, $context['battle_target']['kills'], time(), $context['battle_target']['kills_date']),
			array('member_id', 'opponent_id')
		);
		$context['battle_target']['battles'] = 1;
		$context['battle_target']['battles_date'] = time();
	}
	elseif (empty($context['battle_target']['battles_date']))
	{
		$smcFunc['db_insert']('insert',
			'{db_prefix}battle_members',
			array('member_id' => 'int', 'opponent_id' => 'int', 'battles' => 'int', 'kills' => 'int', 'battles_date' => 'int', 'kills_date' => 'int'),
			array($user_info['id'], $context['battle_target']['id_member'], 1, 0, time(), 1),
			array('member_id', 'opponent_id')
		);
		$context['battle_target']['battles'] = 1;
		$context['battle_target']['battles_date'] = time();
		$context['battle_target']['kills_date'] = 1;
	}
	elseif (!empty($context['battle_target']['battles']) && $context['battle_target']['battles'] <= $battlesLimit)
	{
		$smcFunc['db_insert']('replace',
			'{db_prefix}battle_members',
			array('member_id' => 'int', 'opponent_id' => 'int', 'battles' => 'int', 'kills' => 'int', 'battles_date' => 'int', 'kills_date' => 'int'),
			array($user_info['id'], $context['battle_target']['id_member'], $context['battle_target']['battles']+1, $context['battle_target']['kills'], time(), $context['battle_target']['kills_date']),
			array('member_id', 'opponent_id')
		);
		$context['battle_target']['battles']++;
		$context['battle_target']['battles_date'] = time();
	}
	elseif ($battlesLimit > 0)
		fatal_error($txt['battle_error11'], false);

	if ($context['battle_target']['kills_date'] > 1 && time() - $context['battle_target']['kills_date'] >= 604800)
	{
		$smcFunc['db_insert']('replace',
			'{db_prefix}battle_members',
			array('member_id' => 'int', 'opponent_id' => 'int', 'battles' => 'int', 'kills' => 'int', 'battles_date' => 'int', 'kills_date' => 'int'),
			array($user_info['id'], $context['battle_target']['id_member'], 1, 0, time(), time()),
			array('member_id', 'opponent_id')
		);
		$context['battle_target']['battles_date'] = time();
		$context['battle_target']['kills_date'] = time();
		$context['battle_target']['battles'] = 1;
		$context['battle_target']['kills'] = 0;
	}
	elseif ($context['battle_target']['kills'] > $killsLimit && $killsLimit > 0)
		fatal_error($txt['battle_error12'], false);

	$addedLvl = array('opponent' => round((!empty($user_info['level']) ? $user_info['level'] : 0) / 25), 'member' => round((!empty($context['battle_target']['level']) ? $context['battle_target']['level'] : 0) / 25));
	if (!empty($session))
	{
		$check = 9 * ((!empty($user_info['hp']) ? $user_info['hp'] : 9) + (!empty($user_info['energy']) ? $user_info['energy'] : 9) + (!empty($user_info['stamina']) ? $user_info['stamina'] : 9) + (!empty($user_info['exp']) ? $user_info['exp'] : 9));
		if ($session !== $context['session_id'] . $check)
			fatal_error($txt['battle_cheatrefresh'], false);
	}
	else
		fatal_error($txt['battle_cheatrefresh'], false);

	$lvlAllowance = !empty($modSettings['battle_level_mem']) ? (int)$modSettings['battle_level_mem'] : 0;
	$userLvl = !empty($context['battle_target']['level']) ? (int)$context['battle_target']['level'] : 0;
	if ($lvlAllowance > -1 && ($user_info['level'] - $lvlAllowance) > $userLvl)
		fatal_error($txt['battle_error10'], false);

	// Figure out the attack and defense and who is better.
	$which_atk = 0; $which_def = 0; $this_atk = 0; $this_def = 0;
	if ($user_info['def'] > $context['battle_target']['atk'])
		$which_atk = $context['battle_target']['atk'] - $user_info['def'];

	if ($user_info['atk'] > $context['battle_target']['def'])
		$which_def = $context['battle_target']['def'] - $user_info['atk'];

	if ($context['battle_target']['def'] > $user_info['atk'])
		$this_atk = $user_info['atk'] - $context['battle_target']['def'];

	if ($context['battle_target']['atk'] > $user_info['def'])
		$this_def = $user_info['def'] - $context['battle_target']['atk'];

	// Figure out damage. Put a random seed in, just in case opponents are equal.
	if ($user_info['energy'] > 0 && $user_info['stamina'] > 0 && $user_info['hp'] > 0)
	{
		$this_def = $this_def + mt_rand (0, 5);
		$this_atk = $this_atk + mt_rand (0, 5);
		$which_def = $which_def + mt_rand (0, 5);
		$which_atk = $which_atk + mt_rand (0, 5);
		if ($which_atk > $this_def)
			$this_dmg = $which_atk - $this_def;
		else
			$this_dmg = mt_rand (0, 5);

		if ($this_atk > $which_def)
			$which_dmg = $this_atk - $which_def;
		else
			$which_dmg = mt_rand (0, 5);

		// deduct stats
		if ($context['battle_target']['hp'] > 0)
		{
			// Calculate hp for target and user
			$this_hp = $user_info['hp'] - $this_dmg;
			$which_hp = $context['battle_target']['hp'] - $which_dmg;
			if ($this_hp < 0)
				$this_hp = 0;

			if ($which_hp < 0)
				$which_hp = 0;

			if ($user_info['stamina'] > 0)
				$this_stam =  $user_info['stamina'] - 1;
		}
		else
			fatal_error($txt['battle_game_error_toolate'], false);
	}
	else
		fatal_error($txt['battle_game_error_rrro'], false);

	// Get ready to apply everything
	$t = time();
	$enemy_strength = round($addedLvl['member'] + abs(ceil((!empty($context['battle_target']['def']) ? $context['battle_target']['def'] : 1) + (!empty($context['battle_target']['atk']) ? $context['battle_target']['atk'] : 0) / 100)) / 2);
	$member_strength = round($addedLvl['opponent'] + abs(ceil((!empty($user_info['def']) ? $user_info['def'] : 1) + (!empty($user_info['atk']) ? $user_info['atk'] : 0) / 100)) / 2);

	if ($which_dmg < $this_dmg)
	{
		// you lost therefore deduct stamina and energy
		$stam = array('lost', mt_rand(0, 3));
		$energy = array('lost', mt_rand(0,3));
		$this_stam = $user_info['stamina'] - $stam[1] < 1 ? 0 : round($user_info['stamina'] - $stam[1]);
		$this_energy = $user_info['energy'] - $energy[1] < 1 ? 0 : round($user_info['energy'] - $energy[1]);

		updateMemberData($user_info['id'], array('stamina' => $this_stam, 'energy' => $this_energy));
		$user_info['stamina'] = $this_stam;
		$user_info['energy'] = $this_energy;
	}
	elseif ($which_dmg > $this_dmg)
	{
		// you won therefore add stamina, energy and stat points
		$stam = array('exp', mt_rand(0, 3));
		$energy = array('exp', mt_rand(0,3));
		$this_stam = $user_info['stamina'] + $stam[1] >= $user_info['max_stamina'] ? $user_info['max_stamina'] : $user_info['stamina'] + $stam[1];
		$this_energy = $user_info['energy'] + $energy[1] >= $user_info['max_energy'] ? $user_info['max_energy'] : $user_info['energy'] + $energy[1];

		// add experience if you defeated them and are less or equal to their level...
		if (!empty($modSettings['battle_exp_restrict_membattle']) && $user_info['level'] <= $context['battle_target']['level'] && $which_hp < 1)
		{
			$expNew = mt_rand (0, (!empty($modSettings['exp_def_mem']) ? (int)$modSettings['exp_def_mem'] : 0));
			$expNew = $expNew < 1 ? 0 : $expNew;
			$this_exp = $user_info['exp'] + $expNew;
		}
		elseif (empty($modSettings['battle_exp_restrict_membattle']) && $which_hp < 1)
		{
			$expNew = mt_rand (0, (!empty($modSettings['exp_def_mem']) ? (int)$modSettings['exp_def_mem'] : 0));
			$expNew = $expNew < 1 ? 0 : $expNew;
			$this_exp = $user_info['exp'] + $expNew;
		}
		else
			$this_exp = $user_info['exp'];

		updateMemberData($user_info['id'], array('stamina' => $this_stam, 'energy' => $this_energy, 'exp' => $this_exp));
		$user_info['stamina'] = $this_stam;
		$user_info['energy'] = $this_energy;
		$user_info['exp'] = $this_exp;
	}

	// we can't make this too easy, therefore always deduct atk and def from both players!
	$atk = array('lost', mt_rand(0, 5) + round($enemy_strength/45));
	$def = array('lost', mt_rand(0, 5) + round($enemy_strength/45));
	$member_atk = array('lost', mt_rand(0, 5) + round($member_strength/45));
	$member_def = array('lost', mt_rand(0, 5) + round($member_strength/45));
	$c_atk = $user_info['atk'] - $atk[1] < 1 ? 0 : round($user_info['atk'] - $atk[1]);
	$c_def = $user_info['def'] - $def[1] < 1 ? 0 : round($user_info['def'] - $def[1]);
	$member_c_atk = $context['battle_target']['atk'] - $member_atk[1] < 1 ? 0 : round($context['battle_target']['atk'] - $member_atk[1]);
	$member_c_def = $context['battle_target']['def'] - $member_def[1] < 1 ? 0 : round($context['battle_target']['def'] - $member_def[1]);
	updateMemberData($user_info['id'], array('atk' => $c_atk, 'def' => $c_def));
	updateMemberData($context['battle_target']['id_member'], array('atk' => $member_c_atk, 'def' => $member_c_def));
	$user_info['atk'] = $c_atk;
	$user_info['def'] = $c_def;
	$context['battle_target']['atk'] = $member_c_atk;
	$context['battle_target']['def'] = $member_c_def;
	$oppExpNew = 0;

	if (!empty($this_hp))
	{
		updateMemberData($user_info['id'], array('hp' => $this_hp));
		$user_info['hp'] = $this_hp;
	}
	else
	{
		$oppStatPoint = $addedLvl['opponent'] + mt_rand(1,2);
		$currentStatPoint = !empty($context['battle_target']['stat_point']) ? $context['battle_target']['stat_point'] : 0;
		updateMemberData($user_info['id'], array('is_dead' => 1, 'hp' => 0));
		battle_Insert_dead($context['battle_target']['id_member'],$user_info['id'],$user_info['username'],$t);
		battle_insert_champ($context['battle_target']['id_member'], $user_info['id']);
		loadMemberData(array($context['battle_target']['id_member']),false, 'normal');
		if (!empty($modSettings['battle_exp_restrict_membattle']) && $context['battle_target']['level'] <= $user_info['level'])
		{
			$oppExpNew = mt_rand (0, (!empty($modSettings['exp_def_mem']) ? (int)$modSettings['exp_def_mem'] : 0));
			$oppExpNew = $oppExpNew < 1 ? 0 : $oppExpNew;
			$opponentExp = $context['battle_target']['exp'] + $oppExpNew;
		}
		elseif (empty($modSettings['battle_exp_restrict_membattle']))
		{
			$oppExpNew = mt_rand (0, (!empty($modSettings['exp_def_mem']) ? (int)$modSettings['exp_def_mem'] : 0));
			$oppExpNew = $oppExpNew < 1 ? 0 : $oppExpNew;
			$opponentExp = $context['battle_target']['exp'] + $oppExpNew;
		}
		else
			$opponentExp = $context['battle_target']['exp'];

		$gameOppPoints = round((($user_info['atk'] * $user_info['def']) / 100 + abs($oppExpNew)) / 20);
		$target_slays = !empty($user_profile[$row['id_slain']]['mem_slays']) ?  $user_profile[$row['id_slain']]['mem_slays'] : 0;
		updateMemberData($context['battle_target']['id_member'], array('mem_slays' => $target_slays+1, 'exp' => $opponentExp,  'stat_point' => $currentStatPoint + $oppStatPoint, 'battle_points' => $context['battle_target']['battle_points'] + $gameOppPoints));
		$user_info['hp'] = 0;
		$bhist = $context['battle_target']['real_name'] . ' ' . str_replace('&@!@%', $oppStatPoint, $txt['battle_member_stat_points']);
		add_to_battle_hist($bhist);
	}

	if ((empty($which_hp)) || $which_hp < 1)
	{
		$statPoint = ceil(($addedLvl['member'] + mt_rand(1,2)) /4);
		$currentStatPoint = !empty($user_info['stat_point']) ? $user_info['stat_point'] : 0;
		$user_exp = !empty($user_info['exp']) ? $user_info['exp'] : 0;
		$plus_exp = !empty($modSettings['exp_def_mem']) ? mt_rand(0, $modSettings['exp_def_mem']) : 0;
		$this_slays = !empty($user_info['mem_slays']) ? $user_info['mem_slays']+1 : 1;
		$gamePoints = round((($context['battle_target']['atk'] * $context['battle_target']['def']) / 100 + abs($plus_exp)) / 20);
		updateMemberData($context['battle_target']['id_member'], array('is_dead' => 1, 'hp' => 0));
		updateMemberData($user_info['id'], array('mem_slays' => $this_slays, 'exp' => $user_exp+$plus_exp, 'stat_point' => $currentStatPoint + $statPoint, 'battle_points' => !empty($user_info['battle_points']) ?  $user_info['battle_points'] + $gamePoints : $gamePoints));
		battle_Insert_dead($user_info['id'],$context['battle_target']['id_member'],$context['battle_target']['real_name'],$t);
		battle_insert_champ($user_info['id'], $context['battle_target']['id_member']);
		$user_info['mem_slays'] = $this_slays;
		$user_info['stat_point'] = $currentStatPoint + $statPoint;
		$userName = !empty($user_info['username']) ? $user_info['username'] : $txt['battle_mem'] . '_' . $user_info['id'];
		$bhist = (!empty($user_info['name']) ? $user_info['name'] : $userName) . ' ' . str_replace('&@!@%', $statPoint, $txt['battle_member_stat_points']);
		add_to_battle_hist($bhist);

		if (empty($context['battle_target']['battles_date']))
			$smcFunc['db_insert']('insert',
						'{db_prefix}battle_members',
						array('member_id' => 'int', 'opponent_id' => 'int', 'battles' => 'int', 'kills' => 'int', 'battles_date' => 'int', 'kills_date' => 'int'),
						array($user_info['id'], $context['battle_target']['id_member'], $context['battle_target']['battles'], $context['battle_target']['kills']+1, time(), time()),
						array('member_id', 'opponent_id')
					);
		else
			$smcFunc['db_insert']('replace',
						'{db_prefix}battle_members',
						array('member_id' => 'int', 'opponent_id' => 'int', 'battles' => 'int', 'kills' => 'int', 'battles_date' => 'int', 'kills_date' => 'int'),
						array($user_info['id'], $context['battle_target']['id_member'], $context['battle_target']['battles'], $context['battle_target']['kills']+1, time(), time()),
						array('member_id', 'opponent_id')
					);
	}
	else
		updateMemberData($context['battle_target']['id_member'], array('hp' => $which_hp));


	//Some stuff to show the user depending on the outcome.
	if ($which_dmg < $this_dmg)
	{
		// You lost
		$context['battle_message'] .= '
			<div class="centertext"><img border="0" src="'.$settings['images_url'].'/battle/win-lose/loser.png" alt=""/></div>
			<br />
			<div class="centertext">
				'.$txt['battle_game_lost_you1'] .' '.$which_dmg.' '.$txt['battle_game_lost_you2'].'
				<strong>'.$context['battle_target']['real_name'].'</strong> '.$txt['battle_game_lost_you3'].' '.$this_dmg.' '.$txt['battle_game_pm_msg1'].'
			</div>
			<br  />';
	}
	elseif ($which_dmg > $this_dmg)
	{
		// You won
		$context['battle_message'] .= '
			<div class="centertext"><img border="0" src="'.$settings['images_url'].'/battle/win-lose/victory.png" alt=""/></div>
			<br />
			<div class="centertext">
				'.$txt['battle_game_won_you'].' '.$which_dmg.' '.$txt['battle_game_lost_you2'] .'
				<strong>'.$context['battle_target']['real_name'].'</strong> '.$txt['battle_game_lost_you3'].' '.$this_dmg.' '.$txt['battle_game_pm_msg1'].'.
			</div>
			<br  />';
	}
	else
	{
		// You tied
		$context['battle_message'] .= '
			<div class="centertext"><img border="0" src="'.$settings['images_url'].'/battle/win-lose/draw.png" alt=""/></div>
			<br />
			<div class="centertext">
				'.$txt['battle_game_tied_you'].' '.$which_dmg.' '.$txt['battle_game_lost_you2'].'
				<strong>'.$context['battle_target']['real_name'].'</strong> '.$txt['battle_game_lost_you3'].' '.$this_dmg.' '.$txt['battle_game_pm_msg1'].'.
			</div>
			<br  />';
	}

	// If they got some experience show them.
	if (!empty($expNew))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_exp'] . ' ' . $expNew . ' ' . $txt['battle_game_this_exp1'] . '</div>';
	if (!empty($stam[1]))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_' . $stam[0]] . ' ' . $stam[1] . ' ' . $txt['battle_game_this_stam1'] . '</div>';
	if (!empty($energy[1]))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_' . $energy[0]] . ' ' . $energy[1] . ' ' . $txt['battle_game_this_energy1'] . '</div>';
	if (!empty($atk[1]))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_' . $atk[0]] . ' ' . $atk[1] . ' ' . $txt['battle_game_this_atk1'] . '</div>';
	if (!empty($def[1]))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_' . $def[0]] . ' ' . $def[1] . ' ' . $txt['battle_game_this_def1'] . '</div>';
	if (!empty($statPoint))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_exp'] . ' ' . $statPoint . ' ' . $txt['battle_game_this_stat1'] . '</div>';
	if (!empty($gamePoints))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_points1'] . ' ' . $gamePoints . ' ' . $txt['battle_game_this_points2'] . '</div>';
	if (!empty($gameOppPoints))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_points3'] . ' ' . $gameOppPoints . ' ' . $txt['battle_game_this_points4'] . '</div>';


	// How about a battle again link? That is if no one is dead.
	$checkSession = 9 * ((!empty($user_info['hp']) ? $user_info['hp'] : 9) + (!empty($user_info['energy']) ? $user_info['energy'] : 9) + (!empty($user_info['stamina']) ? $user_info['stamina'] : 9) + (!empty($user_info['exp']) ? $user_info['exp'] : 9));
	if ($this_hp > 0 && $which_hp > 0)
	{
		$context['battle_message'] .= '<br />
			<div class="centertext">
				'.$txt['battle_this_member'].' '.$which_hp.' '.$txt['battle_points_left'].'
			</div>';


		if (($this_hp < 21 && $this_hp > 0) || $this_hp/10 < 2)
			$context['battle_message'] .= '
			<br />
			<div class="centertext">
				'. $txt['battle_hp_low']. ' ~ ' . $txt['battle_return_shop'] . '
			</div>';

		$context['battle_message'] .= '
			<br />
			<form action="' . $scripturl . '?action=battle;sa=main;home;#battle_main" method="post">
				<div class="centertext">
					<input type="submit" value ="'.$txt['battle_return_home'].'" />
				</div>
			</form>
			<div class="centertext">
				' . $txt['battle_or'] . '
			</div>
			<form action="' . $scripturl . '?action=battle;sa=fight;attack=' . $attack . ';session=' . $context['session_id'] . $checkSession . ';home;#battle_main" method="post">
				<div class="centertext">
					<input type="submit" value ="' . $txt['battle_ex_atk_again'] . '" />
				</div>
			</form>';
	}

	if ($this_hp < 1)
	{
		// You were killed
		if ($which_hp < 1)
		{
			// Did they also die in this battle?
			$context['battle_message'] .= '
			<br />
			<div class="centertext">
				'.$txt['battle_game_102'].' <strong>'.$context['battle_target']['real_name'].'</strong>.
			</div>';

			$bhist = $user_info['name'].' '.$txt['battle_hist25'].' '.$context['battle_target']['real_name'];

			if($modSettings['enable_battle_hist'])
				add_to_battle_hist($bhist);

		}

		$context['battle_message'] .= '
			<div class="centertext">
				<br />'.$txt['battle_game_99'].' <strong>'.$context['battle_target']['real_name'].'</strong> '.$txt['battle_game_101'].'
			</div>
			<br />
			<form action="' . $scripturl . '?action=battle;sa=shop;home;#battle_main" method="post">
				<div class="centertext">
					<input type="submit" value ="'.$txt['battle_visit_shop'].'" />
				</div>
			</form>
			<div class="centertext">
				' . $txt['battle_or'] . '
			</div>
			<form action="' . $scripturl . '?action=battle;sa=main;home;#battle_main" method="post">
				<div class="centertext">
					<input type="submit" value ="'.$txt['battle_return_home'].'" />
				</div>
			</form>';
		$output = str_replace('&#*@&', $context['battle_target']['real_name'], $txt['battle_hist30']);
		$output = str_replace('@$%#%', $oppExpNew, $output);
		$bhist = $user_info['name'] . ' ' . $txt['battle_hist23'] . ' ' . $context['battle_target']['real_name'] . ' ' . $txt['battle_hist24'] . ($oppExpNew > 0 ? $output : '');
	}

	if ($which_hp < 1 && $this_hp > 0)
	{
		// You killed them
		$context['battle_message'] .= '
			<br />
			<div class="centertext">
				'.$txt['battle_game_102'].' <strong>'.$context['battle_target']['real_name'].'</strong>.
			</div>
			<br />
			<form action="' . $scripturl . '?action=battle;sa=main;home;#battle_main" method="post">
				<div class="centertext">
					<input type="submit" value ="'.$txt['battle_return_home'].'" />
				</div>
			</form>
			<div class="centertext">
				' . $txt['battle_or'] . '
			</div>
			<form action="' . $scripturl . '?action=battle;sa=battle;home;#battle_main" method="post">
				<div class="centertext">
					<input type="submit" value ="' . $txt['battle'] . '" />
				</div>
			</form>';
		$bhist = $user_info['name'].' '.$txt['battle_hist25'].' '.$context['battle_target']['real_name'];
	}

	if($modSettings['enable_battle_hist'] && isset($bhist))
		add_to_battle_hist($bhist);

	$request = $smcFunc['db_query']('','
		SELECT bpm
		FROM {db_prefix}members
		WHERE  id_member = {int:attack}
		LIMIT 1',
		array(
			'attack' => $attack,
		)
	);

	list ($no_pm_battle) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	if ($no_pm_battle)
	{
		$msg = ''.$user_info['username'].' '.$txt['battle_game_pm_msg'].' '.$which_dmg.' '.$txt['battle_game_pm_msg1'].'
			[url='. $scripturl. '?action=battle;sa=battle;u='.$user_info['id'].';home;#battle_main]'.$txt['battle_game_pm_msg2'].'[/url]';

		require_once($sourcedir.'/Subs-Post.php');
		$pmfrom = array(
			'id' => 0,
			'name' => $txt['battle_game_pm_msg3'],
			'username' => $txt['battle_game_pm_msg3']
		);

		$pmto = array(
			'to' => array($context['battle_target']['id_member']),
			'bcc' => array()
		);
		sendpm($pmto, $txt['battle_game_pm_msg4'], $msg, 0, $pmfrom);
	}
}

function battle_fight_monster()
{
	global $sourcedir, $context, $attack_id, $user_info, $txt, $scripturl, $settings, $smcFunc, $modSettings;
	$context['sub_template']  = 'fmon';
	$context['battle_message'] = false;
	$comparison = array('def' => 'monster_def', 'atk' => 'monster_atk', 'max_hp' => 'monster_max_hp', 'hp' => 'monster_hp');
	$checkSession = !empty($_REQUEST['session']) ? $_REQUEST['session'] : false;
	$time = time();
	$context['battle_ErrorBack']['change'] = $context['battle_ErrorBack']['explore'];
	$context['page_title'] = $txt['battle_expl'];
	$attack_id = !empty($_REQUEST['mon']) ? (int) $_REQUEST['mon'] : 0;
	$gamePoints = 0;

	// First query the composite index table to see if this battle is current
	$request = $smcFunc['db_query']('','
		SELECT f.id_warrior, f.id_monster, f.monster_hp, f.monster_max_hp, f.monster_def, f.monster_atk, f.time,
			m.def, m.atk, m.max_hp, m.hp, m.name, m.mon_range, m.mon_max_range, m.evolve, m.counter, m.img
		FROM {db_prefix}battle_monsters as m
		LEFT JOIN {db_prefix}battle_monsters_fight AS f ON (f.id_monster = m.id_monster AND f.id_warrior = {int:user_id})
		WHERE m.id_monster = {int:attack_id}
		LIMIT 1',
		array('attack_id' => $attack_id, 'user_id' => $user_info['id'])
	);

	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$context['battle_target'] = array(
			'def' => (int)$row['def'],
			'mon_range' => !empty($row['mon_range']) ? (int)$row['mon_range'] : 0,
			'mon_max_range' => !empty($row['mon_max_range']) ? $row['mon_max_range'] : ((!empty($row['mon_range']) ? (int)$row['mon_range'] : 1) + 1),
			'atk' => (int)$row['atk'],
			'max_hp' => (int)$row['max_hp'],
			'hp' => (int)$row['hp'],
			'name' => $row['name'],
			'img' => !empty($row['img']) ? $row['img'] : '',
			'evolve' => !empty($row['evolve']) ? (int)$row['evolve'] : 0,
			'id_warrior' => !empty($row['id_warrior']) ? (int)$row['id_warrior'] : 0,
			'id_monster' => !empty($row['id_monster']) ? (int)$row['id_monster'] : 0,
			'monster_hp' => !empty($row['monster_hp']) ? (int)$row['monster_hp'] : 0,
			'monster_max_hp' => !empty($row['monster_max_hp']) ? (int)$row['monster_max_hp'] : 0,
			'monster_def' => !empty($row['monster_def']) ? (int)$row['monster_def'] : 0,
			'monster_atk' => !empty($row['monster_atk']) ? (int)$row['monster_atk'] : 0,
			'time' => !empty($row['time']) ? (int)$row['time'] : 0,
			'counter' => !empty($row['counter']) ? (int)$row['counter'] : 0,
		);
	}

	$smcFunc['db_free_result']($request);

	$monHp = !empty($context['battle_target']['hp']) ? $context['battle_target']['hp'] : 9;
	$monsterSession = 9 * ((!empty($user_info['exp']) ? $user_info['exp'] : 9) + (!empty($user_info['stamina']) ? $user_info['stamina'] : 9) + (!empty($attack_id) ? $attack_id : 9) + (!empty($context['battle_target']['monster_hp']) ? $context['battle_target']['monster_hp'] : $monHp) + (!empty($user_info['hp']) ? $user_info['hp'] : 9));

	if ($checkSession !== $context['session_id'] . $monsterSession)
		fatal_error($txt['battle_cheatrefresh'], false);

	// 24 hours is allotted between enemy/user fights else the enemy is reset
	if ($context['battle_target']['time'] && $time - $context['battle_target']['time'] < 3600)
	{
		foreach ($comparison as $key => $compare)
			$context['battle_target'][$key] = $context['battle_target'][$comparison[$key]];
	}
	elseif ($context['battle_target']['id_warrior'] == $user_info['id'])
	{
		$smcFunc['db_insert'](
			'replace',
			'{db_prefix}battle_monsters_fight',
		        array('id_warrior' => 'int', 'id_monster' => 'int', 'monster_hp' => 'int', 'monster_max_hp' => 'int', 'monster_def' => 'int', 'monster_atk' => 'int', 'time' => 'int'),
			array($user_info['id'], $attack_id, $context['battle_target']['hp'], $context['battle_target']['max_hp'] , $context['battle_target']['def'], $context['battle_target']['atk'], $time),
			array('id_warrior', 'id_monster')
		);
	}
	else
	{
		$smcFunc['db_insert'](
			'insert',
			'{db_prefix}battle_monsters_fight',
		        array('id_warrior' => 'int', 'id_monster' => 'int', 'monster_hp' => 'int', 'monster_max_hp' => 'int', 'monster_def' => 'int', 'monster_atk' => 'int', 'time' => 'int'),
			array($user_info['id'], $attack_id, $context['battle_target']['hp'], $context['battle_target']['max_hp'] , $context['battle_target']['def'], $context['battle_target']['atk'], $time),
			array('id_warrior', 'id_monster')
		);
	}

	// No matter what preceded this will ensure the correct settings
	foreach ($comparison as $key => $compare)
		$context['battle_target'][$comparison[$key]] = $context['battle_target'][$key];


	$which_atk = 0; $which_def = 0; $this_atk = 0; $this_def = 0;
	if ($user_info['def'] > $context['battle_target']['atk'])
		$which_atk = $context['battle_target']['atk'] - $user_info['def'];

	if ($user_info['atk'] > $context['battle_target']['def'])
		$which_def = $context['battle_target']['def'] - $user_info['atk'];

	if ($context['battle_target']['def'] > $user_info['atk'])
		$this_atk = $user_info['atk'] - $context['battle_target']['def'];

	if ($context['battle_target']['atk'] > $user_info['def'])
		$this_def = $user_info['def'] - $context['battle_target']['atk'];

	// Figure out damage. Put a random seed in, just in case opponents are equal.
	if ($user_info['energy'] > 0 && $user_info['stamina'] > 0 && $user_info['hp'] > 0)
	{
		$this_def = $this_def + mt_rand (0, 5) ;
		$this_atk = $this_atk + mt_rand (0, 5);
		$which_def = $which_def + mt_rand (0, 5);
		$which_atk = $which_atk + mt_rand (0, 5);
		if ($which_atk > $this_def)
			$this_dmg = $which_atk - $this_def;
		else
			$this_dmg = mt_rand (0, 5);

		if ($this_atk > $which_def)
			$which_dmg = $this_atk - $which_def;
		else
			$which_dmg = mt_rand (0, 5);

		//Lets deduct all the stats here since we know we are all good to go.
		// Calculate hp for target and user
		$this_hp = $user_info['hp'] - $this_dmg;
		$which_hp = $context['battle_target']['hp'] - $which_dmg;
		if ($this_hp < 0)
			$this_hp = 0;

		if ($which_hp < 0)
			$which_hp = 0;

		// Deduct stamina.
		if ($user_info['stamina'] > 0)
			$this_stam =  $user_info['stamina'] - 1;
	}
	else
		fatal_error($txt['battle_game_error_rrro'], false);

	$enemy_strength = abs((!empty($context['battle_target']['def']) ? $context['battle_target']['def'] : 1) + (!empty($context['battle_target']['atk']) ? $context['battle_target']['atk'] : 0));
	$enemy_strength = ceil($enemy_strength/200);
	$bonus = $enemy_strength > 50 ?  mt_rand(0, round($enemy_strength / (!empty($user_info['level']) ? $user_info['level']*250 : 0))) : 0;

	// experience and stat points are only gained if you defeated the monster/enemy
	if ((empty($which_hp)) || $which_hp < 1)
	{
		$this_exp = $bonus + mt_rand (0, (!empty($modSettings['exp_def_mon']) ? $modSettings['exp_def_mon'] : 0)) * $enemy_strength;
		$statPoint = ceil(($bonus + mt_rand(1,2))/4);
		$currentStatPoint = !empty($user_info['stat_point']) ? (int)$user_info['stat_point'] : 0;
		$gamePoints = round((($context['battle_target']['atk'] * $context['battle_target']['def']) / 100 + abs($bonus)) + mt_rand(1, round(($context['battle_target']['max_hp'] / 20))) / 100);
	}
	else
		$this_exp = 0;


	// atk and def are always deducted... imo this is more realistic
	$atk = array('lost', mt_rand(0, 3) * $enemy_strength);
	$def = array('lost', mt_rand(0, 3) * $enemy_strength);
	$c_atk = $user_info['atk'] - $atk[1] < 1 ? 0 : round($user_info['atk'] - $atk[1]);
	$c_def = $user_info['def'] - $def[1] < 1 ? 0 : round($user_info['def'] - $def[1]);
	updateMemberData($user_info['id'], array('atk' => $c_atk, 'def' => $c_def));
	$user_info['atk'] = $c_atk;
	$user_info['def'] = $c_def;

	//Some stuff to show the user depending on the outcome.
	if ($which_dmg < $this_dmg)
	{
		//Defeat
		$context['battle_message'] .= '
			<center><img border="0" src="'.$settings['images_url'].'/battle/win-lose/loser.png" alt=""/></center>
			<br />'.$txt['battle_game_lost_you1'] .' '.$which_dmg.' '.$txt['battle_game_lost_you2'].'
			<strong>'.$context['battle_target']['name'].'</strong> '.$txt['battle_game_lost_you3'].' '.$this_dmg.' '.$txt['battle_game_pm_msg1'].'';
	}
	elseif ($which_dmg > $this_dmg)
	{
		// Victory
		$context['battle_message'] .= '
			<center><img border="0" src="'.$settings['images_url'].'/battle/win-lose/victory.png" alt=""/></center>
			<br />'.$txt['battle_game_won_you'].' '.$which_dmg.' '.$txt['battle_game_lost_you2'].'
			<strong>'.$context['battle_target']['name'].'</strong> '.$txt['battle_game_lost_you3'].' '.$this_dmg.' '.$txt['battle_game_pm_msg1'].'.';
	}
	else
	{
		// Draw
		$context['battle_message'] .= '
			<center><img border="0" src="'.$settings['images_url'].'/battle/win-lose/draw.png" alt=""/></center>
			<br />'.$txt['battle_game_tied_you'].' '.$which_dmg.' '.$txt['battle_game_lost_you2'].'
			<strong>'.$context['battle_target']['name'].'</strong> '.$txt['battle_game_lost_you3'].' '.$this_dmg.' '.$txt['battle_game_pm_msg1'].'.';
	}

	// Get ready to apply everything
	$t = time();
	$checkThisSession = (!empty($attack_id) ? $attack_id : 9);
	if (!empty($this_hp))
	{
		updateMemberData($user_info['id'], array('hp' => $this_hp));
		$user_info['hp'] = $this_hp;
		$checkThisSession = $checkThisSession + $this_hp;
	}
	else
	{
		updateMemberData($user_info['id'], array('is_dead' => 1, 'hp' => 0));
		$checkThisSession = $checkThisSession + 9;
	}

	if (!empty($this_stam))
	{
		updateMemberData($user_info['id'], array('stamina' => $this_stam));
		$user_info['stamina'] = $this_stam;
	}

	if (!empty($this_exp))
	{
		$expGain = $this_exp;
		$this_exp =  $user_info['exp'] + $this_exp;
		updateMemberData($user_info['id'], array('exp' => $this_exp));
		$user_info['exp'] = $this_exp;
	}

	$energyCost = mt_rand(0, 3);
	if (!empty($energyCost))
	{
		$user_info['energy'] = $user_info['energy'] - $energyCost > 0 ? $user_info['energy'] - $energyCost : 0;
		updateMemberData($user_info['id'], array('energy' => $user_info['energy']));
	}

	if (!empty($statPoint))
	{
		updateMemberData($user_info['id'], array('stat_point' => $currentStatPoint + $statPoint));
		$user_info['stat_point'] = $currentStatPoint + $statPoint;
	}

	if (!empty($which_hp))
	{
		$smcFunc['db_insert'](
			'replace',
			'{db_prefix}battle_monsters_fight',
		        array('id_warrior' => 'int', 'id_monster' => 'int', 'monster_hp' => 'int', 'monster_max_hp' => 'int', 'monster_def' => 'int', 'monster_atk' => 'int', 'time' => 'int'),
			array($user_info['id'], $attack_id, $which_hp, $context['battle_target']['monster_max_hp'] , $context['battle_target']['monster_def'], $context['battle_target']['monster_atk'], $time),
			array('id_warrior', 'id_monster')
		);
		$checkThisSession = $checkThisSession + $which_hp;
	}
	else
		$checkThisSession = $checkThisSession + 9;

	$checkThisSession = 9 * ($checkThisSession + (!empty($user_info['stamina']) ? $user_info['stamina'] : 9) + (!empty($user_info['exp']) ? $user_info['exp'] : 9));

	// Adjust some stuff before the template is phased
	if ((empty($which_hp)) || $which_hp < 1)
	{
		$user_info['mon_slays'] = !empty($user_info['mon_slays']) ? $user_info['mon_slays'] + 1 : 1;
		$user_info['battle_points'] = !empty($user_info['battle_points']) ? $user_info['battle_points'] + $gamePoints : $gamePoints;
		updateMemberData($user_info['id'], array('mon_slays' => $user_info['mon_slays'], 'battle_points' =>  $user_info['battle_points']));

		$smcFunc['db_query']('','
			DELETE FROM {db_prefix}battle_monsters_fight
			WHERE id_monster = {int:id_monster} AND id_warrior = {int:id_member}',
				array('id_monster' => $attack_id, 'id_member' => $user_info['id'])
		);

		// Is the enemy set to evolve?
		$advancement = $context['battle_target']['counter'] > $context['battle_target']['evolve'] ? 1 : 0;
		$counter = $context['battle_target']['counter'] <= $context['battle_target']['evolve'] ? $context['battle_target']['counter'] + 1 : 0;
		if ($context['battle_target']['evolve'])
		{
			$stats = array('monster_max_hp' => $context['battle_target']['monster_max_hp'], 'monster_def' => $context['battle_target']['monster_def'], 'monster_atk' => $context['battle_target']['monster_atk']);
			$advance = array_rand($stats, 1);
			$context['battle_target'][$advance] = (int)$context['battle_target'][$advance] + $advancement;

			$smcFunc['db_insert'](
				'replace',
				'{db_prefix}battle_monsters',
				array('id_monster' => 'int', 'hp' => 'int', 'max_hp' => 'int', 'def' => 'int', 'atk' => 'int', 'img' => 'string', 'name' => 'string', 'mon_range' => 'int', 'mon_max_range' => 'int', 'evolve' => 'int', 'counter' => 'int'),
				array($attack_id, $context['battle_target']['monster_max_hp'], $context['battle_target']['monster_max_hp'], $context['battle_target']['monster_def'], $context['battle_target']['monster_atk'], $context['battle_target']['img'], $context['battle_target']['name'], $context['battle_target']['mon_range'], $context['battle_target']['mon_max_range'], $context['battle_target']['evolve'], $counter),
				array('id_warrior', 'id_monster')
			);

			foreach ($comparison as $key => $compare)
				$context['battle_target'][$comparison[$key]] = 0;
		}
	}

	// If they got some experience show them.
	if (!empty($expGain))
		$context['battle_message'] .= '<br />' . $txt['battle_game_this_exp'] . ' ' . $expGain . ' ' . $txt['battle_game_this_exp1'];
	if (!empty($energyCost))
		$context['battle_message'] .= '<br />' . $txt['battle_game_this_lost'] . ' ' . $energyCost . ' ' . $txt['battle_game_this_energy1'];
	if (!empty($atk[1]))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_' . $atk[0]] . ' ' . $atk[1] . ' ' . $txt['battle_game_this_atk1'] . '</div>';
	if (!empty($def[1]))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_' . $def[0]] . ' ' . $def[1] . ' ' . $txt['battle_game_this_def1'] . '</div>';
	if (!empty($statPoint))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_exp'] . ' ' . $statPoint . ' ' . $txt['battle_game_this_stat1'] . '</div>';
	if (!empty($gamePoints))
		$context['battle_message'] .= '<div class="centertext">' . $txt['battle_game_this_points1'] . ' ' . $gamePoints . ' ' . $txt['battle_game_this_points2'] . '</div>';

	// How about a battle again link? That is if no one is dead.
	if ($this_hp > 0 && $which_hp > 0)
	{
		if (($this_hp < 21 && $this_hp > 0) || $this_hp < $user_info['max_hp']/20)
			$context['battle_message'] .= '
			<br />
			<br />
			<center>
				'. $txt['battle_hp_low']. '
				<br />
				' . $txt['battle_return_shop'] . '
				<br />
			</center>';

		if ($this_hp > 0)
			$context['battle_message'] .= '<br />
		'.$txt['battle_this_enemy'].' '.$which_hp.' '.$txt['battle_points_left'].'
		<br /><br />
		<form action="' . $scripturl . '?action=battle;sa=explore;#battle_main" method="post">
			<input type="submit" value ="'.$txt['battle_run'].'" />
		</form>
		'.$txt['battle_or'].'<br />
		<form action="' . $scripturl . '?action=battle;sa=battle;sa=fm;mon=' . $_GET['mon'] . ';session=' . $context['session_id'] . $checkThisSession . ';home;#battle_main" method="post">
			<input type="submit" value ="' . $txt['battle_ex_atk_again'] . '" />
		</form>';
	}

	if ($this_hp < 1)
	{
		// You died
		if ($which_hp < 1)
		{
			// Did they also die in this battle?
			$context['battle_message'] .= '
		<br />
		<br />
		'.$txt['battle_game_102'].' <strong>'.$context['battle_target']['name'].'</strong>.';

			$bhist = ''.$user_info['name'].' '.$txt['battle_hist25'].' '.$context['battle_target']['name'];
			if($modSettings['enable_battle_hist'])
				add_to_battle_hist($bhist);
		}

		$context['battle_message'] .= '
		<br /><br />
		'.$txt['battle_game_99'] . ' <strong>' . $context['battle_target']['name'] . '</strong> ' . $txt['battle_game_101'] .'
		<br />
		<br />
		<form action="' . $scripturl . '?action=battle;sa=shop;home;#battle_main" method="post">
			<div class="centertext">
				<input type="submit" value ="'.$txt['battle_visit_shop'].'" />
			</div>
		</form>
		<div class="centertext">
			' . $txt['battle_or'] . '
		</div>
		<form action="' . $scripturl . '?action=battle;sa=main;home;#battle_main" method="post">
			<div class="centertext">
				<input type="submit" value ="'.$txt['battle_return_home'].'" />
			</div>
		</form>';
		$bhist = ''.$user_info['name'].' '.$txt['battle_hist23'].' '.$context['battle_target']['name'].' '.$txt['battle_hist24'].'';
		updateMemberData($user_info['id'], array('is_dead' => 1, 'hp' => 0));
		$t = time();
		battle_Insert_dead($user_info['id'],$user_info['id'],$user_info['name'],$t);
	}

	if ($which_hp < 1 && $this_hp > 0)
	{
		// They died
		$context['battle_message'] .= '
		<br />
		<br />
		'.$txt['battle_game_102'].' <strong>'.$context['battle_target']['name'].'</strong>.
		<br />
		<br />
		<form action="' . $scripturl . '?action=battle;sa=main;home;#battle_main" method="post">
			<input type="submit" value ="'.$txt['battle_return_home'].'" />
		</form>
		'.$txt['battle_or'].'<br />
		<form action="' . $scripturl . '?action=battle;sa=explore;home;#battle_main" method="post">
			<input type="submit" value ="'.$txt['battle_return_explore'].'" />
		</form>';
		$bhist = ''.$user_info['name'].' '.$txt['battle_hist25'].' '.$context['battle_target']['name'];
	}

	if($modSettings['enable_battle_hist'] && isset($bhist))
		add_to_battle_hist($bhist);

}
?>