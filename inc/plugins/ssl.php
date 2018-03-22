<?php

#
#	This program is free software: you can redistribute it and/or modify
#	it under the terms of the GNU General Public License as published by
#	the Free Software Foundation, either version 3 of the License, or
#	(at your option) any later version.
#
#	This program is distributed in the hope that it will be useful,
#	but WITHOUT ANY WARRANTY; without even the implied warranty of
#	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#	GNU General Public License for more details.
#
#	You should have received a copy of the GNU General Public License
#	along with this program.  If not, see <http://www.gnu.org/licenses/>.
#

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("global_start", "is_ssl");

function ssl_info()
{
	return array(
		"name"			=> "SSL Switcher",
		"description"	=> "Detects whether SSL is being used and changes the \$mybb->settings['bburl'] value accordingly.",
		"website"		=> "http://adrianhayter.com",
		"author"		=> "Adrian Hayter",
		"authorsite"	=> "http://adrianhayter.com",
		"version"		=> "1.1",
		"guid"			=> "7d87cef31ba85c8ebd0ca6b65898eab9",
		"compatibility"	=> "18*",
	);
}

function ssl_activate(){}

function ssl_deactivate(){}

function is_ssl()
{
	global $mybb;

	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
	{
		if (substr(strtolower($mybb->settings['bburl']), 0, 5) == "http:")
		{
			$mybb->settings['bburl'] = preg_replace('/^http/', 'https', $mybb->settings['bburl'], 1);
		}
	}
}

?>