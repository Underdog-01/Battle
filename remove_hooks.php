<?php
/*
 * Battle was developed for SMF forums c/o SA, nend & Chen Zhen
 * Copyright 2009, 2010, 2011, 2012, 2013, 2014, 2018  SA | nend | Chen Zhen
 * Revamped and supported by Chen Zhen
 * This software package is distributed under the terms of its Creative Commons - Attribution No Derivatives License (by-nd) 3.0
 * License: https://creativecommons.org/licenses/by-nd/3.0/
 * Support thread: https://web-develop.ca/index.php?board=15.0 
 */

if (!defined('SMF'))
	require '../SSI.php';

remove_integration_function('integrate_pre_include', '$sourcedir/Battle/BattleHooks.php');
remove_integration_function('integrate_pre_include', '$sourcedir/battle/battlehooks.php');
remove_integration_function('integrate_actions', 'battle_actions');
remove_integration_function('integrate_load_permissions', 'battle_load_permissions');
remove_integration_function('integrate_menu_buttons', 'battle_menu_buttons');
remove_integration_function('integrate_admin_areas', 'battle_admin_areas');
remove_integration_function('integrate_pre_load', 'battle_language');
remove_integration_function('integrate_pre_load', 'battle_files');
remove_integration_function('integrate_register', 'battle_user_info');
remove_integration_function('integrate_user_info', 'battle_user_settings');
remove_integration_function('integrate_member_context', 'battle_member_context');
?>