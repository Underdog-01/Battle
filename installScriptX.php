<?php
/*
 * Battle was developed for SMF forums c/o SA, nend & Underdog
 * Copyright 2009, 2010, 2011, 2012, 2013, 2014  SA | nend | Underdog
 * Revamped and supported by -Underdog-
 * This software package is distributed under the terms of its Creative Commons - Attribution No Derivatives License (by-nd) 3.0
 * http://creativecommons.org/licenses/by-nd/3.0/
 */

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc, $scripturl, $modSettings;
@ini_set('memory_limit', '128M');

$currency = !empty($modSettings['bcash']) ? $modSettings['bcash'] : 'gold';
$smfVersion = !empty($modSettings['smfVersion']) ? explode('.', $modSettings['smfVersion']) : array(2,0,7);

// First load the SMF 2's Extra DB Functions
db_extend('packages');
db_extend('extra');

if ((!empty($modSettings['smfVersion'][1])) && intval($smfVersion[1]) == 0)
    add_integration_function('integrate_pre_include', '$sourcedir/SMF2-0_BattleHooks.php');
else
    add_integration_function('integrate_pre_include', '$sourcedir/SMF2-1_BattleHooks.php');

add_integration_function('integrate_actions', 'battle_actions');
add_integration_function('integrate_load_permissions', 'battle_load_permissions');
add_integration_function('integrate_menu_buttons', 'battle_menu_buttons');
add_integration_function('integrate_admin_areas', 'battle_admin_areas');
add_integration_function('integrate_pre_load', 'battle_language');
add_integration_function('integrate_pre_load', 'battle_files');
add_integration_function('integrate_register', 'battle_user_info');
add_integration_function('integrate_user_info', 'battle_user_settings');
add_integration_function('integrate_member_context', 'battle_member_context');

// Version to be updated for new releases
$smcFunc['db_insert']('replace', '{db_prefix}settings',
		array(
			'variable' => 'string',
			'value' => 'string',
			),
	array(
		array ('battle_version' ,'1.15'),
                array ('battle_revision' ,'Beta6'),
		array ('battle_dev' ,0),
		array ('battle_build' ,'3'),
		array ('battle_build_date' ,'February 16, 2014'),
		),
		array()
	);

// The leaderboard may change its format for new releases therefore it is prudent to start it anew
$smcFunc['db_drop_table'] ('{db_prefix}battle_champs');

$smcFunc['db_create_table']('{db_prefix}battle_champs',
   array(
      array(
         'name' => 'id_champ',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
         'auto' => false
      ),
      array(
         'name' => 'id_slain',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
      ),
      array(
         'name' => 'times_champ',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
      ),
      array(
         'name' => 'date',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
      ),
   ),
   array(
      array(
         'type' => 'primary',
         'columns' => array('id_champ')
      ),
   ),
      array(),
   'ignore');

$smcFunc['db_create_table']('{db_prefix}battle_scores',
   array(
        array(
         'name' => 'id_warrior',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
         'auto' => false
        ),
        array(
         'name' => 'battle_title',
         'type' => 'varchar',
         'size' => 255,
         'null' => false,
         'unsigned' => true,
        ),
        array(
         'name' => 'score',
         'type' => 'bigint',
         'size' => 11,
         'null' => false,
         'unsigned' => true,
        ),
        array(
         'name' => 'date',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
        ),
        array(
         'name' => 'level',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
        ),
   ),
   array(
      array(
         'type' => 'primary',
         'columns' => array('battle_title', 'id_warrior')
      ),
   ),
      array(),
   'ignore');

$smcFunc['db_create_table']('{db_prefix}battle_members',
   array(
      array(
         'name' => 'member_id',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
         'auto' => false
      ),
      array(
         'name' => 'opponent_id',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
      ),
      array(
         'name' => 'battles',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
      ),
      array(
         'name' => 'kills',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
      ),
      array(
         'name' => 'battles_date',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
      ),
      array(
         'name' => 'kills_date',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
      ),
   ),
   array(
      array(
         'type' => 'primary',
         'columns' => array('member_id', 'opponent_id')
      ),
   ),
      array(),
   'ignore');

// drop the old redundant table if it exists
$smcFunc['db_drop_table'] ('{db_prefix}battle_quest_hist');

$smcFunc['db_create_table']('{db_prefix}battle_quest_champs',
   array(
        array(
         'name' => 'id_warrior',
         'type' => 'int',
         'size' => 10,
         'null' => false,
         'unsigned' => true,
         'auto' => false
      ),
        array(
         'name' => 'id_quest',
         'type' => 'int',
         'size' => 10,
         'unsigned' => true,
         'null' => false
      ),
        array(
         'name' => 'exp_points',
         'type' => 'int',
         'size' => 10,
         'unsigned' => true,
         'null' => false
      ),
        array(
         'name' => 'warrior_gold',
         'type' => 'int',
         'size' => 10,
         'unsigned' => true,
         'null' => false
      ),
        array(
         'name' => 'fail',
         'type' => 'int',
         'size' => 10,
         'unsigned' => true,
         'null' => false
      ),
        array(
         'name' => 'complete',
         'type' => 'int',
         'size' => 10,
         'unsigned' => true,
         'null' => false
      ),
        array(
         'name' => 'date',
         'type' => 'int',
         'size' => 10,
         'unsigned' => true,
         'null' => false
      ),
   ),
        array(
            array(
                'type' => 'primary',
                'columns' => array('id_warrior', 'id_quest')
            ),
        ),
        array(),
    'ignore'
);

$smcFunc['db_remove_column'](
        '{db_prefix}battle_quest_champs',
        'gold'
);

$smcFunc['db_add_column'](
	'{db_prefix}battle_quest_champs',
	array(
            'name' => 'warrior_gold',
            'type' => 'int',
            'size' => 10,
            'unsigned' => true,
            'null' => false
	),
        array(),
        'ignore',
        'fatal'
);

$smcFunc['db_create_table']('{db_prefix}battle_explore',
	array(
		array(
			'name' => 'id_explore',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
			'auto' => true,
		),
		array(
			'name' => 'outcome1',
			'type' => 'text',
                        'null' => false,
		),
		array(
			'name' => 'outcome2',
			'type' => 'text',
                        'null' => false,
		),
		array(
			'name' => 'outcome2_action',
			'type' => 'text',
                        'null' => false,
		),
		array(
			'name' => 'outcome1_action',
			'type' => 'text',
                        'null' => false,
		),
		array(
			'name' => 'start',
			'type' => 'text',
                        'null' => false,
		),
		array(
			'name' => 'outcome1_reward',
			'type' => 'bigint',
			'size' => 10,
                        'unsigned' => false,
		),
		array(
			'name' => 'price',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false,
		),
		array(
			'name' => 'outcome2_reward',
			'type' => 'bigint',
			'size' => 10,
                        'unsigned' => false,
                        'null' => false
			),
	),
	array(
		array(
			'name' => 'id_explore',
			'type' => 'primary',
			'columns' => array('id_explore'),
		),
	),
		array(),
	'ignore');

$smcFunc['db_create_table']('{db_prefix}battle_history',
	array(
		array(
			'name' => 'id_hist',
			'type' => 'int',
			'null' => false,
                        'unsigned' => true,
			'auto' => true
		),
		array(
			'name' => 'content',
			'type' => 'varchar',
			'size' => 255,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'time',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
	),
	array(
		array(
			'type' => 'primary',
			'columns' => array('id_hist')
		),
	),
		array(),
	'ignore');


$smcFunc['db_create_table']('{db_prefix}battle_graveyard',
	array(
		array(
			'name' => 'id_grave',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
			'auto' => true,
		),
		array(
			'name' => 'id_mem',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false
		),
		array(
			'name' => 'id_memdef',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false,
		),
		array(
			'name' => 'name',
			'type' => 'varchar',
			'size' => 255,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'date',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false,
		),
	),
	array(
		array(
			'name' => 'id_grave',
			'type' => 'primary',
			'columns' => array('id_grave'),
		),
	),
		array(),
	'ignore');

$smcFunc['db_create_table']('{db_prefix}battle_monsters_fight',
	array(
		array(
			'name' => 'id_warrior',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false,
			'auto' => false,
		),
                array(
			'name' => 'id_monster',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false,
		),
		array(
			'name' => 'monster_hp',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false,
		),
                array(
			'name' => 'monster_max_hp',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false,
		),
		array(
			'name' => 'monster_def',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false,
		),
		array(
			'name' => 'monster_atk',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false,
		),
                array(
			'name' => 'time',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false,
		),
	),
	array(
		array(
			'type' => 'primary',
			'columns' => array('id_warrior', 'id_monster'),
		),
	),
		array(),
	'ignore');

$smcFunc['db_create_table']('{db_prefix}battle_quest',
	array(
		array(
			'name' => 'id_quest',
			'type' => 'int',
			'null' => false,
                        'unsigned' => true,
			'auto' => true
		),
		array(
			'name' => 'name',
			'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false
		),
		array(
			'name' => 'gold',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'itext',
			'type' => 'text',
                        'null' => false,
		),
		array(
			'name' => 'stext',
                        'type' => 'text',
                        'null' => false,
		),
		array(
			'name' => 'ftext',
                        'type' => 'text',
                        'null' => false,
		),
		array(
			'name' => 'exp',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'level',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'success',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'plays',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'hp',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'energy',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'is_final',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
                array(
			'name' => 'min_gold',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
                array(
			'name' => 'min_exp',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
                array(
			'name' => 'max_penalty',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
                array(
			'name' => 'max_gain',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
                array(
			'name' => 'limit',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
                array(
			'name' => 'campaign_id',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'default' => 0,
			'null' => false
		),
	),
	array(
		array(
			'type' => 'primary',
			'columns' => array('id_quest')
		),
	),
		array(),
	'ignore');

$smcFunc['db_create_table']('{db_prefix}battle_campaign_1',
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
                        'unsigned' => true,
                        'size' => 255,
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

$smcFunc['db_create_table']('{db_prefix}battle_campaign_2',
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

$smcFunc['db_create_table']('{db_prefix}battle_campaign_3',
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

$result = $smcFunc['db_query']('','
	SELECT id_campaign
	FROM {db_prefix}battle_campaign_1
	LIMIT 1'
);

$check_alpha['id_campaign'] = false;
while ($row = $smcFunc['db_fetch_assoc']($result))
    $check_alpha['id_campaign'] = $row['id_campaign'];

$smcFunc['db_free_result']($result);

if ((empty($check_alpha['id_campaign'])) || !$check_alpha['id_campaign'] !== 1)
{
    $smcFunc['db_insert']('replace',
        '{db_prefix}battle_campaign_1',
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
            'id_campaign' => 1,
            'campaign_name' => 'Alpha',
            'score' => 0,
            'start_time' => 0,
            'end_time' => 0,
            'timed_campaign' => 0,
            'level_completion' => 0,
            'quest_completions' => 0,
            'image' => 'Alpha.gif'
        ),
	array('id_warrior')
    );
}

$result = $smcFunc['db_query']('','
	SELECT id_campaign
	FROM {db_prefix}battle_campaign_2
	LIMIT 1'
);

$check_beta['id_campaign'] = false;
while ($row = $smcFunc['db_fetch_assoc']($result))
    $check_beta['id_campaign'] = $row['id_campaign'];

$smcFunc['db_free_result']($result);

if ((empty($check_beta['id_campaign'])) || !$check_beta['id_campaign'] !== 2)
{
    $smcFunc['db_insert']('replace',
        '{db_prefix}battle_campaign_2',
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
            'id_campaign' => 2,
            'campaign_name' => 'Beta',
            'score' => 0,
            'start_time' => 0,
            'end_time' => 0,
            'timed_campaign' => 0,
            'level_completion' => 0,
            'quest_completions' => 0,
            'image' => 'Beta.gif'
        ),
	array('id_warrior')
    );
}

$result = $smcFunc['db_query']('','
	SELECT id_campaign
	FROM {db_prefix}battle_campaign_3
	LIMIT 1'
);

$check_gamma['id_campaign'] = false;
while ($row = $smcFunc['db_fetch_assoc']($result))
    $check_gamma['id_campaign'] = $row['id_campaign'];

$smcFunc['db_free_result']($result);

if ((empty($check_gamma['id_campaign'])) || !$check_gamma['id_campaign'] !== 3)
{
    $smcFunc['db_insert']('replace',
        '{db_prefix}battle_campaign_3',
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
            'id_campaign' => 3,
            'campaign_name' => 'Gamma',
            'score' => 0,
            'start_time' => 0,
            'end_time' => 0,
            'timed_campaign' => 0,
            'level_completion' => 0,
            'quest_completions' => 0,
            'image' => 'Gamma.gif'
        ),
	array('id_warrior')
    );
}

$result = $smcFunc['db_query']('','
	SELECT id_quest
	FROM {db_prefix}battle_quest
	LIMIT 1',
	array(
	)
);
list ($has_quest) = $smcFunc['db_fetch_row']($result);
$smcFunc['db_free_result']($result);

if (empty($has_quest))
{
	 $smcFunc['db_insert']('ignore',
            '{db_prefix}battle_quest',

	// Fields
	array(
		'name' => 'string',
		'gold' => 'int',
		'itext' => 'string',
		'stext' => 'string',
		'ftext' => 'string',
		'exp' => 'int',
		'level' => 'int',
		'success' => 'int',
		'energy' => 'int',
		'hp' => 'int',
		'is_final' => 'int',
                'min_gold' => 'int',
                'min_exp' => 'int',
                'max_penalty' => 'int',
                'max_gain' => 'int',
                'limit' => 'int',
                'campaign_id' => 'int'
		),

	// Values
	array(
       array(
			'name' => 'Near The Council',
			'gold' => 50,
			'itext' => 'You head over to the council.<br /> You start scavenging for coins.<br />',
			'stext' => '<br />Result: You collect from the gutters!',
			'ftext' => '<br />Result: While scrounging around you lost something!',
			'exp' => 5,
			'level' => 5,
			'success' => 33,
			'energy' => 6,
			'hp' => 3,
			'is_final' => 1,
                        'min_gold' => 5,
                        'min_exp' => 1,
                        'max_penalty' => 5,
                        'max_gain' => 5,
                        'limit' => 3,
                        'campaign_id' => 0
			),

		array(
			'name' => 'Under a Hobo\'s Shack',
			'gold' => 100,
			'itext' => 'You head over to the shack of A well-known hobo, Kent.<br />You creep underneath and start scavenging for things.<br />',
			'stext' => '<br />Result: You dig up some valuables!',
			'ftext' => '<br />Result: Kent finds you and says "What are you doing here?". <br />While you walk off briskly he picks your pockets like a pro.',
			'exp' => 7,
			'level' => 10,
			'success' => 15,
			'energy' => 7,
			'hp' => 4,
			'is_final' => 1,
                        'min_gold' => 10,
                        'min_exp' => 2,
                        'max_penalty' => 10,
                        'max_gain' => 10,
                        'limit' => 3,
                        'campaign_id' => 0
			),
		),
	array());
	}

$result = $smcFunc['db_query']('','
	SELECT id_explore
	FROM {db_prefix}battle_explore
	LIMIT 1',
	array(
	)
);
list ($has_explore) = $smcFunc['db_fetch_row']($result);
$smcFunc['db_free_result']($result);

if (empty($has_explore))
{
	 $smcFunc['db_insert']('ignore',
            '{db_prefix}battle_explore',

	// Fields
	array(
		'outcome1' => 'string',
		'outcome2' => 'string',
		'outcome1_reward' => 'int',
		'outcome2_reward' => 'int',
		'outcome1_action' => 'string',
		'outcome2_action' => 'string',
		'start' => 'string',
		'price' => 'int',
		),

	// Values
	array(
       array(
		'outcome1' => 'First outcome here (gained 200 Gold)',
		'outcome2' => 'Second outcome here (gained 50 Gold)',
		'outcome1_reward' => 200,
		'outcome2_reward' => 50,
		'outcome1_action' => 'gold',
		'outcome2_action' => 'gold',
		'start' => 'A man comes up to you and whispers, I have magical boxes, I\'ll let you open one for 100 Gold. Deal or no deal?',
                'price' => 100,
			),

		array(
		'outcome1' => 'You Gained +10 Health ',
		'outcome2' => 'You Gained +15 energy ',
		'outcome1_reward' => 10,
		'outcome2_reward' => 15,
		'outcome1_action' => 'hp',
		'outcome2_action' => 'energy',
		'start' => 'You See An Old Man He Comes Up To You And Says Wanna Play For 10 Gold',
		'price' => 10,
			),
		),
	array());
	}

$smcFunc['db_create_table']('{db_prefix}battle_monsters',
	array(
		array(
			'name' => 'id_monster',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
			'auto' => true,
		),
		array(
			'name' => 'atk',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
                        'null' => false,
		),
		array(
			'name' => 'def',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
			'null' => false,
		),
		array(
			'name' => 'hp',
			'type' => 'int',
			'size' => 10,
		),
		array(
			'name' => 'max_hp',
			'type' => 'int',
			'size' => 10,
                        'unsigned' => true,
			'null' => false,
		),
		array(
			'name' => 'img',
			'type' => 'varchar',
			'size' => 255,
			'unsigned' => true,
			'null' => false,
		),
		array(
			'name' => 'name',
			'type' => 'varchar',
			'size' => 255,
			'unsigned' => true,
			'null' => false,
		),
                array(
			'name' => 'mon_range',
			'type' => 'int',
			'size' => 10,
			'unsigned' => true,
			'null' => false,
		),
                array(
			'name' => 'mon_max_range',
			'type' => 'int',
			'size' => 10,
			'unsigned' => true,
			'null' => false,
		),
                array(
			'name' => 'evolve',
			'type' => 'int',
			'size' => 10,
			'unsigned' => true,
			'null' => false,
		),
                array(
			'name' => 'counter',
			'type' => 'int',
			'size' => 10,
			'unsigned' => true,
			'null' => false,
                        'default' => 0
		)
	),
	array(
		array(
			'name' => 'id_monster',
			'type' => 'primary',
			'columns' => array('id_monster'),
		),
	),
		array(),
	'ignore');

// If updating this mod these additions need to be added
$smcFunc['db_add_column'](
	'{db_prefix}battle_monsters',
	array(
            'name' => 'mon_max_range',
            'type' => 'int',
            'size' => 10,
            'unsigned' => true,
            'null' => false,
	),
        array(),
        'ignore',
        'fatal'
);

$smcFunc['db_add_column'](
	'{db_prefix}battle_monsters',
	array(
            'name' => 'mon_range',
            'type' => 'int',
            'size' => 10,
            'unsigned' => true,
            'null' => false,
	),
        array(),
        'ignore',
        'fatal'
);

$smcFunc['db_add_column'](
	'{db_prefix}battle_monsters',
	array(
            'name' => 'evolve',
            'type' => 'int',
            'size' => 10,
            'unsigned' => true,
            'null' => false,
	),
        array(),
        'ignore',
        'fatal'
);

$smcFunc['db_add_column'](
	'{db_prefix}battle_monsters',
	array(
            'name' => 'counter',
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

$smcFunc['db_add_column'](
	'{db_prefix}battle_quest',
	array(
            'name' => 'min_gold',
            'type' => 'int',
            'size' => 10,
            'unsigned' => true,
            'null' => false,
	),
        array(),
        'ignore',
        'fatal'
);

$smcFunc['db_add_column'](
	'{db_prefix}battle_quest',
	array(
            'name' => 'min_exp',
            'type' => 'int',
            'size' => 10,
            'unsigned' => true,
            'null' => false,
	),
        array(),
        'ignore',
        'fatal'
);

$smcFunc['db_add_column'](
	'{db_prefix}battle_quest',
	array(
            'name' => 'max_penalty',
            'type' => 'int',
            'size' => 10,
            'unsigned' => true,
            'null' => false,
	),
        array(),
        'ignore',
        'fatal'
);

$smcFunc['db_add_column'](
	'{db_prefix}battle_quest',
	array(
            'name' => 'max_gain',
            'type' => 'int',
            'size' => 10,
            'unsigned' => true,
            'null' => false,
	),
        array(),
        'ignore',
        'fatal'
);

$smcFunc['db_add_column'](
	'{db_prefix}battle_quest',
	array(
            'name' => 'limit',
            'type' => 'int',
            'size' => 10,
            'unsigned' => true,
            'null' => false,
	),
        array(),
        'ignore',
        'fatal'
);

$smcFunc['db_add_column'](
	'{db_prefix}battle_quest',
	array(
            'name' => 'campaign_id',
            'type' => 'int',
            'default' => 0,
            'size' => 10,
            'unsigned' => true,
            'null' => false,
	),
        array(),
        'ignore',
        'fatal'
);

// Add the default enemies/monsters
$result = $smcFunc['db_query']('','
	SELECT id_monster
	FROM {db_prefix}battle_monsters
	LIMIT 1',
	array(
	)
);
list ($has_mon) = $smcFunc['db_fetch_row']($result);
$smcFunc['db_free_result']($result);

// Add the default enemies/monsters
if (empty($has_mon))
{
	 $smcFunc['db_insert']('ignore',
            '{db_prefix}battle_monsters',

	// Fields
	array(
                'atk' => 'string',
		'def' => 'string',
		'hp' => 'string',
		'max_hp' => 'string',
		'img' => 'string',
		'name' => 'string',
		'mon_range' => 'int',
		'mon_max_range' => 'int',
                'evolve' => 'int',
                'counter' => 'int'
		),

	// Values
	array(
            array(
                'atk' => '34',
                'def' => '56',
                'hp' => '100',
                'max_hp' => '100',
                'img' => '5.png',
                'name' => 'Snowqueen',
                'mon_range' => 1,
                'mon_max_range' => 2,
                'evolve' => 0,
                'counter' => 0
            ),

            array(
                'atk' => '46',
                'def' => '50',
                'hp' => '100',
                'max_hp' => '100',
                'img' => '8.png',
                'name' => 'wdm2005',
                'mon_range' => 1,
                'mon_max_range' => 2,
                'evolve' => 0,
                'counter' => 0
            ),
            array(
		'atk' => '67',
		'def' => '55',
		'hp' => '100',
		'max_hp' => '100',
		'img' => '4.png',
		'name' => 'simply sibyl',
		'mon_range' => 1,
		'mon_max_range' => 2,
                'evolve' => 1,
                'counter' => 0
            ),
            array(
                'atk' => '12',
		'def' => '51',
		'hp' => '545',
		'max_hp' => '512',
		'img' => '6.png',
		'name' => 'Skhilled',
		'mon_range' => 1,
		'mon_max_range' => 2,
		'evolve' => 2,
                'counter' => 0
		),
            array(
		'atk' => '70',
		'def' => '300',
		'hp' => '640',
		'max_hp' => '700',
		'img' => 'underdog.png',
		'name' => 'underdog',
		'mon_range' => 3,
		'mon_max_range' => 10,
		'evolve' => 3,
                'counter' => 0
		),
            ),
	array());
	}

// spaces in image file names from previous version is not legal!! let's fix it..
$result = $smcFunc['db_query']('','
	SELECT id_monster, name, atk, def, hp, max_hp, mon_range, mon_max_range, img, evolve, counter
	FROM {db_prefix}battle_monsters'
);
$check = array();
while ($row = $smcFunc['db_fetch_assoc']($result))
{
    $check[] = array(
                'id_monster' => $row['id_monster'],
                'name' => $row['name'],
                'atk' => $row['atk'],
                'def' => $row['def'],
                'hp' => $row['hp'],
                'img' => $row['img'],
                'max_hp' => $row['max_hp'],
                'mon_range' => !empty($row['mon_range']) ? (int)$row['mon_range'] : 0,
                'mon_max_range' => !empty($row['mon_max_range']) ? (int)$row['mon_max_range'] : 1000,
                'evolve' => !empty($row['evolve']) ? $row['evolve'] : 0,
                'counter' => !empty($row['counter']) ? (int)$row['counter'] : 0,
                'proper' => trim(str_replace('.png', '', $row['img']))
                );
}

$smcFunc['db_free_result']($result);

foreach ($check as $query)
{
    if ($query['img'] !== $query['proper'] . '.png' && strpos($query['img'], '.png') !== false)
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
				$query['id_monster'],
				$query['name'],
				$query['atk'],
				$query['def'],
				$query['hp'],
				$query['proper'] . '.png',
				$query['max_hp'],
				$query['mon_range'],
				$query['mon_max_range'],
                                $query['evolve'],
                                $query['counter']
			),
			'id_monster'
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
				$query['id_monster'],
				$query['name'],
				$query['atk'],
				$query['def'],
				$query['hp'],
				$query['img'],
				$query['max_hp'],
				$query['mon_range'],
				$query['mon_max_range'],
                                $query['evolve'],
                                $query['counter']
			),
			'id_monster'
		);
    }
}


$smcFunc['db_insert']('ignore', '{db_prefix}settings',
		array(
			'variable' => 'string',
			'value' => 'string',
			),
	array(
		array ('enable_battle' ,'1'),
		array ('enable_img_menu' ,'0'),
		array ('enable_battle_shoutbox' ,'1'),
		array ('enable_show_who_battle' ,'1'),
		array ('enable_battle_hist' ,'1'),
                array('battle_enable_membattle', '1'),
                array('battle_exp_restrict_membattle', '1'),
		array('battle_enable_quests', '1'),
		array ('enable_sts_post' ,'1'),
		array ('enable_sts_pm' ,'1'),
		array ('enable_sts_profile' ,'1'),
                array ('enable_battle_range' ,'1'),
		array ('bcash', 'gold'),
                array('battle_cash', 'gold'),
                array('battle_points', '1000'),
                array('battle_map_name', 'Battle of Kyofu'),
		array ('battle_map_tile1' ,'plains'),
		array ('battle_map_tile2' ,'hills'),
		array ('battle_map_tile3' ,'forest'),
		array ('battle_map_tile4' ,'swamp'),
		array ('battle_map_tile5' ,'mountains'),
		array ('battle_map_tile6' ,'water'),
                array ('battle_map_coords' ,'1,3,246,248'),
		array ('battle_map_across' ,'10'),
		array ('battle_map_down' ,'10'),
		array ('exp_bef_level' ,'25'),
		array ('exp_stat_level' ,'1'),
                array ('battle_level_mem' ,'-1'),
		array ('exp_def_mem' ,'5'),
		array ('exp_def_mon' ,'5'),
		array ('battle_time' ,'3600'),
		array ('battle_add_amount' ,'1000'),
		array ('battle_how_much_reviv_user' ,'65'),
		array ('battle_how_much_hp' ,'20'),
		array ('battle_gold_reg' ,'100'),
		array ('battle_hp_reg' ,'100'),
		array ('battle_hp_max_reg' ,'100'),
		array ('battle_atk_reg' ,'100'),
		array ('battle_atk_max_reg' ,'100'),
		array ('battle_def_reg' ,'100'),
		array ('battle_def_max_reg' ,'100'),
		array ('battle_stamina_reg' ,'100'),
		array ('battle_stamina_max_reg' ,'100'),
		array ('battle_energy_reg' ,'100'),
		array ('battle_energy_max_reg' ,'100'),
                array ('battle_enemy_designation' ,'Monster'),
		array ('battle_enemy_name_plural' ,'Monsters'),
                array('battle_mem_battle_limit', 0),
                array('battle_mem_kill_limit', 0),
                array('battle_auto_lvl', 0),
                array('battle_players_lvl', 0),
                array('battle_combine_pts', 1)
		),
		array()
	);

// These will fix up tables for people updating this mod
$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'bpm',
	'type' => 'int',
	'default' => '0',
        'unsigned' => true,
        'null' => false,
	),
array(),
false
);
$smcFunc['db_add_column']('{db_prefix}battle_quest',
array(
	'name' => 'energy',
	'type' => 'int',
	'default' => '0',
        'unsigned' => true,
        'null' => false,
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}battle_quest',
array(
        'name' => 'is_final',
        'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
        'default' => '1',
        ),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => $currency,
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '1100',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'is_dead',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '0',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'battle_last',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '0',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'stat_point',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '0',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'lastupdate',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '0',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'atk',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '100',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'max_atk',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '100',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'def',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '100',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'max_def',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '100',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'energy',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '100',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'max_energy',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '100',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'stamina',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '100',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'max_stamina',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '100',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'hp',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '100',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'max_hp',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '100',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'exp',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '0',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'max_exp',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '0',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'level',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '0',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'mem_slays',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '0',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'battle_only_buddies_shout',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '0',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'mon_slays',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	'default' => '0',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}members',
array(
	'name' => 'battle_points',
	'type' => 'bigint',
        'size' => 11,
        'unsigned' => true,
        'null' => false,
	'default' => '0',
	),
array(),
false
);

$smcFunc['db_add_column']('{db_prefix}battle_scores',
array(
	'name' => 'level',
	'type' => 'int',
        'size' => 10,
        'unsigned' => true,
        'null' => false,
	),
array(),
false
);

$smcFunc['db_create_table']('{db_prefix}battle_shouts',
	array(
		array(
			'name' => 'id_shout',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false,
			'auto' => true
		),
		array(
			'name' => 'id_member',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false,
		),
		array(
			'name' => 'content',
			'type' => 'varchar',
			'size' => 255,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'time',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false
		),
	),
	array(
		array(
			'type' => 'primary',
			'columns' => array('id_shout')
		),
	),
		array(),
	'ignore');

	$smcFunc['db_create_table']('{db_prefix}battle_shop',
	array(
		array(
			'name' => 'id_item',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false,
			'auto' => true
		),
		array(
			'name' => 'price',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false,
		),
		array(
			'name' => 'amount',
			'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
			'null' => false,
		),
		array(
			'name' => 'action',
			'type' => 'varchar',
			'size' => 255,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'description',
			'type' => 'varchar',
			'size' => 255,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'img',
			'type' => 'varchar',
			'size' => 255,
                        'unsigned' => true,
			'null' => false
		),
		array(
			'name' => 'name',
			'type' => 'varchar',
			'size' => 255,
                        'unsigned' => true,
			'null' => false
		),
	),
	array(
		array(
			'type' => 'primary',
			'columns' => array('id_item')
		),
	),
		array(),
	'ignore');

	$result = $smcFunc['db_query']('','
	SELECT id_item
	FROM {db_prefix}battle_shop
	LIMIT 1',
	array(
	)
);
list ($has_item) = $smcFunc['db_fetch_row']($result);
$smcFunc['db_free_result']($result);

if (empty($has_item))
{
	 $smcFunc['db_insert']('ignore',
            '{db_prefix}battle_shop',

	// Fields
	array(
	    'price' => 'string',
		'amount' => 'string',
		'action' => 'string',
		'description' => 'string',
		'img' => 'string',
		'name' => 'string',



		),

	// Values
	array(
       array(
	   	 'price' => '100',
		'amount' => '100',
		'action' => 'atk',
		'description' => 'Gives You 100 Attack',
		'img' => 'S_Arco06.png',
		'name' => 'Attack Boost',
			),
			 array(
	   	 'price' => '55',
		'amount' => '55',
		'action' => 'hp',
		'description' => 'Gives You 55 Health',
		'img' => 'S_Arco06.png',
		'name' => 'Health Boost',
			),
			 array(
	   	 'price' => '55',
		'amount' => '50',
		'action' => 'def',
		'description' => 'Gives You 50 defense',
		'img' => 'Ac_Medalha01.png',
		'name' => 'Defense Star',
			),
			 array(
	   	 'price' => '1000',
		'amount' => '100',
		'action' => 'energy',
		'description' => 'Gives You 100 Energy',
		'img' => 'I_C_Maca.png',
		'name' => 'Tomato',
			),
						 array(
	   	 'price' => '50',
		'amount' => '50',
		'action' => 'stamina',
		'description' => 'Gives You 50 Stamina',
		'img' => 'I_C_Limao.png',
		'name' => 'Lemon',
			),

		),
	array());
}
?>