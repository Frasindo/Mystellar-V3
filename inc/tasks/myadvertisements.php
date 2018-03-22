<?php
/***************************************************************************
 *
 *  My Advertisements plugin (/inc/tasks/myadvertisements.php)
 *  Author: Diogo Parrinha
 *  Copyright: © 2014 Diogo Parrinha
 *  
 *  Website: http://mybb-plugins.com
 *  License: license.txt
 *
 *  This plugin adds advertizements zones to your forum.
 *
 ***************************************************************************/

/****************************************************************************
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
****************************************************************************/

function task_myadvertisements($task)
{
	global $mybb, $db, $lang;
	
	$lang->load("myadvertisements");
	
	@set_time_limit(0);
	
	// TODO: store advertisements in cache and use cache here instead of running a query and fetching it like this
	
	// one query per page for sure
	$query = $db->simple_select('myadvertisements_advertisements', '*', 'expire<'.TIME_NOW.' AND expire != 0 AND unlimited = 0');
	while ($ad = $db->fetch_array($query))
	{
		if ($mybb->settings['myadvertisements_sendpm'] == 1 && $mybb->settings['myadvertisements_sendpmuid'] == 1)
		{
			// more queries to send PM
			myadvertisements_send_pm(array('subject' => $lang->myadvertisements_pm_subject, 'message' => $lang->sprintf($lang->myadvertisements_pm_message, htmlspecialchars_uni($ad['name']), $ad['aid']), 'receivepms' => 1, 'touid' => $mybb->settings['myadvertisements_sendpmuid']));
		}
		
		// second query is run if advertisement has experied
		$db->update_query('myadvertisements_advertisements', array('expire' => 0), 'aid='.$ad['aid']);
	}
	
	add_task_log($task, $lang->myadvertisements_task_ran);
}

?>
