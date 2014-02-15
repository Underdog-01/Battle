<?php
/*
 * Battle was developed for SMF forums c/o SA, nend & Underdog
 * Copyright 2009, 2010, 2011, 2012, 2013, 2014  SA | nend | Underdog
 * Revamped and supported by -Underdog-
 * This software package is distributed under the terms of its Creative Commons - Attribution No Derivatives License (by-nd) 3.0
 * http://creativecommons.org/licenses/by-nd/3.0/
 */
global  $modSettings, $scripturl;

// Intro
$txt['battle_intro_text'] = '<strong>Intro</strong><br />Battle is a simple forum based game that lets users fight ' . (!empty($modSettings['battle_enemy_name_plural']) ? $modSettings['battle_enemy_name_plural'] : 'Monsters') . ' or each other in a 1-click battle. Monsters are created by the administrator and battle results are determined by the user\'s skill points pitted against the ' . (!empty($modSettings['battle_enemy_name_plural']) ? $modSettings['battle_enemy_name_plural'] : 'Monster\'s') . ' skill points.';
$txt['battle_intro_text1'] = '<strong>How to play</strong><br />For starters, you probably want to <a href="'.$scripturl.'?action=battle;sa=explore;#battle_main">explore</a> to get a jump on everything.<br  />Exploring is simple, just click on a tile for something random, you might encounter a monster, or get another explore action.<br /><br />After exploring,gaining levels and accumulating stat points to upgrade, you can then do much more such as Quests/Campaigns and battling other Members!<br />Points are awarded for defeating members and ' . (!empty($modSettings['battle_enemy_name_plural']) ? $modSettings['battle_enemy_name_plural'] : 'Monster\'s') . ', whereas more will be awarded for stronger opponents.<br /><br >Game modes are as follows:<br /><ul><li><i>Infinity</i>: An endless battle where the game has no defined ending.</li><li style="list-style-type: none;">&nbsp;</li><li><i>Victory</i>: The first user to reach the target level ends the game for all users.</li><li style="list-style-type: none;">&nbsp;</li><li><i>Rivalry</i>: Every user that has permission to play Battle must reach the target level to end the game. Individuals that reach the target level will have to wait for all others to finish.</li></ul><br /><br />When the target level is completed for either Victory or Rivalry mode, the game may be set by the administrator to auto reset which will start the game anew including all users stats.  If this option is enabled the reset time will be displayed in the title of the Battle template.  If the reset option is disabled, an administrator will have to reset the game manually.  When a game has been reset, hi scores for that game will be transferred to the overall leaderboard.<br /><br />The administrator has the option of setting all campaign points as a bonus to the battle score.  Battle points (including any bonus points) will be displayed in both the Game Stats and Leaderboard.';
$txt['battle_howto'] = 'How To Play';
$txt['battle_welcome_warrior'] = 'Welcome warrior';
$txt['battle_welcome_warrier_intro'] = 'So, you have come to rid the world of treacherous ' . (!empty($modSettings['battle_enemy_name_plural']) ? $modSettings['battle_enemy_name_plural'] : 'Monsters') . '? Do not take on more than you can handle!';
$txt['battle1'] = 'Welcome To Battle';
$txt['battle_dis'] = 'Battle Disabled';

// Error/Warning messages
$txt['battle_error1'] = 'You have no stamina.';
$txt['battle_error2'] = 'They have no stamina.';
$txt['battle_error3'] = 'They have no energy to fight back with!';
$txt['battle_error4'] = 'You Have no energy to fight back with!';
$txt['battle_error5'] = 'It is impossible for you to attack yourself!';
$txt['battle_error6'] = 'You have no attack value! Did you really think you could win by attacking for 0 points?';
$txt['battle_error7'] = 'Either they are dead or have no stats! You cannot attack them!';
$txt['battle_error8'] = 'You cannot attack someone if you have no HP!';
$txt['battle_error9'] = 'This option has been disabled by the administration.';
$txt['battle_error10'] = 'You are not permitted to battle a member that has this low of a level.';
$txt['battle_quest_error1'] = 'You do not have permission to perform this quest.';
$txt['battle_quest_error2'] = 'The start time for this campaign has not yet transpired.';
$txt['battle_quest_error3'] = 'The end time for this campaign has expired.';
$txt['battle_quest_error4'] = 'This campaign is configured for all warriors to wait until each have completed quest levels. At this time there are warriors performing this quest at the previous level.';
$txt['battle_quest_error5'] = 'This campaign is configured for all warriors to wait until each have completed quest levels. This campaign must have begun prior to you graduating to this membergroup, therefore you can not compete in this campaign.';
$txt['battle_quest_error6'] = 'This campaign is configured for all warriors to wait until each have completed quest levels. You have already completed this quest level and must wait for other warriros to complete it.';
$txt['battle_quest_error7'] = 'This campaign is configured for all warriors to wait until each have completed quest levels. All warriors have already completed this quest level for the campaign.';
$txt['amont_m_b_num'] = 'Amount must be numeric.';
$txt['amont_m_b_num1'] = 'Amount must be greater than zero.';
$txt['cheater_sa_battle'] = 'Cheating is not allowed.';
$txt['battle_game_error_rrro'] = 'You do not got enough stanima, energy or health to make this attack';
$txt['battle_game_error_energy_explore'] = '<div style="border: 2px dashed rgb(204, 51, 68); margin: 2ex; padding: 2ex; color: black; background-color: rgb(255, 228, 233);"><div style="float: left; width: 2ex; font-size: 2em; color: red;">!!</div><b style="text-decoration: underline;">Error</b><br/><div style="padding-left: 6ex;">You need at least 2 energy to explore, please go to the <a href="'.$scripturl.'?action=battle;sa=shop;home">Shop</a>.</div></div>';
$txt['battle_deadplease'] = '<div style="border: 2px dashed rgb(204, 51, 68); margin: 2ex; padding: 2ex; color: black; background-color: rgb(255, 228, 233);"><div style="float: left; width: 2ex; font-size: 2em; color: red;">!!</div><b style="text-decoration: underline;">Attention</b><br/><div style="padding-left: 6ex;">You are dead and cannot battle, please go to the <a href="'.$scripturl.'?action=battle;sa=shop;home;#battle_main">Shop</a>.</div></div>';
$txt['battle_cheatrefresh'] = 'No refreshing is allowed '.$user_info['name'].', please go back to <a href="'.$scripturl.'?action=battle;sa=main;home;#battle_main">Battle Main page</a>';
$txt['battle_upcheat'] = 'No cheating '.$user_info['name'].'';
$txt['battle_hp_low'] = '<strong>Warning: Your HP is getting low</strong>';
$txt['battle_mem_error'] = '<div style="border: 2px dashed rgb(204, 51, 68); margin: 2ex; padding: 2ex; color: black; background-color: rgb(255, 228, 233);"><div style="float: left; width: 2ex; font-size: 2em; color: red;">!!</div><b style="text-decoration: underline;">Error</b><br/><div style="padding-left: 6ex;">Unable to find member!</div></div>';
$txt['battle_campaign_exist_error'] = 'That campaign does not exist!';
$txt['battle_error11'] = 'You are only permitted to battle each member ' . $modSettings['battle_mem_battle_limit'] . ' times in 24 hours.';
$txt['battle_error12'] = 'You are only permitted to slay each member ' . $modSettings['battle_mem_kill_limit'] . ' times within 7 days.';
$txt['battle_error13'] = 'You do not have enough stat points to perform this task.';
$txt['battle_error14'] = 'You do not have enough ' . $modSettings['battle_cash'] . ' to buy this item.';
$txt['battle_error15'] = 'Your level is not high enough to perform this quest.';

// Tabs and buttons
$txt['battle_menu1'] = 'Town Map';
$txt['battle_menu2'] = 'Navigation';
$txt['battle_help'] = 'Help';
$txt['battle_return_explore'] = 'Return to Explore';
$txt['battle_return_home'] = 'Return Home';
$txt['battle_game_set'] = 'Settings';
$txt['battle_game_stats'] = 'Game Stats';
$txt['battle_game_leaderboard'] = 'Leaderboard';
$txt['battle_explre'] = 'Explore';
$txt['battle_run'] = 'Run Away';
$txt['battle_or'] = 'or';
$txt['battle_run2'] = 'Run';
$txt['battle_ex_atk_again'] = 'Attack Again!';
$txt['battle_home'] = 'Home';
$txt['battle_stast'] = 'Stats';
$txt['battle_town_map'] = 'Navigation';
$txt['battle_atk_again'] = 'Attack Again!';
$txt['battle_visit_shop'] = 'Visit Shop';
$txt['battle_return_shop'] = 'Visit the <a href="'.$scripturl.'?action=battle;sa=shop;home;#battle_main">Shop</a>';
$txt['battle_return_quest'] = 'Try another <a href="'.$scripturl.'?action=battle;sa=quest;&%#@!$home;#battle_main">Quest</a>';
$txt['battle_expl'] = 'Explore';
$txt['battle_tent'] = 'Battle';
$txt['battle_shops'] = 'Shop';
$txt['battle_gy'] = 'Graveyard';
$txt['battle_questss'] = 'Quest';
$txt['battle_back'] = 'Back';
$txt['battle_title_game'] = !empty($modSettings['battle_map_name']) ? $modSettings['battle_map_name'] : 'Battle';
$txt['battle'] = 'Battle';
$txt['battle_atk'] = 'Attack';

// Shoutbox
$txt['battle_shoput'] = 'Shoutbox';
$txt['battle_shoutbox_input_empty'] = 'Shoutbox input can\'t be empty.';
$txt['battle_shout_button'] = 'Shout';
$txt['battle_shout'] = 'Shout History';
$txt['battle_s'] = 'Shout';

// Warrior options
$txt['enable_battle_shoutbox_buddies_only'] = 'Enable if you want to only see buddy shouts';
$txt['shoutbox_buddies_only'] = 'Enable to only see buddies in the shoutbox';
$txt['battle_game_seta'] = 'Enable to send a PM when you are attacked ';

// Did you know
$txt['battle_did_you'] = 'Did you know';
$txt['battle_did_you1'] = 'There is/are currently';
$txt['battle_did_you2'] = 'There is/are currently';
$txt['battle_did_you3'] = (!empty($modSettings['battle_enemy_name_plural']) ? $modSettings['battle_enemy_name_plural'] : 'Monster(s)') . ' roaming';
$txt['battle_did_you4'] = 'There is/are currently';
$txt['battle_did_you5'] = 'items in our shop';
$txt['battle_did_you6'] = 'Our members have slayed a total of';
$txt['battle_did_you7'] = (!empty($modSettings['battle_enemy_name_plural']) ? $modSettings['battle_enemy_name_plural'] : 'Monsters');
$txt['battle_did_you8'] = 'Members';
$txt['battle_did_you9'] = 'Battle Info Center';
$txt['battle_did_you10'] = 'There are currently';
$txt['battle_did_you11'] = 'member(s) in the graveyard';
$txt['battle_did_you_quest'] = 'quest(s)';

// Explore/Battle
$txt['battle_mem'] = 'Member';
$txt['battle_game_mcog'] = 'caught you offguard and dealt you';
$txt['battle_game_mcog1'] = 'As a result, you died.';
$txt['battle_game_lost_you1'] = 'You lost dealing';
$txt['battle_game_lost_you2'] = 'damage to';
$txt['battle_game_lost_you3'] = 'and taking';
$txt['battle_game_won_you'] = 'You won dealing';
$txt['battle_game_tied_you'] = 'You tied dealing';
$txt['battle_game_99'] = 'In your attempt to take down';
$txt['battle_game_101'] = 'you were killed.';
$txt['battle_game_102'] = 'You have successfully killed';
$txt['battle_game_error_toolate'] = 'Too late, they are already dead.';
$txt['battle_game_this_exp'] = ' You have gained';
$txt['battle_game_this_lost'] = ' You have sacrificed';
$txt['battle_game_this_exp1'] = 'experience.';
$txt['battle_game_this_energy1'] = 'energy.';
$txt['battle_game_this_stam1'] = 'stamina.';
$txt['battle_game_this_atk1'] = 'attack points.';
$txt['battle_game_this_stat1'] = 'stat points.';
$txt['battle_game_this_def1'] = 'defense points.';
$txt['battle_game_pm_msg'] = 'attacked you for';
$txt['battle_game_pm_msg1'] = 'Damage';
$txt['battle_game_pm_msg2'] = 'Attack Back!';
$txt['battle_this_enemy'] = 'This enemy still has';
$txt['battle_points_left'] = 'health points left.';
$txt['battle_this_member'] = 'This member still has';
$txt['battle_member_stat_points'] = ' has gained &@!@% stat points.';
$txt['battle_game_pm_msg3'] = 'Battle Bot';
$txt['battle_game_pm_msg4'] = 'Battle';
$txt['battle_you_in_mon'] = 'You encountered a ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster');
$txt['battle_run_act4'] = 'The Critter laughs at your fast departing ass.<br />You lose 1 ' . (!empty($modSettings['battle_cash']) ? $modSettings['battle_cash'] : 'Gold') . ' and 2 Energy for running.';
$txt['battle_run_act3'] = 'You fail to get away<br />You lose 1 ' . (!empty($modSettings['battle_cash']) ? $modSettings['battle_cash'] : 'Gold') . ' and 2 Energy for your failed attempt';
$txt['battle_run_act2'] = 'In your panic, you run a full circle and are again facing the ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . ' dooh!<br />You lose 1 ' . (!empty($modSettings['battle_cash']) ? $modSettings['battle_cash'] : 'Gold') . ' and 2 Energy for your failed attempt.';
$txt['battle_run_act1'] = 'The ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . ' blocks your attempt to escape.<br />You lose 1 ' . (!empty($modSettings['battle_cash']) ? $modSettings['battle_cash'] : 'Gold') . ' and 2 Energy for your failed attempt.';
$txt['battle_and'] = 'and';
$txt['battle_in_action'] = 'You were killed in action by';
$txt['battle_taking'] = 'taking';
$txt['battle_with'] = 'With Only';
$txt['battle_explorek4'] = 'You were lucky To Escape From ';
$txt['battle_explorek1'] = 'You killed';
$txt['battle_explorek2'] = '! You gained ';
$txt['battle_explorek3'] = 'Exp +1 ';
$txt['battle_you_kill'] = 'You killed';
$txt['battle_you_gain'] = '! You gained ';
$txt['battle_you_lucky'] = 'You were lucky To Escape From';
$txt['battle_with_only'] = 'With Only';
$txt['battle_damage'] = 'Damage';
$txt['battle_and'] = 'And';
$txt['battle_taking'] = 'Taking';
$txt['battle_killed_in_action'] = 'You Were Killed In Action By ';
$txt['battle_not_range'] = 'Monster is not in range';
$txt['battle_range'] = 'Range';
$txt['battle_action'] = 'Action';
$txt['battle_game_this_points1'] = 'You earned a total of';
$txt['battle_game_this_points2'] = 'Battle Points for defeating this opponent';
$txt['battle_game_this_points3'] = 'Your opponent earned a total of';
$txt['battle_game_this_points4'] = 'Battle Points for your defeat';

// Stats
$txt['battle_stats_attack'] = 'Top Attackers By Attack';
$txt['battle_stats_def'] = 'Top Attackers By Defense';
$txt['battle_stats_ms'] = 'Top Member Slayers';
$txt['battle_stats_mos'] = 'Top ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . ' Slayers';
$txt['battle_stats_gold'] = 'Top Battlers By ' . (!empty($modSettings['battle_cash']) ? $modSettings['battle_cash'] : 'Gold');
$txt['battle_stats_level'] = 'Top Battlers By Level';
$txt['battle_ms'] = 'Member Slays';
$txt['battle_mos'] = (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . ' Slays';
$txt['battle_stats_points'] = 'Top Battlers By Points';
$txt['battle_stats_winner'] = 'Winner Of Current Game';
$txt['battle_statsATK'] = 'Attack Member';
$txt['battle_statsD'] = 'Defense';
$txt['battle_statsS'] = 'Stamina';
$txt['battle_statsA'] = 'Attack';
$txt['battle_statsL'] = 'Level';
$txt['battle_statsLvl'] = 'Lvl';
$txt['battle_statsP'] = 'Points';
$txt['battle_statsE'] = 'Exp';
$txt['battle_statsEg'] = 'Energy';
$txt['battle_statsH'] = 'Health';
$txt['battle_statsSP'] = 'Stat Points';
$txt['battle_statsMem'] = 'Members Slain';
$txt['battle_statsMon'] = 'Monsters Slain';
$txt['battle_statsHP'] = 'HP';
$txt['battle_statsID'] = 'Member ID';
$txt['battle_statsName'] = 'Member Name';
$txt['battle_gold'] = !empty($modSettings['battle_cash']) ? $modSettings['battle_cash'] : 'gold';
$txt['battle_tally_points'] = 'Score';
$txt['battle_monStatsA'] = 'Atk';
$txt['battle_monStatsD'] = 'Def';

// History
$txt['battle_hist'] = 'History';
$txt['battle_hist_ex1'] = 'explored and got';
$txt['battle_hist1'] = 'The ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . ' blocks';
$txt['battle_hist2'] = 'attempted to escape';
$txt['battle_hist3'] = 'tried to run and failed';
$txt['battle_hist4'] = 'failed at running away from a ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster');
$txt['battle_hist5'] = 'Succeeded in Getting Away From A ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster');
$txt['battle_hist6'] = 'The ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . ' Laughs At';
$txt['battle_hist7'] = 'Fast Departing Ass';
$txt['battle_hist8'] = 'Attacked The ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster');
$txt['battle_hist9'] = 'for';
$txt['battle_hist10'] = 'Damage';
$txt['battle_hist11'] = 'Got slayed by the ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster');
$txt['battle_hist12'] = 'slayed';
$txt['battle_hist13'] = 'upgraded their max attack';
$txt['battle_hist14'] = 'upgraded their max defense';
$txt['battle_hist15'] = 'upgraded their max energy';
$txt['battle_hist16'] = 'upgraded their max stamina';
$txt['battle_hist17'] = 'upgraded their max health';
$txt['battle_hist18'] = 'purchased';
$txt['battle_hist19'] = 'completed the quest: ';
$txt['battle_hist20'] = 'failed the quest: ';
$txt['battle_hist21'] = 'healed their self';
$txt['battle_hist22'] = 'healed';
$txt['battle_hist23'] = 'attacked';
$txt['battle_hist24'] = 'and got slayed';
$txt['battle_hist25'] = 'slayed';
$txt['battle_hist26'] = 'reached a new level:';
$txt['battle_hist27'] = 'Campaign: &%$@!# has been advanced to level:';
$txt['battle_hist28'] = 'Campaign started:';
$txt['battle_hist29'] = 'Campaign completed:';
$txt['battle_hist30'] = ', as a result &#*@& gained @$%#% experience points.';

// Custom explore actions
$txt['battle_custom'] = 'custom';
$txt['battle_ok'] = 'Ok';
$txt['battle_no'] = 'No Thanks';

// Shop/upgrade
$txt['battle_hosp_heal'] = 'healed you for';
$txt['battle_gy'] = 'Graveyard';
$txt['bshop_amount'] = 'Amount';
$txt['battle_upd'] = 'Increase your Attack to do more damage and win more fights';
$txt['battle_upd1'] = 'Increase your Energy to do more Quests and fight';
$txt['battle_upd2'] = 'Increase your Stamina to do more Quests and fight';
$txt['battle_upd3'] = 'Increase your Defense to repel more attacks';
$txt['battle_upd4'] = 'Increase your Max Health to survive intense fights';
$txt['battle_upgrade_c'] = 'Upgrade your character';
$txt['battle_upgrade_ce'] = 'Upgrade';
$txt['battle_stuh'] = 'You have ';
$txt['battle_syusp'] = 'Stat Points ';
$txt['battle_hosp'] = 'Upgrade';
$txt['battle_levelnew2'] = 'Level Up!';
$txt['battle_shop_suc'] = '<div class="windowbg" id="profile_success">Item was purchased successfully.</div>';
$txt['battle_shop'] = 'Battle';
$txt['battle_shop2'] = 'Shop';
$txt['battle_upgrade_suc'] = '<div class="windowbg" id="profile_success">Stats were upgraded successfully.</div>';
$txt['battle_shop_Item'] = 'Item';
$txt['battle_shop_Price'] = 'Price';
$txt['battle_shop_description'] = 'Description';
$txt['battle_shop_all_have'] = 'You Have <br />Max ';
$txt['battle_shop_not_gold'] = 'Not Enough ' . (!empty($modSettings['battle_cash']) ? $modSettings['battle_cash'] : 'Gold');
$txt['battle_shop_buynow'] = 'Buy Now';
$txt['battle_yes_stats_p'] = 'Upgrade this stat!';
$txt['battle_no_stats_p'] = 'No Stat Points';
$txt['battle_heal_no_gold2'] = 'You do not have enough ' . (!empty($modSettings['battle_cash']) ? $modSettings['battle_cash'] : 'Gold') . ' to buy';
$txt['battle_upgrade_stn'] = '<center><strong>You Have no Stat Points</strong></center>';
$txt['battle_hosp_mh'] = 'You already have max health';
$txt['battle_heal_no_gold'] = 'You do not have enough ' . (!empty($modSettings['battle_cash']) ? $modSettings['battle_cash'] : 'Gold') . ' to heal';

//Graveyard
$txt['battle_rfp'] = 'Revive';
$txt['battle_heal_suc'] = '<div class="windowbg" id="profile_success">User was successfully healed.</div';
$txt['battle_revive_suc'] = '<div class="windowbg" id="profile_success">User was revived successfully.</div>';
$txt['battle_game_cbh'] = 'You can\'t revive a user that isn\'t your buddy.';
$txt['battle_game_cnhu'] = 'You do not have enough cash to revive this user.';

// Leaderboard
$txt['battle_date'] = 'Date';
$txt['battle_sb'] = 'Slain By';
$txt['battle_leaders'] = 'Battle Leaders';
$txt['battle_champ_against'] = 'Wins Against';
$txt['battle_champ_t'] = 'Total Slain';
$txt['battle_chaml_l'] = 'Latest Battle';
$txt['battle_mon_slayz'] = (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . ' Slays';
$txt['battle_leaderboard'] = 'Battle Leaders';
$txt['battle_leaderboard2'] = 'Battle Leaderboard';
$txt['battle_title'] = 'Game Title';
$txt['battle_who_slain'] = 'Recent Slaying';
$txt['battle_mem'] = 'Battle Member';
$txt['battle_recent'] = 'Recent Battles';
$txt['battle_campaign_leaderboard'] = 'Battle Campaign Leaders';
$txt['battle_campaign_lb_name'] = 'Campaign';
$txt['battle_campaign_lb_warrior'] = 'Member';
$txt['battle_campaign_lb_quests'] = 'Completed Quests';
$txt['battle_campaign_lb_score'] = 'Score';
$txt['battle_lscore'] = 'Points';
$txt['battle_campaign_lb_date'] = 'Completion Date';
$txt['battle_campaign_lb_overall'] = 'Overall';
$txt['battle_cleaders'] = 'Campaign Leaders';
$txt['battle_leaderboard_ctitle'] = 'Leaderboard';

// Quests/Campaigns
$txt['battle_Quest'] = 'Quest';
$txt['battle_quest_name'] = 'Name';
$txt['battle_quest_lvl'] = 'Level Required: ';
$txt['battle_quest_energy'] = 'Energy Required: ';
$txt['battle_quest_play'] = 'Plays';
$txt['battle_quest_final'] = 'Premium Quest';
$txt['battle_quest_type'] = 'Type';
$txt['battle_quest_final_1'] = 'Premium';
$txt['battle_quest_final_0'] = 'Standard';
$txt['battle_quest_action'] = 'Action';
$txt['battle_qno'] = 'No Quest';
$txt['battle_quest_do_again'] = 'Do quest again';
$txt['battle_quest_do'] = 'Do quest';
$txt['battle_quest_time_passed'] = 'Quota';
$txt['battle_quest_e2'] = 'You don\'t have enough Energy to perform this quest.<br />You need at least ';
$txt['battle_quest_e4'] = 'You have already performed this quest for the maximum amount permitted.<br />';
$txt['battle_quest_e5'] = 'Quest does not exist.';
$txt['battle_quest_e3'] = 'Energy';
$txt['battle_quest_comp'] = 'Completed';
$txt['battle_quest_restrict'] = 'Restricted';
$txt['battle_quest_rq'] = 'Required ';
$txt['battle_quest_lvl'] = 'Level';
$txt['battle_quest_lost'] = 'You have failed your quest and as a result have lost ' . $modSettings['battle_cash'] . ' and other stats listed below.';
$txt['battle_lost_stat'] = '!@#$& deducted:';
$txt['battle_won_stat'] = '!@#$& gained:';
$txt['battle_quest_final_complete'] = 'Quest completed.';
$txt['battle_quest_final_incomplete'] = 'Incomplete Quest';
$txt['battle_quest_final_new'] = 'New Quest';
$txt['battle_quest_final_limit'] = 'Quest Limit:';
$txt['battle_quest_cont'] = 'Continue';
$txt['battle_quest_final'] = 'Final Quest';
$txt['battle_quest_final2'] = 'Final Quest has been completed!';
$txt['battle_quest_final3'] = 'Quest has been completed!';
$txt['battle_usr_comp_final'] = 'Final quest completed. All stats have been updated!';
$txt['battle_quest_notFinal'] = 'You have gained experience from this quest.';
$txt['battle_quest_denial'] = 'Unavailable';
$txt['battle_quest_lvl_denial'] = 'Lvl Unavailable';
$txt['battle_quest_acc_denial'] = 'Access Denied';
$txt['battle_quest_granted'] = 'Available';
$txt['battle_quest_finished'] = 'Finished';
$txt['battle_quest_pending'] = 'Pending';
$txt['battle_quest_expired'] = 'Expired';
$txt['battle_quest_imminent'] = 'Imminent';
$txt['battle_quest_lvl_comp'] = 'Lvl Completed';
$txt['battle_quest_date_start'] = 'Start Date:';
$txt['battle_quest_time_start'] = 'Start Time:';
$txt['battle_campaign_completed'] = 'Campaign Completed';
$txt['battle_campaign_incomplete'] = 'Incomplete Campaign';
$txt['battle_camp_init'] = 'Start This Campaign';

// Who's in battle
$txt['Battle_who_home'] = 'Viewing Battle home';
$txt['Battle_who_explore'] = 'Exploring';
$txt['Battle_who_shop'] = 'Shopping';
$txt['Battle_who_battle'] = 'Viewing Battle list';
$txt['Battle_who_quest'] = 'Questing';
$txt['Battle_who_leaders'] = 'Viewing the current leaderboard';
$txt['Battle_who_cleaders'] = 'Viewing the campaign leaderboard';
$txt['Battle_who_overall'] = 'Viewing the overall leaderboard';
$txt['Battle_who_upgrade'] = 'Upgrading';
$txt['Battle_who_graveyard'] = 'In the graveyard';
$txt['Battle_who_howto'] = 'Educating theirself';
$txt['Battle_who_monster'] = 'Viewing ' . (!empty($modSettings['battle_enemy_name_plural']) ? $modSettings['battle_enemy_name_plural'] : 'Monsters');
$txt['Battle_who_stats'] = 'Viewing Battle stats';
$txt['Battle_who_settings'] = 'Modifying their settings';
$txt['Battle_who_search'] = 'Searching for ' . (!empty($modSettings['battle_enemy_name_plural']) ? $modSettings['battle_enemy_name_plural'] : 'Monsters');
$txt['Battle_who_fm'] = 'Fighting ' . (!empty($modSettings['battle_enemy_name_plural']) ? $modSettings['battle_enemy_name_plural'] : 'Monsters');
$txt['Battle_who_fight'] = 'Fighting Members';
$txt['battle_online_search1'] = 'There are currently no members in Battle.';
$txt['whoall_battle'] = 'Viewing <a href="'.$scripturl.'?action=battle;#battle_main">Battle</a>.';
$txt['battle_online_search'] = 'Searching...';
$txt['battle_users_online'] = '<strong>Users in Battle</strong>';

// General
$txt['battle_page'] = 'Page';
$txt['battle_list_order'] = 'Ascend/Descend';
$txt['battle_camp_points'] = 'Campaign Points';
$txt['battle_in_progress'] = 'Game Still In Progress';
$txt['battle_mode_disabled'] = 'Game Mode Disabled';
$txt['battle_map_default_name'] = 'Battle of Kyofu';
$txt['battle_auto_reset'] = 'Game completed. All user stats have been reset to default.';
$txt['battle_mode'] = 'Battle Mode:';
$txt['battle_infinity'] = 'Infinity';
$txt['battle_rivalry'] = 'Rivalry';
$txt['battle_victory'] = 'Victory';
$txt['battle_goal'] = '<span style="position:relative;margin-right:15px;">Target Level: ' . (!empty($modSettings['battle_points']) ? abs((int)$modSettings['battle_points']) : 0) . '</span>';
$txt['battle_reset_timer'] = 'Reset Time: &!%@#';
$txt['battle_complete'] = 'Completed';
?>