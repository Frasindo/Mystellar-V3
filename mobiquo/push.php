<?php
define('IN_MYBB', 1);
$_GET['action'] = "login";
define('THIS_SCRIPT', 'member.php');
define("ALLOWABLE_PAGE","login");
require_once '../global.php';
error_reporting(E_ALL & ~E_NOTICE);

require_once TT_ROOT . 'push/TapatalkPush.php';
$tapatalk_push = new TapatalkPush($mybb->settings['tapatalk_push_key'], $mybb->settings['bburl']);

$return_status = $tapatalk_push->do_push_request(array('test' => 1), true);

if(empty($mybb->settings['tapatalk_push_key']))
	$return_status = 'Please set Tapatalk API Key at forum option/setting';
$board_url = $mybb->settings['bburl'];
$option_status = 'On';

echo '<b>Tapatalk Push Notification Status Monitor</b><br/>';
echo '<br/>Push notification test: ' . (($return_status == '1') ? '<b>Success</b>' : '<font color="red">Failed('.$return_status.')</font>');
echo '<br/>Current forum url: ' . $board_url;

echo '<br/><br/><a href="http://tapatalk.com/api/api.php" target="_blank">Tapatalk API for Universal Forum Access</a> | <a href="http://tapatalk.com/mobile.php" target="_blank">Tapatalk Mobile Applications</a><br>
    For more details, please visit <a href="http://tapatalk.com" target="_blank">http://tapatalk.com</a>';

