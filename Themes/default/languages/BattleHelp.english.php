<?php
/*
 * Battle was developed for SMF forums c/o SA, nend & Chen Zhen
 * Copyright 2009, 2010, 2011, 2012, 2013, 2014, 2018  SA | nend | Chen Zhen
 * Revamped and supported by Chen Zhen
 * This software package is distributed under the terms of its Creative Commons - Attribution No Derivatives License (by-nd) 3.0
 * License: https://creativecommons.org/licenses/by-nd/3.0/
 * Support thread: https://web-develop.ca/index.php?board=15.0 
 */

// Help language
global $helptxt, $modSettings;

// Admin Quest
$helptxt['battleHelpQuestName'] = 'The name of this specific quest.';
$helptxt['battleHelpQuestMinSucg'] = 'For s Premium or Final Quest this will be used as the minimum possible monetary units for win or loss.<br />(Min Value to Max Value - random)<br /><br/>For a Standard Quest this will be the maximum possible monetary units for win or loss.<br />(0 to this value - random)';
$helptxt['battleHelpQuestMaxSucg'] = 'For s Premium or Final Quest this will be used as the maximum possible monetary units for win or loss.<br />(Min Value to Max Value - random)<br /><br/>For a Standard Quest (not final) this amount is not applicable.';
$helptxt['battleHelpQuestMinEnerg'] = 'For s Premium or Final Quest this will be used as the minimum possible Energy units for win or loss.<br />(Min Value to Max Value - random)<br /><br/>For a Standard Quest this will be the maximum possible Energy units for win or loss.<br />(0 to this value - random)';
$helptxt['battleHelpQuestMaxEnerg'] = 'For s Premium or Final Quest this will be used as the maximum possible Energy units for win or loss.<br />(Min Value to Max Value - random)<br /><br/>For a Standard Quest (not final) this amount is not applicable.';
$helptxt['battleHelpQuestMaxStatLoss'] = 'This will be the maximum stat loss for losing any quest.<br />(0 to Max Stat Loss - random)';
$helptxt['battleHelpQuestMaxStatWin'] = 'This will be the maximum stat gain for winning any quest.<br />(0 to Max Stat Gain - random)';
$helptxt['battleHelpQuestMaxHP'] = 'This will be the maximum HP that will be lost for any quest.<br />(0 to Max HP - random)';
$helptxt['battleHelpQuestReqEnerg'] = 'This amount of Energy is required to perform this quest.';
$helptxt['battleHelpQuestReqLvl'] = 'This User Level is required to perform this quest.';
$helptxt['battleHelpQuestFormula'] = 'This value represents the probability of winning a quest.';
$helptxt['battleHelpQuestLimit'] = 'This is the amount of times a user is allowed to perform the quest.<br /><br />The last time is considered a final quest and the user will not be able to perform this quest once this number is reached.';
$helptxt['battleHelpQuestInitText'] = 'This is the initial text shown to the user when performing the quest.';
$helptxt['battleHelpQuestSuccText'] = 'This is the text that is displayed after winning a quest.';
$helptxt['battleHelpQuestFailText'] = 'This is the text that is displayed after failing a quest.';
$helptxt['battleHelpQuestPrem'] = 'This sets this quest as premium where all max values will be applicable.';
$helptxt['battleHelpQuestCampaign'] = 'This quest can be set to be become part of a campaign whereas each quest can only be added to one campaign.<br /><br />There are three campaigns to choose from and only one quest from each level restriction can be added to a campaign.<br /><br />Warriors will be required to perform quests incrementing from the lowest level to the highest.<br />If a warrior/user does not have an adequate level to perform a quest inside a campaign, they may be required to explore or battle to attain a higher level before continuing their campaign.';

// Admin Shop
$helptxt['battleHelpShopName'] = 'The name of the item.';
$helptxt['battleHelpShopPrice'] = 'The price of the item.';
$helptxt['battleHelpShopAction'] = 'What the item does.<br />You can use: "hp" "atk" "def" "stamina" "energy"<br />Max Upgrades Are Done Via the stats center.';
$helptxt['battleHelpShopAmount'] = 'The amount given to the user for the above opted action.';
$helptxt['battleHelpShopDescript'] = 'The description of the item.';
$helptxt['battleHelpShopImage'] = 'The image of the item.';

// Admin Enemies
$helptxt['battleHelpMonsterName'] = 'The name of the ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . '.';
$helptxt['battleHelpMonsterAtk'] = 'The strength of attack the ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . ' can inflict.';
$helptxt['battleHelpMonsterDef'] = 'The defence strength of the ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . '.';
$helptxt['battleHelpMonsterHP'] = 'The current health points of the ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . '.';
$helptxt['battleHelpMonsterMaxHP'] = 'The maximum health points the ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . ' can obtain.';
$helptxt['battleHelpMonsterRange'] = 'This ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . ' will attack users who are in this range level.<br />(MIN / MAX)';
$helptxt['battleHelpMonsterEvolve'] = 'The amount of times the enemy needs to be killed by users before a random stat is upgraded by 1 for the ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster') . '.<br />(0 to disable, 1 to 1000 for the setting)';
$helptxt['battleHelpMonsterImage'] = 'The image of the ' . (!empty($modSettings['battle_enemy_designation']) ? $modSettings['battle_enemy_designation'] : 'Monster');

// Admin Custom Actions
$helptxt['battleHelpCustOne'] = 'The text displayed for the first possible random outcome of this custom scenario.';
$helptxt['battleHelpCustIntro'] = 'The description of this custom scenario.';
$helptxt['battleHelpCustGold'] = 'The amount of monetary units required for this custom scenario.';
$helptxt['battleHelpCustOneRewAmt'] = 'The reward amount applied to the stat opted below.';
$helptxt['battleHelpCustOneRewStat'] = 'The warrior statistic that is upgraded.<br />You can opt: "hp" "atk" "def" "stamina" "energy" "gold"';
$helptxt['battleHelpCustTwo'] = 'The text displayed for the second possible random outcome of this custom scenario.';
$helptxt['battleHelpCustTwoRewAmt'] = 'The reward amount applied to the stat opted below.';
$helptxt['battleHelpCustTwoRewStat'] = 'The warrior statistic that is upgraded.<br />You can opt: "hp" "atk" "def" "stamina" "energy" "gold"';

// Admin Config Settings
$helptxt['enable_battle'] = 'Enable/Disable this modification.';
$helptxt['enable_img_menu'] = 'Enabling this option will display the graphic town map in the left corner of the battle template.<br /><br />Disabling this option will display a text command menu in the left corner of the battle template.';
$helptxt['enable_battle_shoutbox'] = 'This option will enable the Battle Shoutbox.';
$helptxt['enable_show_who_battle'] = 'This option will enable the Battle Info Center.';
$helptxt['enable_battle_hist'] = 'This option will enable Battle History.';
$helptxt['enable_battle_range'] = 'Enabling this option will give warriors/users the option to battle enemies outside of their range level whilst exploring.<br /><br />Disabling this option will stop warriors/users from fighting enemies outside their range level whilst exploring.';
$helptxt['enable_sts_post'] = 'This option will show battle stats and a battle link under the user\'s name in posts.';
$helptxt['enable_sts_pm'] = 'This option will show battle stats and a battle link under the user\'s name when they post PM\'s.';
$helptxt['enable_sts_profile'] = 'This option will show battle stats and a battle link in users profiles.';
$helptxt['battle_enemy_designation'] = 'This input is for the title of the enemy as singular.<br />(ie. Monster, Enemy, Alien, Tank, etc.)';
$helptxt['battle_enemy_name_plural'] = 'This input is for the title of the enemies as plural.<br />(ie. Monsters, Enemies, Aliens, Tanks, etc.)';
$helptxt['battle_cash'] = 'This input is for the name of the monetary units to be used with Battle that will be displayed to all members.<br />(ie. money, gold, pounds, pesos, etc.)';
$helptxt['battle_points'] = '0 will disable this feature. This setting is for the user level at which the game will end. When any single player/user reaches this level, the game will end and members will be redirected to the stats page when attempting to Battle. Top stats including points will be displayed along with the winner of the game.';
$helptxt['bcash'] = 'This input is for the cash system being used with Battle.<br />This should only be adjusted if you have another modification or plugin that uses a specific database value from the members table.<br />This will allow you to use a like cash system.';
$helptxt['battle_map_across'] = 'The tile width of the explore map.<br />(recommend not to exceed 12 tiles)';
$helptxt['battle_map_down'] = 'The tile height of the explore map.<br />(recommend not to exceed 12 tiles)';
$helptxt['exp_bef_level'] = 'Experience points needed to advance one level.';
$helptxt['exp_stat_level'] = 'Stat points awarded upon leveling up.';
$helptxt['exp_def_mem'] = 'Maximum experience points a user can earn when battling another member.';
$helptxt['exp_def_mon'] = 'Maximum experience points a user can earn when battling an enemy.';
$helptxt['battle_time'] = 'The time rate at which users monetary units are auto updated.<br />(in seconds ie. 1 hour = 3600)';
$helptxt['battle_add_amount'] = 'The amount of monetary units the user is allotted automatically.';
$helptxt['battle_how_much_reviv_user'] = 'How much is costs to revive a user from the graveyard.';
$helptxt['battle_how_much_hp'] = 'When someone is revived from the graveyard, this is the amount of HP they will be revived with.';
$helptxt['battle_gold_reg'] = 'This is the default monetary units a user will begin with.';
$helptxt['battle_hp_reg'] = 'This is the default hit points a user will begin with.';
$helptxt['battle_hp_max_reg'] = 'This is the default maximum hit points setting a user will begin with.';
$helptxt['battle_atk_reg'] = 'This is the default attack power a user will begin with.';
$helptxt['battle_atk_max_reg'] = 'This is the default maximum attack power setting a user will begin with.';
$helptxt['battle_def_reg'] = 'This is the default defense power a user will begin with.';
$helptxt['battle_def_max_reg'] = 'This is the default maximum defense power setting a user will begin with.';
$helptxt['battle_energy_reg'] = 'This is the default energy units a user will begin with.';
$helptxt['battle_energy_max_reg'] = 'This is the default maximum energy units setting a user will begin with.';
$helptxt['battle_stamina_reg'] = 'This is the default stamina power a user will begin with.';
$helptxt['battle_stamina_max_reg'] = 'This is the default maximum stamina units setting a user will begin with.';
$helptxt['battle_enable_membattle'] = 'This option will allow members to battle against each other.';
$helptxt['battle_enable_quests'] = 'This option will enable the quests feature.';
$helptxt['battle_level_mem'] = 'This number is the level range at which members can battle each other. For example, if this is set to 5, a member can only battle other members that have a level minus 5 or greater than their own. Entering -1 for this input will disable the feature.';
$helptxt['battle_mem_battle_limit'] = 'This is the number of times one member is allowed to battle another member in a 24 hour time period.';
$helptxt['battle_mem_kill_limit'] = 'This is the number of times one member is allowed to slay another member in a 7 day time period.';
$helptxt['battle_exp_restrict_membattle'] = 'Enabling this option will only allow a member to gain experience while battling other members if the opponent level is equal or above their own. If their opponent level is below their own, no experience will be gained.';
$helptxt['battle_auto_lvl'] = 'Enabling this will allow all members to level up automatically.';
$helptxt['battle_players_lvl'] = 'With this enabled, the first player to reach the Ending Level (below) will end the game and become the winner of the current game of Battle. When this is diabled, all users (with permission) will be able to complete their Battle until they reach the Ending Level.';
$helptxt['battle_combine_pts'] = 'With this enabled, battle, explore and quest points will be combined for the game. Disabling this feature will keep the campaign scores separate.';
$helptxt['battle_map_name'] = 'This is the name of the current Battle taking place and will be saved in the overall leaderboard.';
$helptxt['battle_reset_time'] = 'Enter the elapsed time (in hours) for when the game will be automatically reset after its completion. Entering a value of 0 will disable this feature.  When enabled and triggered this will automatically reset all user stats and points XX hours after game completion.';

// Admin campaigns
$helptxt['battleHelpCampaignName'] = 'This is the name of the campaign you are editing.';
$helptxt['battleHelpCampaignTimed'] = 'Enabling this option will set this campaign as timed.<br /><br />You must also set the start and end times shown below this option when this is enabled.';
$helptxt['battleHelpCampaignLevel'] = 'Enabling this option will force warriors to wait for all other warriors to complete a quest level.<br /><br />With this enabled, the game will not allow others to proceed to higher quest levels within this campaign until all have reached the same level.';
$helptxt['battleHelpCampaignStartDate'] = 'Enter the campaign start date (DD/MM/YY).<br /><br />You must enter what is in the allowable range which is todays date or a future date.';
$helptxt['battleHelpCampaignStartTime'] = 'Enter the campaign start time (HH:MM - 24 Hour). <br /><br />You must enter a time that is within the allowable range which is 5 minutes in the future or beyond.';
$helptxt['battleHelpCampaignEndDate'] = 'Enter the campaign end date (DD/MM/YY).<br /><br />You must enter what is in the allowable range which is todays date or a future date.';
$helptxt['battleHelpCampaignEndTime'] = 'Enter the campaign end time (HH:MM - 24 Hour). <br /><br />You must enter a time that is within the allowable range which is 1 hour in the future or beyond.';
$helptxt['battleHelpCampaignMembergroups'] = 'These membergroups are allowed to participate in this quest.<br /><br />These are added via Admin -> Permissions -> Battle.';
$helptxt['battleHelpCampaignCurrentTime'] = 'The current time is shown here for reference but is only updated with a page refresh.';
$helptxt['battleHelpCampaignCurrentStatus'] = 'This displays the current status of this campaign.';
$helptxt['battleHelpCampaignImage'] = 'Select the image for the campaign.';
?>