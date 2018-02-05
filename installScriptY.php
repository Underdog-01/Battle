<?php
/*
 * Battle was developed for SMF forums c/o SA, nend & Chen Zhen
 * Copyright 2009, 2010, 2011, 2012, 2013, 2014, 2018  SA | nend | Chen Zhen
 * Revamped and supported by Chen Zhen
 * This software package is distributed under the terms of its Creative Commons - Attribution No Derivatives License (by-nd) 3.0
 * License: https://creativecommons.org/licenses/by-nd/3.0/
 * Support thread: https://web-develop.ca/index.php?board=15.0 
 */

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc, $scripturl, $modSettings;

$currency = !empty($modSettings['bcash']) ? $modSettings['bcash'] : 'gold';

// First load the SMF 2's Extra DB Functions
db_extend('packages');
db_extend('extra');

@ini_set('memory_limit', '128M');
$membersData = array();

$request = $smcFunc['db_query']('','
                    SELECT id_member, hp, gold, max_hp, atk, max_atk, def, max_def, energy, max_energy, stamina, max_stamina,
                    exp, max_exp, level, bpm, is_dead, stat_point, mem_slays, mon_slays, battle_only_buddies_shout, battle_points
                    FROM {db_prefix}members
                    WHERE id_member >= {int:member}
                    ORDER BY id_member ASC',
                    array('member' => 1)
            );

while ($row = $smcFunc['db_fetch_assoc']($request))
{
    $max_exp = !empty($row['max_exp']) ? $row['max_exp'] : 0;

    if (empty($row['gold']) && empty($row['max_hp']) && empty($row['max_atk']) && empty($row['max_stamina']) && empty($row['max_def']))
    {
        $membersData[$row['id_member']] = array(
            $currency => !empty($row['gold']) ? $row['gold'] : 1100,
            'hp' => !empty($row['hp']) ? $row['hp'] : 100,
            'max_hp' => !empty($row['max_hp']) ? $row['max_hp'] : 100,
            'atk' => !empty($row['atk']) ? $row['atk'] : 100,
            'max_atk' => !empty($row['max_atk']) ? $row['max_atk'] : 100,
            'def' => !empty($row['def']) ? $row['def'] : 100,
            'max_def' => !empty($row['max_def']) ? $row['max_def'] : 100,
            'energy' => !empty($row['energy']) ? $row['energy'] : 100,
            'max_energy' => !empty($row['max_energy']) ? $row['max_energy'] : 100,
            'stamina' => !empty($row['stamina']) ? $row['stamina'] : 100,
            'max_stamina' => !empty($row['max_stamina']) ? $row['max_stamina'] : 100,
            'bpm' => !empty($row['bpm']) ? $row['bpm'] : 0,
            'is_dead' => !empty($row['is_dead']) ? $row['is_dead'] : 0,
            'stat_point' => !empty($row['stat_point']) ? $row['hp'] : 0,
            'level' => !empty($row['level']) ? $row['level'] : 0,
            'mem_slays' => !empty($row['mem_slays']) ? $row['mem_slays'] : 0,
            'mon_slays' => !empty($row['mon_slays']) ? $row['mon_slays'] : 0,
            'battle_only_buddies_shout' => !empty($row['battle_only_buddies_shout']) ? $row['battle_only_buddies_shout'] : 0,
            'exp' => !empty($row['exp']) ? $row['exp'] : $max_exp,
            'max_exp' => !empty($row['max_exp']) ? $row['max_exp'] : $max_exp,
            'battle_points' => !empty($row['battle_points']) ? $row['battle_points'] : 0
        );
    }
    elseif (empty($row['max_hp']) && empty($row['hp']))
        $membersData[$row['id_member']] = array('hp' => 100, 'max_hp' => 100, 'battle_points' => 0);
}
$smcFunc['db_free_result']($request);

foreach ($membersData as $member => $data)
    updateMemberData($member, $data);

// battle table updates ... all will be done here to make sure they are correct due to previous version inconsistancies
$updateTables = array(
                'battle_quest' => array(
                    'id_quest' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => true
                    ),
                    'gold' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'exp' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'level' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'success' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'hp' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'energy' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'is_final' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'min_gold' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'min_exp' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'max_penalty' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'max_gain' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'limit' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'campaign_id' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'plays' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false
                    ),
                    'name' => array(
                        'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false,
                    ),
                    'itext' => array (
                        'type' => 'text',
                        'null' => false
                    ),
                    'stext' => array(
                        'type' => 'text',
                        'null' => false
                    ),
                    'ftext' => array(
                        'type' => 'text',
                        'null' => false
                    ),
                ),
                'battle_quest_champs' => array(
                    'id_warrior' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'id_quest' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'exp_points' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'warrior_gold' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'fail' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'complete' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'date' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                ),
                'battle_shouts' => array(
                    'id_shout' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => true,
                    ),
                    'id_member' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'time' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'content' => array(
                        'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false
                    ),
                ),
                'battle_shop' => array(
                    'id_item' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => true,
                    ),
                    'price' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'amount' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'action' => array(
                        'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false,
                    ),
                    'description' => array(
                        'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false,
                    ),
                    'img' => array(
                        'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false,
                    ),
                    'name' => array(
                        'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false,
                    ),
                ),
                'battle_monsters' => array(
                    'id_monster' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => true,
                    ),
                    'atk' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'def' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'hp' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'max_hp' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'mon_range' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'mon_max_range' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'evolve' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'counter' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'img' => array(
                        'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false,
                    ),
                    'name' => array(
                        'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false,
                    ),
                ),
                'battle_monsters_fight' => array(
                    'id_warrior' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'id_monster' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'monster_hp' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'monster_max_hp' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'monster_def' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'monster_atk' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'time' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                ),
                'battle_champs' => array(
                    'id_champ' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'id_slain' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'times_champ' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'date' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                ),
                'battle_explore' => array(
                    'id_explore' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => true,
                    ),
                    'outcome1_reward' => array(
                        'type' => 'bigint',
                        'size' => 10,
                        'unsigned' => null,
                        'null' => false,
                        'auto' => false,
                    ),
                    'outcome2_reward' => array(
                        'type' => 'bigint',
                        'size' => 10,
                        'unsigned' => null,
                        'null' => false,
                        'auto' => false,
                    ),
                    'price' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'outcome1' => array(
                        'type' => 'text',
                        'null' => false,
                    ),
                    'outcome2' => array(
                        'type' => 'text',
                        'null' => false,
                    ),
                    'outcome1_action' => array(
                        'type' => 'text',
                        'null' => false,
                    ),
                    'outcome2_action' => array(
                        'type' => 'text',
                        'null' => false,
                    ),
                    'start' => array(
                        'type' => 'text',
                        'null' => false,
                    ),
                ),
                'battle_graveyard' => array(
                    'id_grave' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => true,
                    ),
                    'id_mem' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'id_memdef' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'date' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'name' => array(
                        'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false,
                    ),
                ),
                'battle_history' => array(
                    'id_hist' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => true,
                    ),
                    'time' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'content' => array(
                        'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false,
                    ),
                ),
                'members' => array(
                    'bpm' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    $currency => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 100,
                    ),
                    'is_dead' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    'stat_point' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    'lastupdate' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    'atk' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 100,
                    ),
                    'max_atk' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 100,
                    ),
                    'def' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 100,
                    ),
                    'max_def' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 100,
                    ),
                    'energy' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 100,
                    ),
                    'max_energy' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 100,
                    ),
                    'stamina' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 100,
                    ),
                    'max_stamina' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 100,
                    ),
                    'hp' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 100,
                    ),
                    'max_hp' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 100,
                    ),
                    'exp' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    'max_exp' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    'level' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    'mem_slays' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    'mon_slays' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    'battle_only_buddies_shout' => array(
                        'type' => 'int',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    'battle_last' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                    'battle_points' => array(
                        'type' => 'bigint',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ),
                ),
                'battle_scores' => array(
                    'id_warrior' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                        'auto' => false,
                    ),
                    'battle_title' => array(
                        'type' => 'varchar',
                        'size' => 255,
                        'unsigned' => true,
                        'null' => false,
                    ),
                    'score' => array(
                        'type' => 'bigint',
                        'size' => 11,
                        'unsigned' => true,
                        'null' => false,
                    ),
                    'date' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
                    ),
                    'level' => array(
                        'type' => 'int',
                        'size' => 10,
                        'unsigned' => true,
                        'null' => false,
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

// Correct the campaign permissions table due to improper allowance in older versions
$membergroups = array();
$request = $smcFunc['db_query']('', '
		SELECT min_posts, id_group
		FROM {db_prefix}membergroups
		ORDER BY id_group ASC'
	);

while ($row = $smcFunc['db_fetch_assoc']($request))
{
    $row['min_posts'] = !empty($row['min_posts']) ? $row['min_posts'] : 0;

    if ($row['min_posts'] == -1)
        $membergroups[] = $row['id_group'];
}
$smcFunc['db_free_result']($request);

foreach ($membergroups as $membergroup)
{
    for ($i=1;$i<25;$i++)
    {
        $smcFunc['db_query']('', '
            DELETE FROM {db_prefix}permissions
            WHERE permission = {string:permission}
            AND id_group = {int:group}',
            array('group' => $membergroup, 'permission' => 'battle_campaign_' . $i)
	);
    }
}

// Redirect the installer when all is complete
$url = $scripturl . '?action=admin;area=battle;';
if((!empty($_SERVER['HTTP_USER_AGENT'])) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
{
    @header("Refresh:4; url:" . $url);
    @header("Location:".$url);
}
else
    @header("Refresh:3; url=$url");
?>