<?php
/*
 * Battle was developed for SMF forums c/o SA, nend & Chen Zhen
 * Copyright 2009, 2010, 2011, 2012, 2013, 2014, 2018  SA | nend | Chen Zhen
 * Revamped and supported by Chen Zhen
 * This software package is distributed under the terms of its Creative Commons - Attribution No Derivatives License (by-nd) 3.0
 * License: https://creativecommons.org/licenses/by-nd/3.0/
 * Support thread: https://web-develop.ca/index.php?board=15.0
 */
function template_main()
{
    global $txt, $context, $modSettings;
    echo '
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 0.5em;">
	<tr>
            <td style="vertical-align:top;">
                <table width="100%" height="130px;" cellpadding="5" cellspacing="0" border="0" class="tborder">
                    <tr>
                        <td class="catbg" style="height:1em;">
                            ', $txt['a_info2'], '
                        </td>
                    </tr>
                    <tr>
			<td class="windowbg2" style="height: 9em; padding: 0;vertical-align:top;font-size: 0.85em;">
                            <div id="saAnnouncements" style="height: 18ex; overflow: auto; padding-right: 1ex;">
                                <div style="margin: 4px; font-size: 0.85em;">
                                    ', $context['battle_news_connect'], '
                                </div>
                            </div>
			</td>
                    </tr>
                </table>
            </td>
            <td style="width: 1ex;">
                &nbsp;
            </td>
            <td style="vertical-align:top;width: 40%;">
		<table width="100%" height="130px;" cellpadding="5" cellspacing="0" border="0" class="tborder" id="saVersionsTable">
                    <tr>
			<td class="titlebg" style="height:1em;">
                            ', $txt['a_info3'], '
                        </td>
                    </tr>
                    <tr>
			<td class="windowbg2" style="vertical-align:top;height: 9em; line-height: 1.5em;font-size: 0.85em;">
                            ', $txt['battle_mod_cur'], '&nbsp;&nbsp;:&nbsp;
                            <span id="saYourVersion" style="white-space: nowrap;font-style:italic;">
                                ', $txt['battle_version'], '&nbsp;'.$txt['battle_revision'], '
                            </span>
                            <br />', $txt['battle_mod_late'], ':&nbsp;
                            <span id="saCurrentVersion" style="white-space: nowrap;font-style:italic;">
                                ', $context['battle_version_connect'], '
                            </span>
							<br />', (!empty($txt['battle_revision']) && $txt['battle_revision'] !== '&nbsp;' ? $txt['battle_build_info'] . '<br />' : '') . '
                            ', ($txt['battle_version'] !== $context['battle_version_connect'] ? '
							<br />' . $txt['battle_advised'] . '<br />
                            <a href="https://web-develop.ca/index.php?action=downloads;area=stable_smf_battle">
                                ' . $txt['battle_mod_dl'] : '<br />' . $txt['battle_advised_not']) . '
                            </a>
			</td>
                    </tr>
		</table>
            </td>
	</tr>
    </table>
    <table width="100%" cellpadding="5" cellspacing="0" border="0" class="tborder" style="margin-top: 2ex;">
        <tr class="titlebg">
            <td>
            ', $txt['a_info4'], '
            </td>
	</tr>
        <tr>
            <td class="windowbg2" style="padding: 0 10px;">
                <div style="line-height: 1.5em;">
                    ', $txt['battle_mod_credits'], '
                    <hr />
                    <div style="margin: 0; padding: 1ex 0 1ex 0;border:0px">
                        ', $txt['battle_donate'], '<br />
                        <form id="ud_xclick" name="sa_xclick" action="http://web-develop.ca/index.php?page=underdog_donation" method="post">
                            <input type="hidden" name="cmd" value="_xclick" />
                            <input type="hidden" name="business" value="underdog.admin@gmail.com" />
                            <input type="hidden" name="item_name" value="', $txt['battle_donate'], '" />
                            <input type="hidden" name="currency_code" value="USD" />
                            <input type="hidden" name="amount" value="" />
                            <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="', $txt['battle_donate'], '" />
                        </form>
                    </div>
                </div>
            </td>
	</tr>
    </table>';
}

function template_maintain()
{
    global $txt, $scripturl, $context;

    echo '
    <table width="80%" cellpadding="5" cellspacing="0" border="0" class="tborder centertext">
	<tr class="titlebg centertext">
            <td>
                ', $txt['battle_tabaman'], '
            </td>
        </tr>
	<tr class="windowbg2">
	    <td style="line-height:3px;">
		&nbsp;
	    </td>
	</tr>';

    foreach ($context['battle_mcommands'] as $key => $command)
    {
        echo '
	<tr class="windowbg2" style="vertical-align:top;">
            <td style="padding-bottom: 2ex;" width="20%">
                ', $txt['battle_maintain_' . $key], '<br />
		<a id="link', $key, '" href="', $scripturl, '?action=admin;area=battle;sa=', $key, '" onclick="var checkit = confirm(\'', $txt['battle_confirmation'] ,'\'); if (checkit) {document.getElementById(\'linkdpoints\').href = \'', $scripturl, '?action=admin;area=battle;sa=', $key, ';battle_reset_points=\' + document.getElementById(\'battle_reset_points\').value;} else return checkit;">
                    ', $txt['battle_maintain_' . $command], '
                </a>';
	if ($key === 'dpoints')
	    echo '
		    <span style="font-weight:bold;font-size:8px;padding: 0 1em;float:right;vertical-align:bottom;position:absolute;">
		        <input onclick="if (document.getElementById(\'battle_reset_points\').value == 0) {document.getElementById(\'battle_reset_points\').value = 1;} else {document.getElementById(\'battle_reset_points\').value = 0;}" style="vertical-align:top;" id="battle_reset_points" name="battle_reset_points" type="checkbox" value="1" checked="checked" />', $txt['battle_maintain_dcpoints'], '
		    </span>';

	if ($context['battle_maintain_' . $key] === 'done')
		echo '
		<br />
                <span style="font-weight:bold;" id="success', $key, '">
                    ', $txt['battle_adm_suc'], '
                </span>';
	else
		echo '
                <span style="font-weight:bold;">
                    &nbsp;
                </span>';

	echo '
		<hr />';
    }

    echo '
            </td>
	</tr>
	<tr class="catbg">
	    <td style="font-weight:bold;line-height:3px;">
		&nbsp;
	    </td>
	</tr>
    </table>';
}

function template_custEdit_Add()
{
    global $context, $scripturl, $txt, $settings;
echo '
    <form action="', $scripturl, '?action=admin;area=battle;save;sa=custom_save" method="post" name="theAdminForm" accept-charset="', $context['character_set'], '">
        <table border="0" width="80%" cellspacing="1" cellpadding="3" class="centertext tborder">
            <tr class="titlebg">
                <td>
                    ', $txt['battle_custom'] ,'
                </td>
            </tr>
            <tr>
		<td class="windowbg2">
                    <table border="0" cellpadding="3" width="80%">
			<tr>
                            <td style="text-align: left;">
                                <br />
				<a href="', $scripturl, '?action=helpadmin;help=battleHelpCustIntro" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
				', $txt['battle_cust7'], '
                                <div>
                                    &nbsp;
                                </div>
                            </td>
                            <td style="text-align:left;width:60%;">
                                <textarea class="smalltext" name="start" style="width: 100%;height: 70px;box-sizing: border-box;" rows="20" cols="20">', $context['cust']['start'], '</textarea>
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
				<a href="', $scripturl, '?action=helpadmin;help=battleHelpCustGold" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_cust8'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="price" value="', $context['cust']['price'], '" />
                            </td>
			</tr>
		    </table>
                    <hr />
                    <table border="0" cellpadding="3" width="80%">
                        <tr>
                            <td style="text-align: left;width:40%;">
				<a href="', $scripturl, '?action=helpadmin;help=battleHelpCustOne" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_cust1'], '
                            </td>
                            <td style="width:60%;">
                                <textarea class="smalltext" name="outcome1" style="width: 100%;height: 70px;box-sizing: border-box;" rows="20" cols="20">', $context['cust']['outcome1'], '</textarea>
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
				<a href="', $scripturl, '?action=helpadmin;help=battleHelpCustOneRewAmt" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_cust3'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="outcome1_reward" value="', $context['cust']['outcome1_reward'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
				<a href="', $scripturl, '?action=helpadmin;help=battleHelpCustOneRewStat" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_cust5'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="outcome1_action" value="', $context['cust']['outcome1_action'], '" />
                            </td>
                        </tr>
                    </table>
                    <hr />
                    <table border="0" cellpadding="3" width="80%">
                        <tr>
                            <td style="text-align: left;width:40%;">
				<a href="', $scripturl, '?action=helpadmin;help=battleHelpCustTwo" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_cust2'], '
                            </td>
                            <td style="width:60%;">
                                <textarea class="smalltext" name="outcome2" style="width: 100%;height: 70px;box-sizing: border-box;" rows="20" cols="20">', $context['cust']['outcome2'], '</textarea>
                            </td>
			</tr>
			<tr>
                            <td style="text-align:left;">
				<a href="', $scripturl, '?action=helpadmin;help=battleHelpCustTwoRewAmt" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_cust4'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="outcome2_reward" value="', $context['cust']['outcome2_reward'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
				<a href="', $scripturl, '?action=helpadmin;help=battleHelpCustTwoRewStat" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_cust6'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="outcome2_action" value="', $context['cust']['outcome2_action'], '" />
                            </td>
			</tr>
                        <tr>
                            <td colspan="2">
                                <div style="width:78%;position:absolute;">
                                    <hr />
                                </div>
                            </td>
                        </tr>
                    </table>
		    <table border="0" cellpadding="3" width="100%">
			<tr class="centertext">
			    <td colspan="3" style="text-align: center;">
				<div style="line-height:5px;">&nbsp;</div>
				<input type="submit" name="submit" value="', $txt['battle_save'], '" />
			    </td>
			</tr>
			<tr class="catbg">
			    <td style="font-weight:bold;line-height:3px;" colspan="3">
				&nbsp;
			    </td>
			</tr>
		    </table>
		</td>
            </tr>
	</table>
	<input type="hidden" name="cust" value="', $context['cust']['id'], '" />
	<input type="hidden" name="sc" value="', $context['session_id'], '" />
    </form>';
}

function template_battle_custom()
{
    global $context, $txt, $scripturl, $settings;

    echo '
    <table width="90%" border="0" cellspacing="1" cellpadding="4" class="bordercolor centertext">
        <tr>
            <td width="100%" class="centertext titlebg" >
                ', $txt['battle_ce'], '
            </td>
        </tr>
        <tr>
            <td width="15%" class="centertext windowbg2" style="font-weight:bold;">
                <a href="', $scripturl, '?action=admin;area=battle;sa=custom_add">
                    ', $txt['battle_cea'], '
                </a>
            </td>
        </tr>';

    foreach ($context['battle_cust'] as $key => $row)
    {
	$class = $key % 2 == 0 ? 'windowbg' : 'windowbg2';
        echo'
        <tr>
            <td width="15%" align="left" class="', $class, '">
                <span style="font-weight:bold;">
                    ', strlen($row['start']) > 103 ? substr($row['start'], 0, 100) . '...' : $row['start'], '
                </span>
                <span style="float:right;">
                    <a href="', $scripturl, '?action=admin;area=battle;sa=custom_edit;cust=', $row['id_explore'], '">
                        <img style="position:relative;padding: 0px 2px 0px 2px;width:15px;height:15px;" src="', $settings['default_theme_url'], '/images/battle/wrench.gif" title="', $txt['battle_cust_edit'], '" alt="', $txt['battle_edit'], '" />
                    </a>
                    <a href="', $scripturl, '?action=admin;area=battle;sa=custom_del;cust=', $row['id_explore'], '" onclick="var check = confirm(\'', $txt['battle_confirmation'] ,'\');if(!check){return false;}">
                        <img style="position:relative;padding: 0px 2px 0px 2px;width:16px;height:16px;" src="', $settings['default_theme_url'], '/images/battle/minus.png" title="', $txt['battle_cust_delete'], '" alt="', $txt['battle_del'], '" />
                    </a>
                </span>
            </td>
        </tr>';
    }

   echo '
	<tr class="catbg">
            <td style="font-weight:bold;line-height:3px;">
		&nbsp;
            </td>
	</tr>
    </table>', $context['battle_display']['page'];
}

function template_battle_quest()
{
    global $context, $txt, $scripturl, $settings;
    echo '
    <table width="90%" border="0" cellspacing="1" cellpadding="4" class="bordercolor centertext">
        <tr>
            <td class="titlebg" style="width:70%;text-align:left;">
                ', $txt['battle_questss'], '
            </td>
	    <td class="titlebg" style="width:15%;text-align:left;">
                ', $txt['battle_campaign_id'], '
	    </td>
	    <td class="titlebg" style="width:15%;text-align:right;">
		<span>
		    ', $txt['battle_campaign_command'], '
		</span>
            </td>
        </tr>
        <tr>
            <td class="centertext windowbg2" style="font-weight:bold;width:100%;" colspan="3">
                <a href="', $scripturl, '?action=admin;area=battle;sa=quest_add">
                    ', $txt['battle_Quest_add'], '
                </a>
            </td>
        </tr>';

    foreach ($context['battle_quest'] as $key => $row)
    {
	$class = $key % 2 == 0 ? 'windowbg' : 'windowbg2';
        echo'
        <tr>
            <td class="', $class, '" style="width:70%;text-align:left;">
                <span style="font-weight:bold;">
                    ', strlen($row['name']) > 90 ? substr($row['name'], 0, 87) . '...' : $row['name'], '
                </span>
	    </td>
	    <td class="', $class, '" style="width:15%;text-align:left;">
		<span class="smalltext" style="font-style:oblique;">
                    ', $row['campaign'], '
                </span>
	    </td>
	    <td class="', $class, '" style="width:15%;text-align:right;">
                <span>
                    <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=quest_edit;quest=', $row['id_quest'], '">
                        <img style="position:relative;padding: 0px 2px 0px 2px;width:15px;height:15px;" src="' . $settings['default_theme_url'] . '/images/battle/wrench.gif" title="' . $txt['battle_Quest_edit'] . '" alt="' . $txt['battle_edit'] . '" />
                    </a>
                    <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=quest_del;quest=', $row['id_quest'], '" onclick="var check = confirm(\'', $txt['battle_confirmation'] ,'\');if(!check){return false;}">
                        <img style="position:relative;padding: 0px 2px 0px 2px;width:16px;height:16px;" src="', $settings['default_theme_url'], '/images/battle/minus.png" title="' . $txt['battle_Quest_delete'] . '" alt="' . $txt['battle_del'] . '" />
                    </a>
                </span>
            </td>
        </tr>';
    }

   echo '
	<tr class="catbg">
            <td style="font-weight:bold;line-height:3px;" colspan="3">
		&nbsp;
            </td>
	</tr>
    </table>', $context['battle_display']['page'];
}

function template_questEdit_Add()
{
    global $context, $scripturl, $txt, $settings;
    echo '
    <form action="', $scripturl, '?action=admin;area=battle;save;sa=quest_save" method="post" name="theAdminForm" accept-charset="', $context['character_set'], '">
        <table border="0" width="80%" cellspacing="0" cellpadding="3" class="centertext tborder">
            <tr class="titlebg">
                <td>
                    ', $txt['battle_questss'], '
                </td>
            </tr>
            <tr>
                <td class="windowbg2">
                    <table border="0" cellpadding="3" width="80%">
                        <tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestName" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_name'], '
                            </td>
                            <td style="text-align:left;">
                                <input style="width:100%;" type="text" name="name" size="35" maxlength="255" value="', $context['quest']['name'], '" />
                            </td>
                        </tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestMinSucg" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_minSucg'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="min_gold" size="8" value="', $context['quest']['min_gold'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestMaxSucg" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_sucg'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="gold" size="8" value="', $context['quest']['gold'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestMinEnerg" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_MinEnerg'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="min_exp" size="8" value="', $context['quest']['min_exp'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestMaxEnerg" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_Energ'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="exp" size="8" value="', $context['quest']['exp'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestMaxStatLoss" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_RandStat'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="max_penalty" size="8" value="', $context['quest']['max_penalty'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestMaxStatWin" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_RandStatGain'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="max_gain" size="8" value="', $context['quest']['max_gain'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestMaxHP" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_HP'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="hp" size="8" value="', $context['quest']['hp'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestReqEnerg" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_qen'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="energy" size="8" value="', $context['quest']['energy'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestReqLvl" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_lr'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="level" size="8" value="', $context['quest']['level'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestFormula" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_sucf'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="success" size="8" value="', $context['quest']['success'], '" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestLimit" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_QuestLimit'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="text" name="limit" size="8" value="', $context['quest']['limit'], '" />
                            </td>
			</tr>
                        <tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestInitText" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_itxt'], '
                            </td>
                            <td>
                                <textarea class="smalltext" name="itext" style="width: 100%;height: 70px;box-sizing: border-box;" rows="20" cols="20">', $context['quest']['itext'], '</textarea>
                            </td>
        		</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestSuccText" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_suc'], '
                            </td>
                            <td>
                                <textarea class="smalltext" name="stext" style="width: 100%;height: 70px;box-sizing: border-box;" rows="20" cols="20">', $context['quest']['stext'], '</textarea>
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestFailText" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_Quest_fail'], '
                            </td>
                            <td>
                                <textarea class="smalltext" name="ftext" style="width: 100%;height: 70px;box-sizing: border-box;" rows="20" cols="20">', $context['quest']['ftext'], '</textarea>
                            </td>
                        </tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestPrem" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_final_quest'], '
                            </td>
                            <td style="text-align:left;">
                                <input type="checkbox" name="is_final" class="input_check" ', ( $context['quest']['is_final'] ? ' checked="checked" ' : ''), ' />
                            </td>
                        </tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpQuestCampaign" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_campaign_opt'], '
                            </td>
                            <td style="text-align:left;">
                                <select name="campaign_id" id="option" style="display:block;position:relative;">
				    <option value="0"', ($context['quest']['campaign_id'] == '0' ? ' selected="selected"' : ''), '>
					', $txt['battle_none'], '
				    </option>';
    foreach ($context['battle_campaigns'] as $key => $campaign)
    	echo '
				    <option value="', $campaign['id_campaign'], '"', ($context['quest']['campaign_id'] == $campaign['id_campaign'] ? ' selected="selected"' : ''), '>
					', $campaign['campaign_name'], '
				    </option>';

    echo '
				</select>
                            </td>
                        </tr>
                    </table>
		</td>
            </tr>
	    <tr>
		<td colspan="2" class="centertext windowbg2">
		    <br />
		    <input type="submit" name="submit" value="', $txt['battle_save'], '" />
		</td>
	    </tr>
	    <tr class="catbg">
		<td style="font-weight:bold;line-height:3px;" colspan="2">
		    &nbsp;
		</td>
	    </tr>
        </table>
	<input type="hidden" name="quest" value="', $context['quest']['id'], '" />
	<input type="hidden" name="sc" value="', $context['session_id'], '" />
    </form>';
}

function template_monsterEdit_Add()
{
	global $context, $scripturl, $settings, $txt;

	echo '
    <form action="', $scripturl, '?action=admin;area=battle;save;sa=savemonster" method="post" name="theAdminForm" accept-charset="', $context['character_set'], '">
        <table border="0" width="80%" cellspacing="1" cellpadding="3" class="tborder windowbg2 centertext">
            <tr class="titlebg">
                <td colspan="2" style="text-align:center;">
                    ', $txt['battle_monsters'], '
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <a href="', $scripturl, '?action=helpadmin;help=battleHelpMonsterName" onclick="return reqWin(this.href);" style="text-decoration:none;">
                        <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
                    </a>
                    ', $txt['battle_monsters_name'], '
                </td>
                <td style="text-align:left;">
                    <input type="text" name="name" value="', $context['monster']['name'], '" />
                </td>
            </tr>
            <tr>
                <td style="text-align:left;">
                    <a href="', $scripturl, '?action=helpadmin;help=battleHelpMonsterAtk" onclick="return reqWin(this.href);" style="text-decoration:none;">
                        <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
                    </a>
                    ', $txt['battle_monsters_atk'], '
                </td>
                <td style="text-align:left;">
                    <input type="text" name="atk" value="', $context['monster']['atk'], '" size="9" maxlength="8" />
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <a href="', $scripturl, '?action=helpadmin;help=battleHelpMonsterDef" onclick="return reqWin(this.href);" style="text-decoration:none;">
                        <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
                    </a>
                    ', $txt['battle_monsters_def'], '
                </td>
                <td style="text-align:left;">
                    <input type="text" name="def" value="', $context['monster']['def'], '" size="9" maxlength="8" />
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <a href="', $scripturl, '?action=helpadmin;help=battleHelpMonsterHP" onclick="return reqWin(this.href);" style="text-decoration:none;">
                        <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
                    </a>
                    ', $txt['battle_monsters_hp'], '
                </td>
                <td style="text-align:left;">
                    <input type="text" name="hp" value="', $context['monster']['hp'], '" size="9" maxlength="8" />
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <a href="', $scripturl, '?action=helpadmin;help=battleHelpMonsterMaxHP" onclick="return reqWin(this.href);" style="text-decoration:none;">
                        <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
                    </a>
                    ', $txt['battle_monsters_m_hp'], '
                </td>
                <td style="text-align:left;">
                    <input type="text" name="max_hp" value="', $context['monster']['max_hp'], '" size="9" maxlength="8" />
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <a href="', $scripturl, '?action=helpadmin;help=battleHelpMonsterRange" onclick="return reqWin(this.href);" style="text-decoration:none;">
                        <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
                    </a>
                    ', $txt['battle_monsters_range'], '
                </td>
                <td style="text-align:left;">
                    <span>
                        <input type="text" name="mon_range" size="6" maxlength="5" value="', $context['monster']['mon_range'], '" size="6" maxlength="5" />
                    </span>
                    <span style="position:relative;left:10px;">
                        <input type="text" name="mon_max_range" size="9" maxlength="8" value="', $context['monster']['mon_max_range'], '" size="8" maxlength="7" />
                    </span>
                </td>
            </tr>
	    <tr>
                <td style="text-align: left;">
                    <a href="',$scripturl,'?action=helpadmin;help=battleHelpMonsterEvolve" onclick="return reqWin(this.href);" style="text-decoration:none;">
                        <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
                    </a>
                    ', $txt['battle_monsters_evolve'], '
                </td>
                <td style="text-align:left;">
                    <input type="text" name="evolve" value="', $context['monster']['evolve'], '" size="8" maxlength="7" />
                </td>
            </tr>
            <tr>
                <td colspan="2" style="line-height:20px;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="2" style="line-height:1px;">
                    <hr />
                </td>
            </tr>
            <tr>
                <td colspan="2" style="line-height:20px;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td style="vertical-align: bottom;" class="centertext" colspan="2">
                    <span style="position:relative;">
                        <a href="',$scripturl,'?action=helpadmin;help=battleHelpMonsterImage" onclick="return reqWin(this.href);" style="text-decoration:none;">
                            <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/battle/battle-help.gif" alt="?" />
                        </a>
                        ', $txt['battle_monsters_img'], '
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="centertext">
                    <center>
                        <select name="img" id="opt" onchange="show_image()" style="display:block;position:relative;">
                            <option value="blank.gif"', ($context['monster']['img'] == 'blank.gif' ? ' selected="selected"' : ''), '>
                                ', $txt['battle_none'], '
                            </option>';

	// Get all images for the dropdown list
	foreach ($context['battle_images'] as $image)
		echo '
                        <option value="', $image, '"', ($context['monster']['img'] == $image ? ' selected="selected"' : ''), '>
                            ', $image, '
                        </option>';

	echo '
                        </select>
                    </center>
                </td>
            </tr>
            <tr>
                <td style="height: 145px;" colspan="2" class="centertext">
                    <span style="position:relative;">
                        <img id="icon" src="', $settings['images_url'], '/battle/monsters/', $context['monster']['img'], '" border="1" style="max-height:140px;max-width:140px;" alt="" />
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="centertext">
                    <br />
                    <input type="submit" name="submit" value="'.$txt['battle_save'].'" />
                </td>
            </tr>
	    <tr class="catbg">
		<td style="font-weight:bold;line-height:3px;" colspan="2">
		    &nbsp;
		</td>
	    </tr>
        </table>
        <input type="hidden" name="monster" value="', $context['monster']['id'], '" />
        <input type="hidden" name="sc" value="', $context['session_id'], '" />
    </form>
    <script type="text/javascript" language="javascript"><!-- // --><![CDATA[
        function show_image()
        {
            var myopt = document.getElementById("opt");
            var myimage = document.getElementById("icon");
            if (myopt.value !== "blank.gif")
            {
                var image_url = "', $settings['images_url'], '/battle/monsters/" + myopt.value;
                myimage.src = image_url;
                myimage.style = "display:inline;max-height:140px;max-width:140px;";
            }
            else
            {
                myimage.src = "', $settings['images_url'], '/battle/monsters/blank.gif";
                myimage.style = "height:0px;width:0px;";
            }
        }
    // ]]></script>';
}

function template_battle_admin_monsters()
{
    // Gotta global it other wise thing dont work properly
    global  $context, $settings,  $scripturl, $request, $txt;
    echo '
    <table width="90%" border="0" cellspacing="1" cellpadding="4" class="bordercolor centertext">
        <tr>
            <td width="100%" class="centertext titlebg" >
                ', $txt['battle_monsters'], '
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;width:15%;" class="centertext windowbg2" >
                <a href="', $scripturl, '?action=admin;area=battle;sa=monster">
                    ', $txt['battle_monsters_add'], '
                </a>
            </td>
        </tr>
    </table>
    <table width="90%" border="0" cellspacing="0" cellpadding="4" class="bordercolor centertext">
    ';

    foreach ($context['get_monsters'] as $key => $row)
    {
        $class = $key % 2 == 0 ? 'windowbg' : 'windowbg2';
        echo'
        <tr>
            <td align="left" class="' . $class . '" style="padding-left:1%;line-height:25px;width:30%;">
                <img src="', $settings['images_url'], '/battle/monsters/', $row['img'], '" width="35" height="35" alt="" />
                <span style="position:relative;bottom:12px;">
                    ', strlen($row['name']) > 30 ? substr($row['name'], 0, 27) . '...' : $row['name'], '
                </span>
            </td>
            <td align="right" class="', $class, ' centertext" style="line-height:25px;width:5%;">
                <img border="0" src="', $settings['images_url'], '/battle/bomb.png" alt="" style="vertical-align:middle;margin-right:-20px;" />
            </td>
            <td align="left" class="', $class, '" style="padding-left:1%;line-height:25px;width:15%;">
                <span style="position:relative;">
                    ', $txt['battle_atk'], '=', $row['atk'], '
                </span>
            </td>
            <td align="left" class="', $class, ' centertext" style="line-height:25px;width:5%;">
                <img border="0" src="', $settings['images_url'], '/battle/shield.png" alt="" style="vertical-align:middle;margin-right:-20px;" />
            </td>
            <td align="left" class="', $class, '" style="padding-left:1%;line-height:25px;width:15%;">
                <span style="position:relative;">
                    ', $txt['battle_def'], '=', $row['def'], '
                </span>
            </td>
            <td align="left" class="', $class, '" style="padding-right:1%;line-height:25px;width:30%;">
                <span style="float:right;">
                    <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=editm;monster=', $row['id_monster'], '">
                    	<img style="position:relative;padding: 0px 2px 0px 2px;width:15px;height:15px;" src="', $settings['default_theme_url'], '/images/battle/wrench.gif" title="' . $txt['battle_monsters_edit'], '" alt="', $txt['battle_edit'], '" />
                    </a>
                    <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=del;monster=', $row['id_monster'], '" onclick="var check = confirm(\'', $txt['battle_confirmation'] ,'\');if(!check){return false;}">
                    	<img style="position:relative;padding: 0px 2px 0px 2px;width:16px;height:16px;" src="', $settings['default_theme_url'], '/images/battle/minus.png" title="', $txt['battle_monsters_delete'], '" alt="', $txt['battle_del'], '" />
                    </a>
                </span>
            </td>
        </tr>';
    }

    echo '
	<tr class="catbg">
            <td style="font-weight:bold;line-height:3px;" colspan="7">
		&nbsp;
            </td>
	</tr>
    </table>', $context['battle_display']['page'];
}

function template_battle_campaigns()
{
    global $context, $txt, $scripturl, $settings;

    echo '
    <table width="90%" border="0" cellspacing="1" cellpadding="4" class="bordercolor centertext">
        <tr>
            <td width="100%" class="centertext titlebg" >
                ', $txt['battle_campaign_title'], '
            </td>
        </tr>
	<tr>
            <td style="font-weight:bold;width:15%;" class="centertext windowbg" >
                <a href="', $scripturl, '?action=admin;area=battle;sa=add_campaign">
                    ', $txt['battle_campaign_new'], '
                </a>
            </td>
        </tr>';

    foreach ($context['campaign'] as $key => $row)
    {
	$class = $key % 2 == 0 ? 'windowbg' : 'windowbg2';
        echo'
        <tr>
            <td align="left" class="', $class, '" style="padding-left:1%;line-height:25px">
                <img style="width:20px;height:20px;vertical-align:middle;" border="0" src="', $settings['images_url'], '/battle/campaign/', $row['img'], '" alt="X"  title="', $row['campaign_name'], '" />
                <span style="font-weight:bold;position:relative;vertical-align:middle;">
                    ', strlen($row['campaign_name']) > 100 ? substr($row['campaign_name'], 0, 97) . '...' : $row['campaign_name'], '
                </span>
                <span style="float:right;position:relative;vertical-align:middle;">
		    <span style="position:relative;right:30px;font-style:oblique;font-weight:bold;">
			', !empty($context['campaign_pruned'][$row['id_campaign']]) ? $txt['battle_campaign_pruned'] : '','
		    </span>
                    <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=edit_campaign;id_campaign=', $row['id_campaign'], ';">
                        <img style="position:relative;padding: 0px 2px 0px 2px;width:15px;height:15px;" src="', $settings['default_theme_url'], '/images/battle/wrench.gif" title="', $txt['battle_campaign_edit'], '" alt="', $txt['battle_edit'], '" />
                    </a>
		    <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=prune_campaign;id_campaign=', $row['id_campaign'], ';">
                        <img style="position:relative;padding: 0px 2px 0px 2px;width:16px;height:16px;" src="', $settings['default_theme_url'], '/images/battle/trash.png" title="', $txt['battle_campaign_prune'], '" alt="', $txt['battle_prune'], '" />
                    </a>
		    <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=del_campaign;id_campaign=', $row['id_campaign'], ';" onclick="var check = confirm(\'', $txt['battle_confirmation'] ,'\');if(!check){return false;}">
                        <img style="position:relative;padding: 0px 2px 0px 2px;width:16px;height:16px;" src="', $settings['default_theme_url'], '/images/battle/minus.png" title="', $txt['battle_campaign_delete'], '" alt="', $txt['battle_del'], '" />
                    </a>';

	echo '
                </span>
            </td>
        </tr>';
    }

   echo '
	<tr class="catbg">
            <td style="font-weight:bold;line-height:3px;">
		&nbsp;
            </td>
	</tr>
    </table>', $context['battle_display']['page'];
}

function template_campaignEdit()
{
    global $context, $scripturl, $settings, $txt;
    echo '
    <form action="', $scripturl, '?action=admin;area=battle;save;sa=save_campaign;id_campaign=', $context['campaign_id'], '" method="post" name="theAdminForm" accept-charset="', $context['character_set'], '">
        <table border="0" width="80%" cellspacing="0" cellpadding="3" class="centertext tborder windowbg2">
            <tr class="titlebg">
                <td>
                    ', $txt['battle_campaign_title'], '
                </td>
            </tr>
            <tr>
                <td class="windowbg2">
                    <table border="0" cellpadding="3" width="80%">
                        <tr>
                            <td style="text-align: left;width:70%;white-space: nowrap;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpCampaignName" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_campaign_name'], '
                            </td>
                            <td style="text-align: left;width:30%;">
				<input type="text" name="campaign_name" value="', $context['campaign']['campaign_name'], '" size="30" maxlength="30" />
                            </td>
                        </tr>
			<tr>
                            <td style="text-align: left;width:70%;white-space: nowrap;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpCampaignTimed" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_campaign_timed'], '
                            </td>
                            <td style="text-align: left;width:30%;">
                                <input type="checkbox" name="timed_campaign" class="input_check" ', ($context['campaign']['timed_campaign'] ? ' checked="checked" ' : ''), ' value="1" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;width:70%;white-space: nowrap;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpCampaignLevel" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_campaign_lvl_completion'], '
                            </td>
                            <td style="text-align: left;width:30%">
                                <input type="checkbox" name="level_completion" class="input_check" ', ($context['campaign']['level_completion'] ? ' checked="checked" ' : ''), ' value="1" />
                            </td>
                        </tr>
			<tr>
                            <td style="text-align: left;width:70%;white-space: nowrap;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpCampaignStartDate" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_campaign_start_date'], '
                            </td>
                            <td style="text-align: left;width:30%;">
                                <input type="text" name="start_date" value="', $context['campaign']['start_date'], '" size="10" maxlength="10" />
                            </td>
                        </tr>
			<tr>
                            <td style="text-align: left;width:70%;white-space: nowrap;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpCampaignStartTime" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_campaign_start_hours'], '
                            </td>
                            <td style="text-align: left;width:30%;">
                                <input type="text" name="start_hours" value="', $context['campaign']['start_hours'], '" size="10" maxlength="10" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;width:70%;white-space: nowrap;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpCampaignEndDate" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_campaign_end_date'], '
                            </td>
                            <td style="text-align: left;width:30%;">
                                <input type="text" name="end_date" value="', $context['campaign']['end_date'], '" size="10" maxlength="10" />
                            </td>
                        </tr>
			<tr>
                            <td style="text-align: left;width:70%;white-space: nowrap;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpCampaignEndTime" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_campaign_end_hours'], '
                            </td>
                            <td style="text-align: left;width:30%;">
                                <input type="text" name="end_hours" value="', $context['campaign']['end_hours'], '" size="10" maxlength="10" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;width:70%;white-space: nowrap;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpCampaignCurrentTime" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_campaign_current_time'], '
                            </td>
                            <td style="text-align: left;width:30%;">
                                ', $context['campaign']['current_time'], '
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;width:70%;white-space: nowrap;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpCampaignCurrentStatus" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_campaign_status'], '
                            </td>
                            <td style="text-align: left;width:30%;">
                                ', $context['campaign']['current_status'], '
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;width:70%;white-space: nowrap;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpCampaignMembergroups" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_campaign_membergroups'], '
                            </td>
                            <td style="text-align: left;width:30%;">
                                ', ltrim($context['campaign']['membergroups'], ', '), '
                            </td>
			</tr>
                    </table>
                </td>
	    <tr>
                <td colspan="2" style="line-height:20px;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="2" style="line-height:1px;">
                    <hr />
                </td>
            </tr>
            <tr>
                <td colspan="2" style="line-height:20px;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td style="vertical-align: bottom;" class="centertext" colspan="2">
                    <span style="position:relative;">
                        <a href="', $scripturl, '?action=helpadmin;help=battleHelpCampaignImage" onclick="return reqWin(this.href);" style="text-decoration:none;">
                            <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
                        </a>
                        ', $txt['battle_campaign_image'], '
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="centertext">
                    <center>
                        <select name="image" id="opt" onchange="show_image()" style="display:block;position:relative;">
                            <option value="blank.gif"', ($context['campaign']['img'] == 'blank.gif' ? ' selected="selected"' : ''), '>
                                ', $txt['battle_none'], '
                            </option>';

	// Get all images for the dropdown list
	foreach ($context['campaign_images'] as $image)
		echo '
                        <option value="', $image, '"', ($context['campaign']['img'] == $image ? ' selected="selected"' : ''), '>
                            ', $image, '
                        </option>';

	echo '
                        </select>
                    </center>
                </td>
            </tr>
            <tr>
                <td style="height: 145px;" colspan="2" class="centertext">
                    <span style="position:relative;">
                        <img id="icon" src="', $settings['images_url'], '/battle/campaign/', $context['campaign']['img'], '" border="1" style="max-height:140px;max-width:140px;', (!$context['campaign']['img'] ? 'display:none;' : ''), '" alt="" />
                    </span>
                </td>
            </tr>
            <tr class="windowbg2">
                <td class="centertext">
                    <input type="submit" name="submit" value="', $txt['battle_save'], '" />
                </td>
            </tr>
	    <tr class="catbg">
		<td style="font-weight:bold;line-height:3px;" colspan="2">
		    &nbsp;
		</td>
	    </tr>
	</table>
	<input type="hidden" name="id_campaign" value="', $context['campaign_id'], '" />
	<input type="hidden" name="sc" value="', $context['session_id'], '" />
    </form>
    <script type="text/javascript" language="javascript"><!-- // --><![CDATA[
        function show_image()
        {
            var myopt = document.getElementById("opt");
            var myimage = document.getElementById("icon");
            if (myopt.value !== "blank.gif")
            {
                var image_url = "', $settings['images_url'], '/battle/campaign/" + myopt.value;
                myimage.src = image_url;
                myimage.style = "display:inline;max-height:140px;max-width:140px;";
            }
            else
            {
                myimage.src = "', $settings['images_url'], '/battle/campaign/blank.gif";
                myimage.style = "display:none;";
            }
        }
    // ]]></script>';
}

function template_battle_shop()
{
    global $context, $txt, $scripturl, $settings;

    echo '
    <table width="90%" border="0" cellspacing="1" cellpadding="4" class="bordercolor centertext">
        <tr>
            <td width="100%" class="centertext titlebg" >
                ', $txt['battle_shop_item'], '
            </td>
        </tr>
        <tr>
            <td style="width:15%; font-weight:bold;" class="centertext windowbg2">
                <a href="', $scripturl, '?action=admin;area=battle;sa=add_item">
                    ', $txt['battle_shop_add'], '
                </a>
            </td>
        </tr>';

    foreach ($context['shop'] as $key => $row)
    {
	$class = $key % 2 == 0 ? 'windowbg' : 'windowbg2';
        echo'
        <tr>
            <td align="left" class="', $class, '" style="padding-left:1%;line-height:25px">
                <img border="0" src="', $settings['images_url'], '/battle/shop/', $row['img'], '" alt="X"  title="', $row['name'], '" />
                <span style="font-weight:bold;position:relative;bottom:12px;">
                    ', strlen($row['name']) > 100 ? substr($row['name'], 0, 97) . '...' : $row['name'], '
                </span>
                <span style="float:right;position:relative;top:12px;">
                    <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=edit_item;item=', $row['id_item'], '">
                        <img style="position:relative;padding: 0px 2px 0px 2px;width:15px;height:15px;" src="', $settings['default_theme_url'], '/images/battle/wrench.gif" title="', $txt['battle_shop_edit'], '" alt="', $txt['battle_edit'], '" />
                    </a>
                    <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=shop_del;item=', $row['id_item'], '" onclick="var check = confirm(\'', $txt['battle_confirmation'] ,'\');if(!check){return false;}">
                        <img style="position:relative;padding: 0px 2px 0px 2px;width:16px;height:16px;" src="', $settings['default_theme_url'], '/images/battle/minus.png" title="', $txt['battle_shop_delete'], '" alt="', $txt['battle_del'], '" />
                    </a>
                </span>
            </td>
        </tr>';
    }

   echo '
	<tr class="catbg">
            <td style="font-weight:bold;line-height:3px;">
		&nbsp;
            </td>
	</tr>
    </table>', $context['battle_display']['page'];
}

function template_shopEdit_Add()
{
    global $context, $scripturl, $settings, $txt;
    echo '
    <form action="', $scripturl, '?action=admin;area=battle;save;sa=save_item" method="post" name="theAdminForm" accept-charset="', $context['character_set'], '">
        <table border="0" width="80%" cellspacing="0" cellpadding="3" class="centertext tborder">
            <tr class="titlebg">
                <td>
                    ', $txt['battle_shop_item'], '
                </td>
            </tr>
            <tr>
                <td class="windowbg2">
                    <table border="0" cellpadding="3" width="80%">
                        <tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpShopName" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_shop_name'], '
                            </td>
                            <td style="text-align: left;">
                                <input type="text" name="name" value="', $context['item']['name'], '" />
                            </td>
                        </tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpShopPrice" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_shop_price'], '
                            </td>
                            <td style="text-align: left;">
                                <input type="text" name="price" value="', $context['item']['price'], '" size="8" maxlength="10" />
                            </td>
			</tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpShopAction" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_shop_action'], '
                            </td>
                            <td style="text-align: left;">
                                <input type="text" name="act" value="', $context['item']['action'], '" size="8" maxlength="10" />
                            </td>
                        </tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpShopAmount" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_shop_amount'], '
                            </td>
                            <td style="text-align: left;">
                                <input type="text" name="amount" value="', $context['item']['amount'], '" size="8" maxlength="10" />
                            </td>
                        </tr>
			<tr>
                            <td style="text-align: left;">
                                <a href="', $scripturl, '?action=helpadmin;help=battleHelpShopDescript" onclick="return reqWin(this.href);" style="text-decoration:none;">
                                    <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
				</a>
                                ', $txt['battle_shop_d'], '
                            </td>
                            <td style="text-align: left;">
                                <textarea class="smalltext" name="description" style="width: 100%;height: 70px;box-sizing: border-box;" rows="20" cols="20">', $context['item']['description'], '</textarea>
                            </td>
			</tr>
                    </table>
                </td>
            <tr class="windowbg2">
                <td style="line-height:20px;border:0px;">
                    &nbsp;
                </td>
            </tr>
            <tr class="windowbg2">
                <td style="line-height:1px;">
                    <hr />
                </td>
            </tr>
            <tr class="windowbg2">
                <td style="line-height:20px;">
                    &nbsp;
                </td>
            </tr>
            <tr class="windowbg2">
                <td style="vertical-align: bottom;" class="centertext">
                    <span style="position:relative;">
                        <a href="', $scripturl, '?action=helpadmin;help=battleHelpShopImage" onclick="return reqWin(this.href);" style="text-decoration:none;">
                            <img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="', $settings['default_theme_url'], '/images/battle/battle-help.gif" alt="?" />
			</a>
                        ', $txt['battle_shop_img'], '
                    </span>
                </td>
            <tr class="windowbg2">
                <td class="centertext">
                    <center>
                        <select name="img" id="opt" onchange="show_image()" style="display:block;position:relative;">
                            <option value="blank.gif"', ($context['item']['img'] == 'blank.gif' ? ' selected="selected"' : ''), '>
                                ', $txt['battle_none'], '
                            </option>';

	// Get all images for the dropdown list
	foreach ($context['shop_images'] as $image)
		echo '
                            <option value="', $image, '"', ($context['item']['img'] == $image ? ' selected="selected"' : ''), '>
                                ', $image, '
                            </option>';

	echo '
                        </select>
                    </center>
                </td>
            </tr>
            <tr class="windowbg2">
                <td style="height: 145px;" class="centertext">
                    <span style="position:relative;">
                        <img id="icon" src="', $settings['images_url'], '/battle/shop/', $context['item']['img'], '" border="1" style="max-height:140px;max-width:140px;" alt="" />
                    </span>
                </td>
            </tr>
            <tr class="windowbg2">
                <td class="centertext">
                    <input type="submit" name="submit" value="', $txt['battle_save'], '" />
                </td>
            </tr>
	    <tr class="catbg">
		<td style="font-weight:bold;line-height:3px;">
		    &nbsp;
		</td>
	    </tr>
	</table>
	<input type="hidden" name="item" value="', $context['item']['id'], '" />
	<input type="hidden" name="sc" value="', $context['session_id'], '" />
    </form>
    <script type="text/javascript" language="javascript"><!-- // --><![CDATA[
        function show_image()
        {
            var myopt = document.getElementById("opt");
            var myimage = document.getElementById("icon");
            if (myopt.value !== "blank.gif")
            {
                var image_url = "', $settings['images_url'], '/battle/shop/" + myopt.value;
                myimage.src = image_url;
                myimage.style = "display:inline;max-height:140px;max-width:140px;";
            }
            else
            {
                myimage.src = "', $settings['images_url'], '/battle/shop/blank.gif";
                myimage.style = "display:none;";
            }
        }
    // ]]></script>';
}

function template_bmembers()
{
    global $b, $txt, $sc, $scripturl, $context, $settings;
    global $userid;

    isAllowedTo('admin_battle');

    if((isset($_GET['next']) || isset($_GET['update'])) && isset($userid))
    {
        echo '
    <form action="',$scripturl,'?action=admin;area=battle;sa=bmem;update;current_page=', $context['current_page']+1, ';order=', $context['battle_order'], ';sort=',$context['battle_sort'], $context['battle_userid'], ';sesc=', $sc, '" method="post">
        <table class="bordercolor" border="0" cellpadding="4" cellspacing="1" width="100%">
            <tr class="titlebg">
                <td colspan="10" class="largetext centertext">', $txt['battle_mem'], '</td>
            </tr>
            <tr class="windowbg">
		<td style="text-align:left;vertical-align:middle;">
		    <input id="reset_all1" type="checkbox" name="reset_all_new" value="1" style="vertical-align:middle;" onclick="if (document.getElementById(\'reset_all1\').checked){var check = confirm(\'', $txt['battle_confirmation'] ,'\');if(!check){return false;}}" />
		    <span style="vertical-align:middle;font-size:9px;">
			', $txt['battle_warrior_reset_all_new'], '
		    </span>
                </td>
                <td colspan="2" style="text-align:right;vertical-align:middle;">
		    <span style="vertical-align:middle;font-size:9px;">
			', $txt['battle_warrior_reset_all'], '
		    </span>
                    <input id="reset_all2" type="checkbox" name="reset_all" value="1" style="vertical-align:middle;" onclick="if (document.getElementById(\'reset_all2\').checked){var check = confirm(\'', $txt['battle_confirmation'] ,'\');if(!check){return false;}}" />
                </td>
            </tr>
            <tr class="windowbg2">
                <td>
                    ', $txt['battle_gold'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="cash" size="10" value="', $context['battle_warriorInfo'][$userid]['gold'], '" />
                </td>
            </tr>
            <tr class="windowbg">
                <td>
                    ', $txt['battle_lvl'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="level" size="10" value="', $context['battle_warriorInfo'][$userid]['level'], '" />
                </td>
            </tr>
            <tr class="windowbg2">
                <td>
                    ', $txt['battle_hp'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="hp" size="10" value="', $context['battle_warriorInfo'][$userid]['hp'], '"/>
                </td>
            </tr>
            <tr class="windowbg">
                <td>
                    ', $txt['battle_atk'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="atk" size="10" value="', $context['battle_warriorInfo'][$userid]['atk'], '"/>
                </td>
            </tr>
            <tr class="windowbg2">
                <td>
                    ', $txt['battle_def'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="def" size="10" value="', $context['battle_warriorInfo'][$userid]['def'], '"/>
                </td>
            </tr>
            <tr class="windowbg">
                <td>
                    ', $txt['battle_energy'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="energy" size="10" value="', $context['battle_warriorInfo'][$userid]['energy'], '"/>
                </td>
            <tr class="windowbg2">
                <td>
                    ', $txt['battle_Stamina'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="stam" size="10" value="', $context['battle_warriorInfo'][$userid]['stamina'], '"/>
                </td>
            </tr>
            <tr class="windowbg">
                <td>
                    ', $txt['battle_matk'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="max_atk" size="10" value="', $context['battle_warriorInfo'][$userid]['max_atk'], '"/>
                </td>
            </tr>
            <tr class="windowbg2">
                <td>
                    ', $txt['battle_mdef'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="max_def" size="10" value="', $context['battle_warriorInfo'][$userid]['max_def'], '"/>
                </td>
            </tr>
            <tr class="windowbg">
                <td>
                    ', $txt['battle_menergy'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="max_energy" size="10" value="', $context['battle_warriorInfo'][$userid]['max_energy'], '"/>
                </td>
            </tr>
            <tr class="windowbg2">
                <td>
                    ', $txt['battle_mStamina'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="max_stam" size="10" value="', $context['battle_warriorInfo'][$userid]['max_stamina'], '"/>
                </td>
            </tr>
            <tr class="windowbg">
                <td>
                    ', $txt['battle_mhp'], ':
                </td>
                <td colspan="2">
                    <input type="text" name="max_hp" size="10" value="', $context['battle_warriorInfo'][$userid]['max_hp'], '" />
                </td>
            </tr>
            <tr class="windowbg2">
                <td style="float:left;">
                    <input type="button" onclick="window.open(\'', $scripturl, '?action=admin;area=battle;sa=bmem;order=', $context['battle_order'],';sort=', $context['battle_sort'], ';current_page=', $context['current_page']+1, ';\',\'_self\',\'resizable=yes\')" value="', $txt['battle_back'], '" />
                </td>
                <td>
                    <input type="hidden" name="sc" value="', $sc, '" />
                    <input type="hidden" name="thename" value="', $userid, '" />
                    &nbsp;
                </td>
                <td>
                    <span style="float:right;">
                        <input type="submit" value="', $txt['battle_save'], '" />
                    </span>
                    <span style="float:right;position:relative;right:2%;top:2px;">
                        <span style="bottom:3px;position:relative;font-size:9px;">
                            ', $txt['battle_defaults'], '
                        </span>
                        <input id="default" type="checkbox" name="defaults" value="1" onclick="if (document.getElementById(\'default\').checked){var check = confirm(\'', $txt['battle_confirmation'] ,'\');if(!check){return false;}}" />
                    </span>
                </td>
            </tr>
            <tr class="titlebg">
                <td colspan="3" class="centertext">
                    ', $txt['battle_add_warrior'], '
                    ', $context['battle_userlink'], '
                </td>
            </tr>';
    }
    else
    {
        echo '
    <form action="', $scripturl, '?action=admin;area=battle;sa=bmem;next;current_page=', $context['current_page']+1, ';order=', $context['battle_order'], ';sort=', $context['battle_sort'], ';sesc=', $sc, '" method="post">
        <table class="bordercolor" border="0" cellpadding="4" cellspacing="1" width="100%">
            <tr class="titlebg">
                <td colspan="10" class="centertext largetext">
                    ', $txt['battle_mem'], '
                </td>
            </tr>
            <tr class="windowbg2">
                <td style="width:20%">
                    ', $txt['battle_entername'], '
                </td>
                <td style="width:60%;">
                    <div style="text-align:center;">
                        <input style="width:60%;" type="text" name="thename" />
                    </div>
                </td>
                <td style="width:20%;">
                    <div style="text-align:center;">
                        <input style="padding:0px;" type="submit" value="', $txt['battle_query'], '" />
                    </div>
                </td>
            </tr>
            <tr class="windowbg2">
                <td colspan="3">
                    <input type="hidden" name="sc" value="', $sc, '" />
                </td>
            </tr>
            <tr class="windowbg2">
                <td colspan="3" class="error">
                    ', (!empty($b['b_message']) ? $b['b_message'] : ''), '
                </td>
            </tr>';
    }

    echo'
        </table>
    </form>';
    if (!empty($context['battle_warriors'][0]))
    {
        echo '
    <table class="bordercolor" border="0" cellpadding="4" cellspacing="1" width="100%">
        <tr class="titlebg">
            <td colspan="10" class="centertext largetext">', 'Warriors', '</td>
        </tr>';


        foreach ($context['battle_warriors'] as $key => $member)
        {
            $class = $key % 2 == 0 ? 'windowbg' : 'windowbg2';
            echo '
        <tr class="', $class, '">
            <td>
                <a href="', $member['href'], '">
                    ', $member['name'], '
                </a>
            </td>
            <td style="text-align:right;">
                <a style="text-decoration:none;" href="', $member['stats'], 'current_page=', (!empty($context['current_page']) ? $context['current_page']+1 : 1), '">
                    <img style="position:relative;padding: 0px 2px 0px 2px;width:15px;height:15px;" src="' . $settings['default_theme_url'] . '/images/battle/wrench.gif" title="' . $txt['battle_warrior_edit'] . '" alt="' . $txt['battle_edit'] . '" />
                </a>
            </td>
        </tr>';
        }


        echo '
	<tr class="catbg">
            <td style="font-weight:bold;line-height:3px;" colspan="2">
		&nbsp;
            </td>
	</tr>
    </table>', $context['battle_display']['page'];

        if ((int)$context['battle_display']['pages'] > 1)
            echo '
    <span style="position:absolute;width:99%;display:inline-block;left:90%;vertical-align:top;padding-top:3px;">
        <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=bmem;sort=id_member;current_page=', (int)$context['current_page']+1, ';order=', $context['battle_order'], '">
            <img alt="" src="', $settings['default_theme_url'], '/images/battle/battle-sort_id.png" title="', $txt['battle_sort_id'], '" class="windowbg" style="vertical-align:top;width:15px;height:15px;border:0px;" />
        </a>
        <img alt="" style="height:15px;width:1px;border:0px;" src="', $settings['default_theme_url'], '/images/battle/battle_vertical_bar.gif" />
        <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=bmem;sort=real_name;current_page=', (int)$context['current_page']+1, ';order=', $context['battle_order'], '">
            <img alt="" src="', $settings['default_theme_url'], '/images/battle/battle-sort_name.png" title="', $txt['battle_sort_name'], '" class="windowbg" style="vertical-align:top;width:15px;height:15px;border:0px;" />
        </a>
        <img alt="" style="height:15px;width:1px;border:0px;" src="', $settings['default_theme_url'], '/images/battle/battle_vertical_bar.gif" />
        <a style="text-decoration:none;" href="', $scripturl, '?action=admin;area=battle;sa=bmem;sort=', $context['battle_sort'], ';current_page=', (int)$context['current_page']+1, ';order=', ($context['battle_order'] == 'DESC' ? 'ASC' : 'DESC'), '">
            <img alt="" src="'.$settings['default_theme_url'].'/images/battle/battle-sort_dir.gif" title="'.$txt['battle_sort_dir'].'" class="windowbg" style="vertical-align:top;width:15px;height:15px;border:0px;" />
        </a>
    </span>';

    }
}
?>