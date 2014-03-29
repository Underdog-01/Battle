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

function battle_array_insert(&$input, $key, $insert, $where = 'before', $strict = false)
{
	$position = array_search($key, array_keys($input), $strict);

	// Key not found -> insert as last
	if ($position === false)
	{
		$input = array_merge($input, $insert);
		return;
	}

	if ($where === 'after')
		$position += 1;

	// Insert as first
	if ($position === 0)
		$input = array_merge($insert, $input);
	else
		$input = array_merge(
			array_slice($input, 0, $position, true),
			$insert,
			array_slice($input, $position, null, true)
		);
}

function battle_actions(&$actionArray)
{
	global $modSettings;
	// Continue to work if Battle is enabled.
	if (empty($modSettings['enable_battle']))
		return;

	$actionArray['battle'] = array('Battle.php', 'battle');
}

// Permission hooks
function battle_load_permissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
{
	global $context, $smcFunc, $sourcedir, $txt;
	require_once($sourcedir . '/BattleAdmin.php');
	$battle_disableCampaigns = false;

	// If it's a post-based membergroup, the permissions for campaigns must be disabled
	$groupId = !empty($context['group']['id']) ? (int)$context['group']['id'] : 0;

	$request = $smcFunc['db_query']('', '
			SELECT min_posts, id_group
			FROM {db_prefix}membergroups
			WHERE id_group = {int:idgroup}
			ORDER BY id_group ASC
			LIMIT 1',
			array('idgroup' => $groupId)
		);

	$row = $smcFunc['db_fetch_assoc']($request);
	$smcFunc['db_free_result']($request);

	if ((empty($row['min_posts'])) || $row['min_posts'] != -1)
		$battle_disableCampaigns = true;

	$campaigns = battle_campaigns_list(0, 0);

	// Build the array for the Battle permissions.
	$permissionList['membergroup'] += array(
		'view_battle' => array(false, 'battle', 'battle'),
		'admin_battle' => array(false, 'battle', 'battle'),
		'battle_shouts' => array(false, 'battle', 'battle'),
		'battle_shouts_mod' => array(false, 'battle', 'battle'),
	);

	$context['non_guest_permissions'] = array_merge(
		$context['non_guest_permissions'],
		array(
			'view_battle',
			'admin_battle',
			'battle_shouts',
			'battle_shouts_mod',
		)
	);

	if (!$battle_disableCampaigns)
	{
		foreach ($campaigns as $campaign)
		{
			if (empty($campaign['id_campaign']))
				continue;

			$campaignId = (int)$campaign['id_campaign'];
			$campaignName = !empty($campaign['campaign_name']) ? $smcFunc['strtolower']($campaign['campaign_name']) : $txt['battle_campaign_id'] . '_' . $campaignId;
			$txt['permissionname_battle_campaign_' . $campaignId] = str_replace('#@#$!', $campaignName, $txt['permissionname_battle_perm']);

			// The loop did not work for the help text... check language file
			// $txt['permissionhelp_battle_campaign' . $campaignId] = str_replace('#@#$!', $campaignName, $txt['permissionhelp_battle_perm']);

			$permissionList['membergroup'] += array(
				'battle_campaign_' . $campaignId => array(false, 'battle', 'battle'),
			);

			$context['non_guest_permissions'] = array_merge(
				$context['non_guest_permissions'],
				array(
					'battle_campaign_' . $campaignId,
				)
			);
		}
	}

}

// Buttons/tabs
function battle_menu_buttons(&$menu_buttons)
{
	global $context, $modSettings, $scripturl, $txt;

	loadLanguage('BattleAdmin');
	// Insert after Profile tab.
	battle_array_insert($menu_buttons, 'search',
		array(
			'battle' => array(
				'title' => $txt['battle_tab'],
				'href' => $scripturl . '?action=battle;#battle_main',
				'show' =>  allowedTo('view_battle') && !empty($modSettings['enable_battle']),
				'sub_buttons' => array(
				),
				'active_button' => false,
			),
		)
	);

	//admin tab
	if(allowedTo('admin_battle'))
	{
		$counter = 0;
		foreach ($menu_buttons['admin']['sub_buttons'] as $area => $dummy)
			if (++$counter && $area == 'featuresettings')
				break;

		$menu_buttons['admin']['sub_buttons'] = array_merge(
			array_slice($menu_buttons['admin']['sub_buttons'], 0, $counter, TRUE),
			array(
				'battle' => array(
					'title' => $txt['battle_atab'],
					'href' => $scripturl . '?action=admin;area=battle',
					'show' => allowedTo('admin_battle'),
				),
			),
			array_slice($menu_buttons['admin']['sub_buttons'], $counter, NULL, TRUE)
		);
	}
}

// Battle admin area
function battle_admin_areas(&$admin_areas)
{
	global $context, $modSettings, $scripturl, $txt;

	// Let's add our own style to this menu...
	list($area, $sa, $battle) = array(!empty($_REQUEST['area']) ? $_REQUEST['area'] : false, !empty($_REQUEST['sa']) ? $_REQUEST['sa'] : false, $txt['battle_atab']);
	$subActions = array(
		$txt['battle_tabac'] => array('config'),
		$txt['battle_tabas'] => array('shop', 'edit_item'),
		$txt['battle_tabaq'] => array('quest', 'quest_edit'),
		$txt['battle_tabam'] => array('monsterlist', 'editm'),
		$txt['battle_tabamn'] => array('custom', 'custom_edit'),
		$txt['battle_tabamm'] => array('bmem'),
		$txt['battle_campaign_tab'] => array('campaigns', 'edit_campaign'),
		$txt['battle_tabaman'] => array('maintain')
	);

	foreach ($subActions as $key => $subAction)
	{
		if (in_array($sa, $subAction) && $area === 'battle')
			$button[$key] = '<span style="display:inline-block;font-weight:bold;font-style:oblique;font-family:arial black;">' . $key . '</span>';
		else
			$button[$key] = $key;
	}
	if ($area === 'battle')
		$battle = '<span style="display:inline-block;font-weight:bold;">' . $txt['battle_atab'] . '</span>';

	// Insert after layout in Admin center.
	battle_array_insert($admin_areas, 'layout',
		array(
			'battle' => array(
				'title' => $txt['battle_tab'],
				'permission' => array('admin_battle'),
				'areas' => array(
					'battle' => array(
						'label' => $battle,
						'file' => 'BattleAdmin.php',
						'function' => 'battleAdmin',
						'custom_url' => $scripturl . '?action=admin;area=battle',
						'icon' => 'battle_admin.png',
						'permission' => array('admin_battle'),
					),
					'battleconfig' => array(
						'label' => $button[$txt['battle_tabac']],
						'file' => 'BattleAdmin.php',
						'function' => 'battleAdmin',
						'custom_url' => $scripturl . '?action=admin;area=battle;sa=config',
						'icon' => 'battle_config.png',
						'permission' => array('admin_battle'),
					),
					'battleshop' => array(
						'label' => $button[$txt['battle_tabas']],
						'file' => 'BattleAdmin.php',
						'function' => 'battleAdmin',
						'custom_url' => $scripturl . '?action=admin;area=battle;sa=shop',
						'icon' => 'battle_shop.png',
						'permission' => array('admin_battle'),
					),
					'battlequest' => array(
						'label' => $button[$txt['battle_tabaq']],
						'file' => 'BattleAdmin.php',
						'function' => 'battleAdmin',
						'custom_url' => $scripturl . '?action=admin;area=battle;sa=quest',
						'icon' => 'battle_quest.png',
						'permission' => array('admin_battle'),
					),
					'battlemons' => array(
						'label' => $button[$txt['battle_tabam']],
						'file' => 'BattleAdmin.php',
						'function' => 'battleAdmin',
						'custom_url' => $scripturl . '?action=admin;area=battle;sa=monsterlist',
						'icon' => 'battle_monster.png',
						'permission' => array('admin_battle'),
					),
					'battlecexp' => array(
						'label' => $button[$txt['battle_tabamn']],
						'file' => 'BattleAdmin.php',
						'function' => 'battleAdmin',
						'custom_url' => $scripturl . '?action=admin;area=battle;sa=custom',
						'icon' => 'battle_explore.png',
						'permission' => array('admin_battle'),
					),
					'battlemems' => array(
						'label' => $button[$txt['battle_tabamm']],
						'file' => 'BattleAdmin.php',
						'function' => 'battleAdmin',
						'custom_url' => $scripturl . '?action=admin;area=battle;sa=bmem',
						'icon' => 'battle_members.png',
						'permission' => array('admin_battle'),
					),
					'campaigns' => array(
						'label' => $button[$txt['battle_campaign_tab']],
						'file' => 'BattleAdmin.php',
						'custom_url' => $scripturl . '?action=admin;area=battle;sa=campaigns',
						'icon' => 'battle_campaign.png',
						'permission' => array('admin_battle'),
					),
					'battlemaintain' => array(
						'label' => $button[$txt['battle_tabaman']],
						'file' => 'BattleAdmin.php',
						'function' => 'battleAdmin',
						'custom_url' => $scripturl . '?action=admin;area=battle;sa=maintain',
						'icon' => 'battle_maintain.png',
						'permission' => array('admin_battle'),
					),
				),
				'subsections' => array(
					'main' => array($txt['battle_tabammain']),
					'config' => array($txt['battle_tabac']),
					'shop' => array($txt['battle_tabas']),
					'quest' => array($txt['battle_tabaq']),
					'monsterlist' => array($txt['battle_tabam']),
					'custom' => array($txt['battle_tabamn']),
					'campaigns' => array($txt['battle_campaign_tab']),
					'bmem' => array($txt['battle_tabamm']),
					'maintain' => array($txt['battle_tabaman']),
				),
			),
		)
	);
}

function battle_user_info(&$regOptions, &$theme_vars)
{
	global $modSettings;

	$regOptions['register_vars'] += array(
		'gold' => $modSettings['battle_gold_reg'],
		'hp' => $modSettings['battle_hp_reg'],
		'max_hp' => $modSettings['battle_hp_max_reg'],
		'atk' => $modSettings['battle_atk_reg'],
		'max_atk' => $modSettings['battle_atk_max_reg'],
		'def' => $modSettings['battle_def_reg'],
		'max_def' => $modSettings['battle_def_max_reg'],
		'energy' => $modSettings['battle_energy_reg'],
		'max_energy' => $modSettings['battle_energy_max_reg'],
		'stamina' => $modSettings['battle_stamina_reg'],
		'max_stamina' => $modSettings['battle_stamina_max_reg'],
	);

	if (!in_array($modSettings['bcash'], $regOptions['register_vars']))
		$regOptions['register_vars'] += array(
			$modSettings['bcash'] => $modSettings['battle_gold_reg']
		);
}

function battle_member_context(&$user, $display_custom_fields)
{
	global $modSettings, $memberContext, $user_profile;

	$profile = $user_profile[$user];

	$memberContext[$user] += array(
		'battle_only_buddies_shout' => $profile['battle_only_buddies_shout'],
		'stat_point' => $profile['stat_point'],
		'gold' => $profile['gold'],
		'hp' => $profile['hp'],
		'max_hp' => $profile['max_hp'],
		'energy' => $profile['energy'],
		'max_energy' => $profile['max_energy'],
		'stamina' => $profile['stamina'],
		'max_stamina' => $profile['max_stamina'],
		'atk' => $profile['atk'],
		'max_atk' => $profile['max_atk'],
		'def' => $profile['def'],
		'max_def' => $profile['max_def'],
		'exp' => $profile['exp'],
		'max_exp' => $profile['max_exp'],
		'level' => $profile['level'],
		'mem_slays' => $profile['mem_slays'],
		'mon_slays' => $profile['mon_slays'],
		'lastupdate' => $profile['lastupdate'],
		'is_dead' => $profile['is_dead'],
		'bpm' => $profile['bpm'],
		'battle_points' => $profile['battle_points'],
	);

	if (!in_array($modSettings['bcash'], $memberContext[$user]))
	{
		$profile[$modSettings['bcash']] = !empty($profile[$modSettings['bcash']]) ? $profile[$modSettings['bcash']] : $modSettings['bcash'];
		$memberContext[$user] += array(
			$modSettings['bcash'] => $profile[$modSettings['bcash']],
		);
	}
}

function battle_user_settings()
{
	global $modSettings, $user_info, $user_settings;

	$user_info += array(
                'battle_only_buddies_shout' => isset($user_settings['battle_only_buddies_shout']) ? $user_settings['battle_only_buddies_shout'] : '',
                'stat_point' => isset($user_settings['stat_point']) ? $user_settings['stat_point'] : '',
                'gold' => isset($user_settings['gold']) ? $user_settings['gold'] : '',
                'hp' => isset($user_settings['hp']) ? $user_settings['hp'] : '',
                'max_hp' => isset($user_settings['max_hp']) ? $user_settings['max_hp'] : '',
                'energy' => isset($user_settings['energy']) ? $user_settings['energy'] : '',
                'max_energy' => isset($user_settings['max_energy']) ? $user_settings['max_energy'] : '',
                'stamina' => isset($user_settings['stamina']) ? $user_settings['stamina'] : '', 'max_stamina' => isset($user_settings['max_stamina']) ? $user_settings['max_stamina'] : '',
                'atk' => isset($user_settings['atk']) ? $user_settings['atk'] : '',
                'max_atk' => isset($user_settings['max_atk']) ? $user_settings['max_atk'] : '',
                'def' => isset($user_settings['def']) ? $user_settings['def'] : '',
                'max_def' => isset($user_settings['max_def']) ? $user_settings['max_def'] : '',
                'exp' => isset($user_settings['exp']) ? $user_settings['exp'] : '',
                'max_exp' => isset($user_settings['max_exp']) ? $user_settings['max_exp'] : '',
                'level' => isset($user_settings['level']) ? $user_settings['level'] : '',
                'mem_slays' => isset($user_settings['mem_slays']) ? $user_settings['mem_slays'] : '',
                'mon_slays' => isset($user_settings['mon_slays']) ? $user_settings['mon_slays'] : '',
                'lastupdate' => isset($user_settings['lastupdate']) ? $user_settings['lastupdate'] : '',
                'is_dead' => isset($user_settings['is_dead']) ? $user_settings['is_dead'] : '',
                'bpm' => isset($user_settings['bpm']) ? $user_settings['bpm'] : '',
                'battle_points' => isset($user_settings['battle_points']) ? $user_settings['battle_points'] : '',
	);

	if (!in_array($modSettings['bcash'], $user_info))
		$user_info += array(
			$modSettings['bcash'] => isset($user_settings[$modSettings['bcash']]) ? $user_settings[$modSettings['bcash']] : '',
		);
}

function battle_language()
{
	loadLanguage('BattleAdmin');
	loadLanguage('Battle');
	loadLanguage('BattleHelp');
}

function battle_files()
{
	// Load all necessary files
	global $sourcedir;

	require_once($sourcedir . '/Subs-Battle.php');
	require_once($sourcedir . '/Subs-BattleQuests.php');
	require_once($sourcedir . '/BattleMain.php');
	require_once($sourcedir . '/BattleShouts.php');
	require_once($sourcedir . '/BattleQuests.php');
	require_once($sourcedir . '/BattleOpps.php');
}
?>