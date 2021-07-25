<?php
/*
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<!--**************************
	* PieBill Simple Forums:  PHP based web forums
	* &copy; Copyright 2019 - 2021 Piebill.com
	* SPDX-License-Identifier: GPL-2.0-or-later
	* include for all php sripts
	**************************-->
 */
	// Set to 1 if running on server side else set to 0
$SRVSIDE = 0;

	// Set to 1 if DEBUG process is needed. Otherwise set to 0
$CDEBUG = 1;

	// Name of this forum shown on top
$COMPANY = 'PieBill Simple Forum';

	// There are 3 topic extensions for 3 Topic Headings
	// All TOPICHEADS, TOPICSORT and REPLYSORT should have equal no. of entries
	// All TOPICHEADS, TOPICSORT, REPLYSORT keys should be consistent e.g _fg, _it and _gt.
	// Note: separate by comma, last item has no comma
$TOPICHEADS = [
	'_fg' => 'Forum Guidelines',
	'_it' => 'Important Topics',
	'_gt' => 'General Topics'
];  

	// Sort topics headings either in ascending 'asc' or descending 'des' order
	// All TOPICHEADS, TOPICSORT and REPLYSORT should have equal no. of entries
	// All TOPICHEADS, TOPICSORT, REPLYSORT keys should be consistent e.g _fg, _it and _gt.
	// Note: separate by comma, last item has no comma
$TOPICSORT = [
	'_fg' => 'asc',
	'_it' => 'asc',
	'_gt' => 'des',
];

	// Sort topics replies either in ascending 'asc' or descending 'des' order
	// All TOPICHEADS, TOPICSORT and REPLYSORT should have equal no. of entries
	// All TOPICHEADS, TOPICSORT, REPLYSORT keys should be consistent e.g _fg, _it and _gt.
	// Note: separate by comma, last item has no comma
$REPLYSORT = [
	'_fg' => 'asc',
	'_it' => 'asc',
	'_gt' => 'des',
];  

	// Uses the timezones from https://www.php.net/manual/en/timezones.others.php
$TMZONE = 'GMT';
$TMZONE = 'US/Pacific';
$TMZONE = 'US/Central';
$TMZONE = 'US/Eastern';
$TMZONE = 'Asia/Tokyo';
$TMZONE = 'Asia/Kolkata';

	// Change to your Drive Letter of htdocs
if( $SRVSIDE == 0 )
{
	//Drive location web, forum, Graph, xampp locations
	$DRV = 'C:';
	$DRVHDOCS = $DRV . '/xampp/htdocs';
	$DRVFORUM = $DRV . '/xampp/htdocs/forumsimple';
	$DRVPOST = $DRV . '/xampp/htdocs/forumsimple/posts';
	$DRVBIN = $DRV . '/xampp';  // used as bin directory

	// web forum, graph locations
	$WEBFORUM = '/forumsimple';

} else {
	//Drive location web, forum, Graph, xampp locations
	$DRV = '/home/pieroot';
	$DRVHDOCS = $DRV . '/public_html';
	$DRVFORUM = $DRV . '/public_html/forumsimple';
	$DRVPOST = $DRV . '/public_html/forumsimple/posts';
	$DRVBIN = $DRV;  // used as bin directory

	// web forum, graph locations
	$WEBFORUM = '/forumsimple';

}

/******************** Functions ********************/

function tohttps()
{
    if($_SERVER["HTTPS"] != "on")
    {
	    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	    exit();
    }
}
?>
