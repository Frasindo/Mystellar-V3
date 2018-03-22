<?php

// Plugin : Add Captcha
// Author : Harshit Shrivastava

// Disallow direct access to this file for security reasons

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}
$plugins->add_hook("newthread_end", "captcha_newThread");
$plugins->add_hook("newthread_do_newthread_start", "captcha_donewThread");
$plugins->add_hook("editpost_end", "captcha_editPost");
$plugins->add_hook("editpost_do_editpost_start", "captcha_doeditPost");
$plugins->add_hook("newreply_end", "captcha_newReply");
$plugins->add_hook("newreply_do_newreply_start", "captcha_donewReply");
$plugins->add_hook("showthread_start", "captcha_quickReply");
$plugins->add_hook("private_send_end", "captcha_newMsg");
$plugins->add_hook("private_send_do_send", "captcha_donewMsg");
$plugins->add_hook("search_end", "captcha_search");
$plugins->add_hook("search_do_search_start", "captcha_dosearch");

function captcha_info()
{
	return array(
		"name"			=> "Add Captcha",
		"description"	=> "Add Captcha",
		"website"		=> "http://mybb.com",
		"author"		=> "Harshit Shrivastava",
		"authorsite"	=> "mailto:harshit_s21@rediffmail.com",
		"version"		=> "1.0",
		"compatibility" => "18*,16*"
	);
}
function captcha_validate($gid)
{
	global $mybb;
	if($mybb->settings['captcha_group'])
	{
		$gids = explode(",", $mybb->settings['captcha_group']);
		if(in_array($gid, $gids))
		{
			return True;
		}
	}
	return False;
}
function validateCount()
{
	global $mybb;
	if($mybb->user['uid'] == 0)
		return true;
	if($mybb->settings['captcha_countBased'] != "select")
	{
		if($mybb->settings['captcha_countBased'] == "postCount")  // यदि उपयोगकर्ता की पोस्ट निर्धारित की गई पोस्ट से कम है कपत्चा दिखाओ
		{
			if($mybb->user['postnum']<$mybb->settings['captcha_count'])
			{
				return True;
			}
			return False;
		}
		if($mybb->settings['captcha_countBased'] == "repCount")  // यदि उपयोगकर्ता की रेपुतटीओन निर्धारित की गई रेपुतटीओन से कम है कपत्चा दिखाओ
		{
			if($mybb->user['reputation']<$mybb->settings['captcha_count'])
			{
				return True;
			}
			return False;
		}
		if($mybb->settings['captcha_countBased'] == "timeCount")  // यदि उपयोगकर्ता की रेपुतटीओन निर्धारित की गई रेपुतटीओन से कम है कपत्चा दिखाओ
		{
			if($mybb->user['timeonline']<($mybb->settings['captcha_count']*3600))
			{
				return True;
			}
			return False;
		}
	}
	return True;
}

//नए कड़ी के समय कपत्चा दिखाओ
function captcha_newThread()
{
	global $mybb,$db, $lang, $captcha;
	if ($mybb->settings['captcha_show'] == 1 && $mybb->settings['captcha_post'] == 1 && captcha_validate($mybb->user['usergroup']) && validateCount())
	{
		require_once MYBB_ROOT.'inc/class_captcha.php';
		$post_captcha = new captcha(true, "post_captcha");

		if($post_captcha->html)
		{
			$captcha = $post_captcha->html;
		}
	}
}
function captcha_donewThread()
{
	global $mybb,$db, $lang, $captcha,$header,$thread_errors,$post_errors;
	if ($mybb->settings['captcha_show'] == 1 && $mybb->settings['captcha_post'] == 1 && captcha_validate($mybb->user['usergroup']) && validateCount())
	{		
		require_once MYBB_ROOT.'inc/class_captcha.php';
		$post_captcha = new captcha(false, "post_captcha");

		if($post_captcha->validate_captcha() == false)
		{
			// CAPTCHA validation failed
			foreach($post_captcha->get_errors() as $error)
			{
				error($error);
			}
		}
	}
}
function captcha_editPost()
{
	global $mybb,$db, $lang, $captcha,$pollbox;
	if ($mybb->settings['captcha_show'] == 1 && $mybb->settings['captcha_editpost'] == 1 && captcha_validate($mybb->user['usergroup']) && validateCount())
	{
		require_once MYBB_ROOT.'inc/class_captcha.php';
		$post_captcha = new captcha(true, "post_captcha");

		if($post_captcha->html)
		{
			$pollbox .= $post_captcha->html;
		}
	}
}
function captcha_doeditPost()
{
	global $mybb,$db, $lang, $captcha,$header,$post_errors;
	if ($mybb->settings['captcha_show'] == 1 && $mybb->settings['captcha_editpost'] == 1 && captcha_validate($mybb->user['usergroup']) && validateCount())
	{
		require_once MYBB_ROOT.'inc/class_captcha.php';
		$post_captcha = new captcha(false, "post_captcha");

		if($post_captcha->validate_captcha() == false)
		{
			// CAPTCHA validation failed
			foreach($post_captcha->get_errors() as $error)
			{
				error($error);
			}
		}
	}
}
function captcha_newReply()
{
	global $mybb,$db, $lang, $captcha;
	if ($mybb->settings['captcha_show'] == 1 && $mybb->settings['captcha_reply'] == 1 && captcha_validate($mybb->user['usergroup']) && validateCount())
	{
		require_once MYBB_ROOT.'inc/class_captcha.php';
		$post_captcha = new captcha(true, "post_captcha");

		if($post_captcha->html)
		{
			$captcha = $post_captcha->html;
		}
	}
}
function captcha_quickReply()
{
	global $mybb,$db, $lang, $plugincptca;
	if ($mybb->settings['captcha_show'] == 1 && $mybb->settings['captcha_reply'] == 1 && captcha_validate($mybb->user['usergroup']) && validateCount())
	{
		require_once MYBB_ROOT.'inc/class_captcha.php';
		$post_captcha = new captcha(true, "post_captcha");

		if($post_captcha->html)
		{
			$plugincptca = $post_captcha->html;
		}
	}
}
function captcha_donewReply()
{
	global $mybb,$db, $lang, $captcha,$header,$thread_errors,$post_errors;
	if ($mybb->settings['captcha_show'] == 1 && $mybb->settings['captcha_reply'] == 1 && captcha_validate($mybb->user['usergroup']) && validateCount())
	{		
		require_once MYBB_ROOT.'inc/class_captcha.php';
		$post_captcha = new captcha(false, "post_captcha");

		if($post_captcha->validate_captcha() == false)
		{
			// CAPTCHA validation failed
			foreach($post_captcha->get_errors() as $error)
			{
				error($error."<script>document.getElementById('quickreply_spinner').style.display='none';</script>");
			}
		}
	}
}
function captcha_newMsg()
{
	global $mybb,$db, $lang, $captcha,$private_send_tracking;
	if ($mybb->settings['captcha_show'] == 1 && $mybb->settings['captcha_message'] == 1 && captcha_validate($mybb->user['usergroup']) && validateCount())
	{
		require_once MYBB_ROOT.'inc/class_captcha.php';
		$login_captcha = new captcha(false, "post_captcha");

		if($login_captcha->type == 1)
		{
			if(!$correct)
			{
				$login_captcha->build_captcha();
			}
			else
			{
				$captcha = $login_captcha->build_hidden_captcha();
			}
		}
		if($login_captcha->html)
		{
			$private_send_tracking = $login_captcha->html;
		}
		
	}
}
function captcha_donewMsg()
{
	global $mybb,$db, $lang, $captcha,$post_errors;
	if ($mybb->settings['captcha_show'] == 1 && $mybb->settings['captcha_message'] == 1 && captcha_validate($mybb->user['usergroup']) && validateCount())
	{		
		require_once MYBB_ROOT.'inc/class_captcha.php';
		$post_captcha = new captcha(false, "post_captcha");

		if($post_captcha->validate_captcha() == false)
		{
			// CAPTCHA validation failed
			foreach($post_captcha->get_errors() as $error)
			{
				error($error);
			}
		}
	}
}
function captcha_search()
{
	global $mybb,$db, $lang, $captcha,$moderator_options,$srchlist;
	if ($mybb->settings['captcha_show'] == 1 && $mybb->settings['captcha_search'] == 1 && captcha_validate($mybb->user['usergroup']) && validateCount())
	{
		require_once MYBB_ROOT.'inc/class_captcha.php';
		$login_captcha = new captcha(false, "post_captcha");

		if($login_captcha->type == 1)
		{
			if(!$correct)
			{
				$login_captcha->build_captcha();
			}
			else
			{
				$captcha = $login_captcha->build_hidden_captcha();
			}
		}
		if($login_captcha->html)
		{
			$moderator_options .= $login_captcha->html;
		}
	}
}
function captcha_dosearch()
{
	global $mybb,$db, $lang, $captcha,$post_errors;
	if ($mybb->settings['captcha_show'] == 1 && $mybb->settings['captcha_search'] == 1 && captcha_validate($mybb->user['usergroup']) && validateCount())
	{		
		require_once MYBB_ROOT.'inc/class_captcha.php';
		$post_captcha = new captcha(false, "post_captcha");

		if($post_captcha->validate_captcha() == false)
		{
			// CAPTCHA validation failed
			foreach($post_captcha->get_errors() as $error)
			{
				error($error);
			}
		}
	}
}
function captcha_activate()
{
global $db, $mybb;
$captcha_group = array(
        'gid'    => 'NULL',
        'name'  => 'captcha',
        'title'      => 'Add Catcha',
        'description'    => 'Add captcha',
        'disporder'    => "1",
        'isdefault'  => "0",
    ); 
$db->insert_query('settinggroups', $captcha_group);
$gid = $db->insert_id(); 
// Enable / Disable
$captcha_setting1 = array(
        'sid'            => 'NULL',
        'name'        => 'captcha_show',
        'title'            => 'Enable this plugin',
        'description'    => 'If you set this option to yes, this plugin will add captcha to your pages.',
        'optionscode'    => 'yesno',
        'value'        => '1',
        'disporder'        => 1,
        'gid'            => intval($gid),
    );
	
$captcha_setting2 = array(
        'sid'            => 'NULL',
        'name'        => 'captcha_post',
        'title'            => 'Put captcha for new post',
        'description'    => 'It will show captcha while creating new thread',
        'optionscode'    => 'yesno',
        'value'        => '1',
        'disporder'        => 2,
        'gid'            => intval($gid),
    );
$captcha_setting3 = array(
        'sid'            => 'NULL',
        'name'        => 'captcha_reply',
        'title'            => 'Put captcha for new reply',
        'description'    => 'It will show captcha while creating new replies',
        'optionscode'    => 'yesno',
        'value'        => '1',
        'disporder'        => 3,
        'gid'            => intval($gid),
    );
$captcha_setting4 = array(
        'sid'            => 'NULL',
        'name'        => 'captcha_message',
        'title'            => 'Put captcha for new private message',
        'description'    => 'It will show captcha while creating new private messages',
        'optionscode'    => 'yesno',
        'value'        => '1',
        'disporder'        => 4,
        'gid'            => intval($gid),
    );
	$captcha_setting5 = array(
        'sid'            => 'NULL',
        'name'        => 'captcha_editpost',
        'title'            => 'Put captcha when editing post',
        'description'    => 'It will show captcha while editing post',
        'optionscode'    => 'yesno',
        'value'        => '0',
        'disporder'        => 5,
        'gid'            => intval($gid),
    );
	$captcha_setting6 = array(
        'sid'            => 'NULL',
        'name'        => 'captcha_search',
        'title'            => 'Put captcha when searching',
        'description'    => 'It will show captcha while searching',
        'optionscode'    => 'yesno',
        'value'        => '1',
        'disporder'        => 6,
        'gid'            => intval($gid),
    );
	$captcha_setting7 = array(
        'sid'            => 'NULL',
        'name'        => 'captcha_group',
        'title'            => 'Enter group IDs',
        'description'    => 'Enter the comma separated group ID for which captcha to be shown',
        'optionscode'    => 'text',
        'value'        => '1',
        'disporder'        => 7,
        'gid'            => intval($gid),
    );
	$captcha_setting8 = array(
        'sid'            => 'NULL',
        'name'        => 'captcha_countBased',
        'title'            => 'Enter captcha criteria',
        'description'    => 'Enter the number below which captcha to be shown. Enter 0 to disable it.',
        'optionscode'    => 'select
select=Select
postCount=Post Count
repCount=Reputation Count
timeCount=Time Online',
        'value'        => 'postCount',
        'disporder'        => 8,
        'gid'            => intval($gid),
    );
	$captcha_setting9 = array(
        'sid'            => 'NULL',
        'name'        => 'captcha_count',
        'title'            => 'Enter Number',
        'description'    => 'Enter the count below which captcha to be shown for the above criteria.(Time in hours)',
        'optionscode'    => 'text',
        'value'        => '10',
        'disporder'        => 9,
        'gid'            => intval($gid),
    );
	$db->insert_query('settings', $captcha_setting1);
	$db->insert_query('settings', $captcha_setting2);
	$db->insert_query('settings', $captcha_setting3);
	$db->insert_query('settings', $captcha_setting4);
	$db->insert_query('settings', $captcha_setting5);
	$db->insert_query('settings', $captcha_setting6);
	$db->insert_query('settings', $captcha_setting7);
	$db->insert_query('settings', $captcha_setting8);
	$db->insert_query('settings', $captcha_setting9);
rebuild_settings();
require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
find_replace_templatesets("showthread_quickreply", "#".preg_quote('captcha') . "#i", 'plugincptca');
}
function captcha_deactivate()
{
  global $db;
  $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name = 'captcha_show'");
  $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name = 'captcha_post'");
  $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name = 'captcha_reply'");
  $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name = 'captcha_message'");
  $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name = 'captcha_editpost'");
  $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name = 'captcha_search'");
  $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name = 'captcha_group'");
  $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name = 'captcha_countBased'");
  $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name = 'captcha_count'");
  $db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='captcha'");
  rebuild_settings();
  require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
  find_replace_templatesets("showthread_quickreply", "#".preg_quote('plugincptca') . "#i", 'captcha');
}
?>
