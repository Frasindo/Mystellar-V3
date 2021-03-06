<?php

defined('IN_MOBIQUO') or exit;

require_once MYBB_ROOT."inc/functions_post.php";
require_once MYBB_ROOT."inc/functions_upload.php";
function get_raw_post_func($xmlrpc_params)
{
	global $db, $lang, $theme, $plugins, $mybb, $session, $settings, $cache, $time, $mybbgroups;
	require_once MYBB_ROOT.$mybb->settings['tapatalk_directory'].'/emoji/emoji.class.php';
	$lang->load("editpost");

	$input = Tapatalk_Input::filterXmlInput(array(
		'post_id' => Tapatalk_Input::INT,
	), $xmlrpc_params);

	// No permission for guests
	if(!$mybb->user['uid'])
	{
		return tt_no_permission();
	}

	// Get post info
	$pid = $input['post_id'];

	$query = $db->simple_select("posts", "*", "pid='$pid'");
	$post = $db->fetch_array($query);

	if(!$post['pid'])
	{
		return xmlrespfalse($lang->error_invalidpost);
	}

	// Get thread info
	$tid = $post['tid'];
	$thread = get_thread($tid);

	if(!$thread['tid'])
	{
		return xmlrespfalse($lang->error_invalidthread);
	}

	$thread['subject'] = htmlspecialchars_uni($thread['subject']);

	// Get forum info
	$fid = $post['fid'];
	$forum = get_forum($fid);
	if(!$forum || $forum['type'] != "f")
	{
		return xmlrespfalse($lang->error_closedinvalidforum);
	}
	if($forum['open'] == 0 || $mybb->user['suspendposting'] == 1)
	{
		return tt_no_permission();
	}

	$forumpermissions = forum_permissions($fid);

	if(!is_moderator($fid, "caneditposts"))
	{
		if($thread['closed'] == 1)
		{
			return xmlrespfalse($lang->redirect_threadclosed);
		}
		if($forumpermissions['caneditposts'] == 0)
		{
			return tt_no_permission();
		}
		if($mybb->user['uid'] != $post['uid'])
		{
			return tt_no_permission();
		}
		// Edit time limit
		$time = TIME_NOW;
		if($mybb->settings['edittimelimit'] != 0 && $post['dateline'] < ($time-($mybb->settings['edittimelimit']*60)))
		{
			$lang->edit_time_limit = $lang->sprintf($lang->edit_time_limit, $mybb->settings['edittimelimit']);
			return xmlrespfalse($lang->edit_time_limit);
		}
	}

	// Check if this forum is password protected and we have a valid password
	tt_check_forum_password($forum['fid']);

	if($forumpermissions['canpostattachments'] != 0)
	{ // Get a listing of the current attachments, if there are any
		$attachcount = 0;
		global $attachcache;
		$query = $db->simple_select("attachments", "*", "pid='{$pid}'");
		$attachments = '';
		while($attachment = $db->fetch_array($query))
		{
			$attachcache[$attachment['pid']][$attachment['aid']] = $attachment;
			$attachcount++;
		}
	}
	$attachment_list = array();
	$inline_attachment_list = array();
	if($attachcount)
	{
		process_post_attachments($post['pid'], $post, true, $attachment_list, $inline_attachment_list);
	}

	$result = new xmlrpcval(array(
		'post_id'       => new xmlrpcval($post['pid'], 'string'),
		'post_title'    => new xmlrpcval($post['subject'], 'base64'),
		'post_content'  => new xmlrpcval(tapatalkEmoji::covertNameToEmoji($post['message']), 'base64'),
		'attachments'   => new xmlrpcval($attachment_list, 'array'),
		'group_id'      => new xmlrpcval($post['posthash']),
		'show_reason'   => new xmlrpcval($mybb->settings['alloweditreason'] && version_compare($mybb->version, '1.8.0','>='),'boolean'),
		'edit_reason'   => new xmlrpcval($post['editreason'], 'base64'),
	), 'struct');

	return new xmlrpcresp($result);
}
