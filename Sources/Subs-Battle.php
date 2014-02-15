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

function battle_pagination($content, $count = 20)
{
	/* PHP pagination - max 7 visible integers and 6 periods (all links) - current page encircled with square brackets
	 * This php pagination code was developed by Underdog copyright 2013
	 * http://webdevelop.comli.com
	 * Licensed under the GNU Public License: http://www.gnu.org/licenses/gpl.html
	*/

	// This particular function is only used when opting an entire table else it is not necessary
	global $context;

	/*  Set the $context variables for the display template  */
	$context['current_count'] = count($content);
	$context['current_pages'] = (($context['current_count']) / $count) + 1;

	if (count($content) <= $count || $count < 2)
	{
		$context['current_pages'] = 0;
		return $content;
	}

	if (($context['current_count'] / $count) == (int)($context['current_count'] / $count))
		$context['current_pages'] = ($context['current_count'] / $count);

	$context['current_showResults'] = array(((int)$context['current_page'] * $count), (((int)$context['current_page'] + 1) * $count) - 1);




	if ((int)$context['current_page']+1 == (int)$context['current_pages'])
	    $context['current_showResults'][1] = count($content);
	else
	    $context['current_showResults'][1] = (int)$context['current_page']*$count + ($count-1);

	foreach($content as $key => $var)
	{
		if ((int)$key >= (int)$context['current_showResults'][0] && (int)$key <= (int)$context['current_showResults'][1])
			$new_content[] = $var;
	}

	if (!empty($new_content))
		$context['current_showResults'][1] = ((int)$context['current_page']*$count) + count($new_content);
	else
		$new_content = $content;

	return $new_content;
}

function battle_pages($lang, $anchor, $link, $pages, $sort=false, $order=false)
{
	/* PHP pagination - max 7 visible integers and 6 periods (all links) - current page encircled with square brackets
	 * This php pagination code was developed by Underdog copyright 2013
	 * http://webdevelop.comli.com
	 * Licensed under the GNU Public License: http://www.gnu.org/licenses/gpl.html
	*/
	global $context, $txt, $scripturl;

	$pageCount = 1;
	$display = array('page' => false, 'pages' => '0');
	$page = !empty($context['current_page']) ? (int)$context['current_page'] : 0;
	$display['pages'] = !empty($pages) ? (int)$pages : 1;

	if ($display['pages'] > 1)
	{
            $display['page'] =  '
    <script type="text/javascript"><!-- // --><![CDATA[
        function changeColor(s)
	{
                document.getElementById("link"+s).style.color = "red";
	}
	function changeColorBack(s)
	{
		document.getElementById("link"+s).style.color = "blue";
	}
    // ]]></script>
    <span style="text-align:center;position:relative;width:99%;display:inline-block;">
        ' . $lang . '<br />';

            while ($pageCount < (int)$display['pages']+1)
            {
		$current_page = (int)$page+1;
		$total = (int)$display['pages'];

		if ($pageCount == 1 || $pageCount == $total || $pageCount == $current_page || $pageCount == $current_page+1 ||
		    $pageCount == $current_page+2 || $pageCount == $current_page-1 || $pageCount == $current_page-2)
		{
                    if ((int)$pageCount == (int)$page+1)
			$display['page'] .= '
        <a onclick="this.href=\'javascript: void(0)\';" onmouseout="changeColor('. $pageCount . ')" onmouseover="changeColorBack(' . $pageCount . ')" id="link' . $pageCount . '" style="color:red;text-decoration:none;" href="'. $link . ';current_page=' . $pageCount . ';' . $sort . $order . $anchor . '">[' . $pageCount . ']</a> ';
                    else
			$display['page'] .= '
        <a onmouseout="changeColorBack(' . $pageCount . ')" onmouseover="changeColor(' . $pageCount . ')" id="link' . $pageCount . '" style="color:blue;text-decoration:none;" href="' . $link . ';current_page=' . $pageCount . ';' . $sort . $order . $anchor . '">' . $pageCount . '</a> ';
		}
		elseif ($pageCount < $current_page-2 && $pageCount > $current_page-6)
			$display['page'] .= '
        <a onmouseout="changeColorBack(' . $pageCount . ')" onmouseover="changeColor(' . $pageCount . ')" id="link' . $pageCount . '" style="color:blue;text-decoration:none;" href="' . $link . ';current_page=' . $pageCount . ';' . $sort . $order . $anchor . '">.</a> ';
		elseif ($pageCount > $current_page+2 && $pageCount < $current_page+6)
			$display['page'] .= '
        <a onmouseout="changeColorBack(' . $pageCount . ')" onmouseover="changeColor(' . $pageCount . ')" id="link' . $pageCount . '" style="color:blue;text-decoration:none;" href="' . $link . ';current_page=' . $pageCount . ';' . $sort . $order . $anchor . '">.</a> ';

                $pageCount++;
            }

            $display['page'] .= '
    </span>';
	}

	// set the cookie for the redirects
	if ((!empty($page)) && ($page+1) > 0 && $page < $display['pages'])
		setcookie('battle_page', $_REQUEST['current_page']+1, time()+3600, '/');
	elseif ((!empty($page)) && ($page+1) > 0 && $page >= $display['pages'])
		setcookie('battle_page', $display['pages'], time()+3600, '/');
	else
		setcookie('battle_page', 0, time()+3600, '/');

	return $display;
}

function battle_didyouknow()
{
	// Do not bother loading/executing files for those without permission
	if (!allowedTo('view_battle'))
		return;

	//total quest
	battle_did_you_get('totalquest','battle_quest','totalquest','tot_quest','');

	//total monsters
	battle_did_you_get('totalmon','battle_monsters','totalmon','tot_mon','');

	//total shop items
	battle_did_you_get('totalshop','battle_shop','totalshop','tot_shop','');

	//total users in grave yard
	battle_did_you_get('totalgrave','battle_graveyard','totalgrave','tot_grave','');

	//total monster slays
	battle_did_you_get('total','members','mon_slays','mon_slays',' SUM(mon_slays) AS mon_slays,');

	//total member slays
	battle_did_you_get('total','members','mem_slays','mem_slays',' SUM(mem_slays) AS mem_slays,');

}

function battle_did_you_get($colm,$table,$tott,$variable,$sum_as)
{
	// Do not bother loading/executing files for those without permission
	if (!allowedTo('view_battle'))
		return;

	global $scripturl, $context, $smcFunc;

	$did = !empty($_COOKIE['battle_' . $variable]) ? $_COOKIE['battle_' . $variable] : false;

	if (!empty($did))
	{
		$context[$variable] = $did;
		return;
	}

	$result =  $smcFunc['db_query']('', '
			SELECT {raw:sum}
			COUNT(*) as {raw:col}
			FROM {db_prefix}{raw:table}',
			array(
			'col' => $colm,
			'table' => $table,
			'sum' => $sum_as,
			)
		);

	$row = $smcFunc['db_fetch_assoc']($result);
	$tott = $row[$tott];
	$smcFunc['db_free_result']($result);
	$context[$variable] = $tott;
	setcookie('battle_' . $variable, $tott, time()+3600, '/');
}

function battle_search()
{
	global  $context, $user_info, $txt, $modSettings, $smcFunc;

	$context['sub_template']  = 'battle_search';
	$context['page_title'] = $txt['battle_expl'];

	if(isset($_GET['open']))
	{
		// This is just here for now to pass the data.
		explore_custom();
    	}
	else
	{
		// New battle search. Needed it badly, couldn't tell first attack from monsters or actions.
		$rand = mt_rand(1, 2); // Well lets not count out the random
		if ($rand == 1)
			$check = explore_custom_init();
		else
			$check = explore_monster_init();

		if (!$check)
			explore_custom_init();
	}
}

function explore_custom_init($check = false)
{
	global  $context, $user_info, $txt, $modSettings, $smcFunc;

	$context['isbattle_action'] = true;
	$request = $smcFunc['db_query']('', '
		SELECT m.id_explore, m.start
		FROM {db_prefix}battle_explore AS m
		ORDER BY RAND()
		LIMIT 1',
		array()
	);

	// Loop through all results
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		// And add them to the list
		$context['battle_explore'][] = array(
			'id_explore' => $row['id_explore'],
			'start' => html_entity_decode($row['start']),
		);

		$check = true;
	}
	$smcFunc['db_free_result']($request);

	$context['battle']['customSessionId'] = 9 * ((!empty($user_info['stamina']) ? $user_info['stamina'] : 9) + (!empty($user_info['exp']) ? $user_info['exp'] : 9) + (!empty($user_info[$modSettings['bcash']]) ? $user_info[$modSettings['bcash']] : 9) + (!empty($context['battle_explore'][0]['id_explore']) ? $context['battle_explore'][0]['id_explore'] : 9) + (!empty($user_info['hp']) ? $user_info['hp'] : 9));
	return $check;
}

function explore_monster_init($where = false, $check = false)
{
	global  $context, $user_info, $txt, $modSettings, $smcFunc;
	$level = !empty($user_info['level']) ? (int)$user_info['level'] : 0;

	if (empty($modSettings['enable_battle_range']))
		$where = ' AND m.mon_range <= ' . $level . ' AND m.mon_max_range >= ' . $level;

		$request = $smcFunc['db_query']('', "
		SELECT m.id_monster, m.name, m.atk, m.def, m.hp,m.img, e.id_explore,
		e.start, m.mon_range, m.mon_max_range, evolve, f.monster_hp
		FROM {db_prefix}battle_monsters AS m
		INNER JOIN {db_prefix}battle_explore AS e
		LEFT JOIN {db_prefix}battle_monsters_fight AS f ON (f.id_monster = m.id_monster AND f.id_warrior = {int:user_id})
		WHERE hp <> 0 {$where}
		ORDER BY RAND()
		LIMIT 1",
		array('user_id' => $user_info['id'])
	);

	// Loop through all results
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		// And add them to the list
		$context['battle_explore'][] = array(
			'id_monster' => $row['id_monster'],
			'name' => $row['name'],
			'mon_range' => !empty($row['mon_range']) ? (int)$row['mon_range'] : 0,
			'mon_max_range' => !empty($row['mon_max_range']) ? $row['mon_max_range'] : ((!empty($row['mon_range']) ? (int)$row['mon_range'] : 0) + 1),
			'atk' => $row['atk'],
			'def' => $row['def'],
			'hp' => $row['hp'],
			'mon_hp' => !empty($row['monster_hp']) ? $row['monster_hp'] : $row['hp'],
			'img' => $row['img'],
			'evolve' => !empty($row['evolve']) ? (int)$row['evolve'] : 0
		);

		$check = true;
	}
	$smcFunc['db_free_result']($request);
	$key = key(array_slice($context['battle_explore'], -1, 1, TRUE));

	// First Attack !!!
	if ($check)
	{
		$this_qfirstatk = mt_rand (0, 1);
		if ($this_qfirstatk == 1 && $user_info['level'] >= $context['battle_explore'][$key]['mon_range'] && $user_info['level'] <= $context['battle_explore'][$key]['mon_max_range'])
		{
			$this_firstatk = mt_rand(0, 25);
			if ($this_firstatk > 0)
			{
				$context['battle']['firstatk'] = $this_firstatk;
				$user_info['hp'] = $user_info['hp'] - $this_firstatk;
				if($user_info['hp'] < 0)
				{
					$user_info['hp'] = 0;
					$context['battle']['firstdead'] = true;
				}
				updateMemberData($user_info['id'], array('hp' => $user_info['hp']));
			}
		}

		$context['battle']['checkSession'] = 9 * ((!empty($user_info['stamina']) ? $user_info['stamina'] : 9) + (!empty($user_info['exp']) ? $user_info['exp'] : 9) + (!empty($context['battle_explore'][$key]['id_monster']) ? $context['battle_explore'][$key]['id_monster'] : 9) + (!empty($context['battle_explore'][$key]['mon_hp']) ? $context['battle_explore'][$key]['mon_hp'] : 9) + (!empty($user_info['hp']) ? $user_info['hp'] : 9));
	}

	return $check;
}

function explore_custom()
{
	global  $context, $user_info, $txt, $modSettings, $smcFunc;

	$context['battle_explore'] = array();
	$_GET['open'] = !empty($_GET['open']) ? (int) $_GET['open'] : 0;
	$session = !empty($_REQUEST['session']) ? $_REQUEST['session'] : false;
	$num = mt_rand(1, 2);
	$context['battle_ErrorBack']['change'] = $context['battle_ErrorBack']['explore'];
	$checkSession = 9 * ((!empty($user_info[$modSettings['bcash']]) ? $user_info[$modSettings['bcash']] : 9) + (!empty($user_info['stamina']) ? $user_info['stamina'] : 9) + (!empty($user_info['exp']) ? $user_info['exp'] : 9) + (!empty($_GET['open']) ? $_GET['open'] : 9) + (!empty($user_info['hp']) ? $user_info['hp'] : 9));
	if ($session !== $context['session_id'] . $checkSession)
		fatal_error($txt['battle_cheatrefresh'], false);

	if($num == 1)
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_explore, outcome1, outcome1_reward, outcome1_action, outcome2, outcome2_reward, outcome2_action, start,price
			FROM {db_prefix}battle_explore
			WHERE id_explore = {int:do} AND {int:cash} >= price
			LIMIT 1',
			array(
				'do' => $_GET['open'],
				'cash' => !empty($user_info[$modSettings['bcash']]) ? (int)$user_info[$modSettings['bcash']] : 0,
			)
		);
		// Loop through all results
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			// And add them to the list
			$context['battle_explore'][] = array(
				'id_explore' => $row['id_explore'],
				'outcome1' => html_entity_decode($row['outcome1']),
				'outcome1_reward' => $row['outcome1_reward'],
				'outcome2' => html_entity_decode($row['outcome2']),
				'outcome2_reward' => $row['outcome2_reward'],
				'outcome2_action' => $row['outcome2_action'],
				'outcome1_action' => $row['outcome1_action'],
				'price' => $row['price']
			);
		}
		$smcFunc['db_free_result']($request);

		foreach ($context['battle_explore'] as $row)
		{
			if ($user_info[$modSettings['bcash']] - $row['price'] < 0)
				fatal_error($txt['battle_error14'], false);

			//check the outcome action if gold make it $modSettings['bcash']
			if($row['outcome1_action'] === 'gold')
				$row['outcome1_action'] = $modSettings['bcash'];

			if($row['outcome1_action'] === $modSettings['bcash'])
			{
				$user_info[$modSettings['bcash']] =  $user_info[$modSettings['bcash']] - $row['price'] < 1 ? 0 : $user_info[$modSettings['bcash']] - $row['price'];
				$user_info[$row['outcome1_action']] = $user_info[$row['outcome1_action']] + $row['outcome1_reward'];
				updateMemberData($user_info['id'], array($row['outcome1_action'] => $user_info[$row['outcome1_action']], $modSettings['bcash'] => $user_info[$modSettings['bcash']]));

				if($modSettings['enable_battle_hist'])
				{
					$content = $user_info['name'] . ' ' . $txt['battle_hist_ex1'] . ' ' . $row['outcome1_reward'] . ' ' . $row['outcome1_action'];
					add_to_battle_hist($content);
				}
				$context['battleExplore']['customAction'] = $row['outcome1'];
			}
			else
			{
				$user_info[$modSettings['bcash']] =  $user_info[$modSettings['bcash']] - $row['price'] < 1 ? 0 : $user_info[$modSettings['bcash']] - $row['price'];
				$gain = $user_info[$row['outcome1_action']] + $row['outcome1_reward'];
				$user_info[$row['outcome1_action']] = $gain > $user_info['max_'.$row['outcome1_action']] ? $user_info['max_'.$row['outcome1_action']] : $gain;

				updateMemberData($user_info['id'], array($row['outcome1_action'] => $user_info[$row['outcome1_action']], $modSettings['bcash'] => $user_info[$modSettings['bcash']]));

				if($modSettings['enable_battle_hist'])
				{
					$currency = !empty($modSettings['battle_cash']) ? $modSettings['battle_cash'] : 'gold';
					$content = $user_info['name'] . ' ' . $txt['battle_hist_ex1'] . ' ' . ($row['outcome1_reward'] === 'gold' ? $currency : $row['outcome1_reward']) . ' ' . $row['outcome1_action'];
					add_to_battle_hist($content);
				}
				$context['battleExplore']['customAction'] = $row['outcome1'];
			}
		}
	}
	else
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_explore, outcome1, outcome1_reward, outcome1_action, outcome2, outcome2_reward, outcome2_action, start,price
			FROM {db_prefix}battle_explore
			WHERE id_explore = {int:do} AND {int:cash} >= price
			LIMIT 1',
			array(
				'do' => $_GET['open'],
				'cash' => !empty($user_info[$modSettings['bcash']]) ? (int)$user_info[$modSettings['bcash']] : 0,
			)
		);

		// Loop through all results
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			// And add them to the list
			$context['battle_explore'][] = array(
				'id_explore' => $row['id_explore'],
				'outcome1' => html_entity_decode($row['outcome1']),
				'outcome1_reward' => $row['outcome1_reward'],
				'outcome2' => html_entity_decode($row['outcome2']),
				'outcome2_reward' => $row['outcome2_reward'],
				'outcome2_action' => $row['outcome2_action'],
				'outcome1_action' => $row['outcome1_action'],
				'price' => $row['price']
			);
		}

		$smcFunc['db_free_result']($request);
		foreach ($context['battle_explore'] as $row)
		{
			if ($user_info[$modSettings['bcash']] - $row['price'] < 0)
				fatal_error($txt['battle_error14'], false);

			//check the outcome action if gold make it $modSettings['bcash']
			if($row['outcome2_action'] == 'gold')
				$row['outcome2_action'] = $modSettings['bcash'];

			if($row['outcome2_action'] === $modSettings['bcash'])
			{
				$user_info[$modSettings['bcash']] =  $user_info[$modSettings['bcash']] - $row['price'] < 1 ? 0 : $user_info[$modSettings['bcash']] - $row['price'];
				$user_info[$row['outcome2_action']] = $user_info[$row['outcome2_action']] + $row['outcome2_reward'];
				updateMemberData($user_info['id'], array($row['outcome2_action'] => $user_info[$row['outcome2_action']], $modSettings['bcash'] => $user_info[$modSettings['bcash']]));

				if($modSettings['enable_battle_hist'])
				{
					$content = $user_info['name'] . ' ' . $txt['battle_hist_ex1'] . ' ' . $row['outcome2_reward'] . ' ' . $row['outcome2_action'];
					add_to_battle_hist($content);
				}
				$context['battleExplore']['customAction'] = $row['outcome2'];
			}
			else
			{
				$user_info[$modSettings['bcash']] =  $user_info[$modSettings['bcash']] - $row['price'] < 1 ? 0 : $user_info[$modSettings['bcash']] - $row['price'];
				$gain = $user_info[$row['outcome2_action']] + $row['outcome2_reward'];
				$user_info[$row['outcome2_action']] = $gain > $user_info['max_'.$row['outcome2_action']] ? $user_info['max_'.$row['outcome2_action']] : $gain;
				updateMemberData($user_info['id'], array($row['outcome2_action'] => $user_info[$row['outcome2_action']], $modSettings['bcash'] => $user_info[$modSettings['bcash']]));

				if($modSettings['enable_battle_hist'])
				{
					$currency = !empty($modSettings['battle_cash']) ? $modSettings['battle_cash'] : 'gold';
					$content = $user_info['name'] . ' ' . $txt['battle_hist_ex1'] . ' ' . ($row['outcome2_reward'] === 'gold' ? $currency : $row['outcome2_reward']) . ' ' . $row['outcome2_action'];
					add_to_battle_hist($content);
				}
				$context['battleExplore']['customAction'] = $row['outcome2'];
			}
		}
	}

	if (empty($context['battle_explore']))
		fatal_error($txt['battle_error14'], false);

	$context['battle']['customSessionId'] = 9 * ((!empty($user_info[$modSettings['bcash']]) ? $user_info[$modSettings['bcash']] : 9) + (!empty($user_info['stamina']) ? $user_info['stamina'] : 9) + (!empty($user_info['exp']) ? $user_info['exp'] : 9) + (!empty($_GET['open']) ? $_GET['open'] : 9) + (!empty($user_info['hp']) ? $user_info['hp'] : 9));
}

function battle_stat_do_upgrade($action,$amount,$amount1,$uaction)
{
    global $txt, $context, $smcFunc, $modSettings, $user_info;

    $outcomes = array(
		'max_atk' => 'battle_hist13',
		'max_atk5' => 'battle_hist13',
		'max_energy' => 'battle_hist15',
		'max_energy5' => 'battle_hist15',
		'max_stamina' => 'battle_hist16',
		'max_stamina5' => 'battle_hist16',
		'max_def' => 'battle_hist14',
		'max_def5' => 'battle_hist14',
		'max_hp' => 'battle_hist17',
		'max_hp5' => 'battle_hist17'
	);

    if (isset($_REQUEST[$action]))
    {
	if (!empty($user_info['stat_point']))
	{
	    checkSession('get');

		foreach ($outcomes as $key => $action)
		{
			if ($uaction === $key)
				$content = $user_info['name'] . '&nbsp;' . $txt[$outcomes[$key]];
		}

		if ($user_info['stat_point'] - $amount1 > -1)
		{
			if($modSettings['enable_battle_hist'])
				add_to_battle_hist($content);

			$uac = $user_info[$uaction] + $amount;
			$user_info['stat_point'] = $user_info['stat_point'] - $amount1;
			$user_info['stat_point'] = (int)$user_info['stat_point'] > 0 ? (int)$user_info['stat_point'] : 0;
			updateMemberData($user_info['id'], array('stat_point' => $user_info['stat_point'], $uaction => $uac));
			redirectexit('action=battle;sa=upgrade;done;#battle_main');
		}
		else
			fatal_error($txt['battle_error13'], false);
	}
	else
	    fatal_error($txt['battle_cheatrefresh'], false);
    }

}

function battle_shop_do_buy()
{
    global  $smcFunc, $txt, $scripturl, $modSettings, $user_info, $context;

    if(isset($_GET['buy']))
    {
	$battleQ = $smcFunc['db_query']('', '
		SELECT id_item, name, price, action, img, description, amount
		FROM {db_prefix}battle_shop
		WHERE id_item = {int:item}
		LIMIT 1',
			array('item' => $_GET['buy'],
		    )
		);

	while ($row = $smcFunc['db_fetch_assoc']($battleQ))
        {
	    if ($row['action'] == 'hp')
	    {
		$request = $smcFunc['db_query']('','
			SELECT id_memdef
			FROM {db_prefix}battle_graveyard
			WHERE  id_memdef = {int:mem}
			LIMIT 1',
				array('mem' => $user_info['id'],)
		   );

		list ($member) = $smcFunc['db_fetch_row']($request);

		$smcFunc['db_free_result']($request);

		if ($member)
		{
		    $smcFunc['db_query']('','
			DELETE FROM {db_prefix}battle_graveyard
			WHERE id_memdef = {int:id}',
				array('id' => $user_info['id'],
				)
			);

		    updateMemberData($user_info['id'], array('is_dead' => 0));
		}
	    }

	    // ensure it is numeric, eliminate decimals and it is positive
	    $_POST['pamount'] = !empty($_POST['pamount']) ? abs((int)round($_POST['pamount'])) : 0;
	    $price = $row['price'] * $_POST['pamount'];
	    if (($user_info[$modSettings['bcash']] >= $price))
	    {
		$row['amount'] = $row['amount'] * $_POST['pamount'];
		if($modSettings['enable_battle_hist'])
		{
		    $price = $row['price'] * $_POST['pamount'];
		    $content = $user_info['name'].' '.$txt['battle_hist18'].' '.$_POST['pamount'].' '.$row['name'].' '.$txt['battle_hist9'].' '.$price.' '.$txt['battle_gold'];
		    add_to_battle_hist($content);
		}

		$action =  $user_info[$row['action']] + $row['amount'];
		if ($action > $user_info['max_'.$row['action']])
		    $action = $user_info['max_'.$row['action']];

		$price = $user_info[$modSettings['bcash']] - $row['price'] * $_POST['pamount'];
		updateMemberData($user_info['id'], array($modSettings['bcash'] => $price, $row['action'] => $action));
		$user_info[$modSettings['bcash']] = $price;
		$user_info[$row['action']] = $action;
		redirectexit('action=battle;sa=shop;done;#battle_main');
	    }
	    else
		fatal_error($txt['battle_heal_no_gold2'].' '.$_POST['pamount'].' '.$row['name'].' @ '.$price.' '.$txt['battle_gold']);
	}
    }
}

function battle_get_stats($cname, $attribute)
{
	global $smcFunc, $context, $txt, $max_views, $modSettings, $scripturl;

	$result =  $smcFunc['db_query']('', '
		SELECT {raw:cname}, real_name, id_member
		FROM {db_prefix}members
		WHERE {raw:cname} <> 0
		ORDER BY {raw:cname} DESC, real_name
		LIMIT 10',
		array(
			'cname' => $cname,
		)
	);

	$context[$attribute] = array();
	$max_views = 1;

	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		$context[$attribute][] = array(
			'id_member' => $row['id_member'],
			'real_name' => $row['real_name'],
			$cname => $row[$cname]
		);

		if ($max_views < $row[$cname])
			$max_views = $row[$cname];
	}

	$smcFunc['db_free_result']($result);

}

function getmonsterslEntry($reset = false)
{
    global $smcFunc, $request;

    if ($request == false)
        return false;

    if (!($row = $smcFunc['db_fetch_assoc']($request)))
        return false;

    $output = array(
	'id_monster' => $row['id_monster'],
	'name' => $row['name'],
	'atk' => $row['atk'],
	'def' => $row['def'],
	'hp' => $row['hp'],
	'img' => $row['img'],
	'max_hp' => $row['max_hp'],
	'mon_range' => !empty($row['mon_range']) ? (int)$row['mon_range'] : 1,
	'mon_max_range' => !empty($row['mon_max_range']) ? $row['mon_max_range'] : ((!empty($row['mon_range']) ? (int)$row['mon_range'] : 1) + 1),
	'counter' => !empty($row['counter']) ? (int)$row['counter'] : 0
	);

    return $output;
}

function battle_whos_online_func($func_method = 'echo')
{
   global $sourcedir;

      require_once($sourcedir . '/Subs-MembersOnline.php');
      $members = array(
		'show_hidden' => allowedTo('moderate_forum'),
		'sort' => 'log_time',
		'reverse_sort' => true,
	 );

   $get = getMembersOnlineStats($members);

   if ($func_method !== 'echo')
      return $get + array('users' => $get['users_online']);
}

function battle_histlist()
{
	global $smcFunc, $context, $scripturl;
	if(isset($_REQUEST['del']))
	{
		// Do we have permission to moderate battle?
		isAllowedTo('battle_shouts_mod');
		$id = (int)$_REQUEST['del'];
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}battle_history
			WHERE id_hist = {int:ids}',
			array('ids' => $id,)
		);

		redirectexit('action=battle;#battle_main');
	}
}

function add_to_battle_hist($content)
{
	global $user_info, $smcFunc;
	$t = time();
	$smcFunc['db_insert']('',
		'{db_prefix}battle_history',
		array('content' => 'string', 'time' => 'string'),
		array($content, $t),
		array()
	);
}

function battle_campaign_score($userId = 0, $score = 0)
{
    global $smcFunc;

    for($i=1;$i<25;$i++)
    {
	if (battle_check_table_exists('battle_campaign_' . $i))
	{
	    $request = $smcFunc['db_query']('',"
		    SELECT score
		    FROM {db_prefix}{raw:camp}
		    WHERE id_warrior = {int:member}
		    LIMIT 1",
		    array('member' => $userId, 'camp' => 'battle_campaign_' . $i)
	    );

	    while ($row = $smcFunc['db_fetch_assoc']($request))
		$score = $score + (!empty($row['score']) ? $row['score'] : 0);

	    $smcFunc['db_free_result']($request);
	}
    }

    return $score;
}

function battle_fetchMembergroups($group_id = 0)
{
	global $smcFunc;

	$request = $smcFunc['db_query']('', '
		SELECT id_member
		FROM {db_prefix}members
		WHERE id_group = {int:id_group}
		OR id_post_group = {int:id_group}
		OR FIND_IN_SET({int:id_group}, additional_groups)
		ORDER BY real_name',
		array('id_group' => $group_id)
	);

	$members = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$members[] = $row['id_member'];
	$smcFunc['db_free_result']($request);

	if (empty($members))
		return array();

	return $members;
}
?>