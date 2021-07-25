<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<!--**************************
	* PieBill Simple Forums:  PHP based web forums
	* &copy; Copyright 2019 - 2021 Piebill.com
	* SPDX-License-Identifier: GPL-2.0-or-later
	* shows the initial forums posting page
	**************************-->
<?php
include_once('config.php');
date_default_timezone_set($TMZONE);

if( $CDEBUG )
{
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);
   // error_reporting(E_ERROR | E_PARSE);
}

// Make sure to use only secure https protocol
tohttps();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $COMPANY?></title>
<link href="piestyle.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
function showall(slines, tlines, eid) {
	for(i = slines; i < tlines; i++) {
		div = document.getElementById(eid + i);
		div.style.display = "block";
	}
}
function hideall(slines, tlines, eid) {
	for(i = slines; i < tlines; i++) {
		div = document.getElementById(eid + i);
		div.style.display = "none";
	}
}
function showmenu(divno, eid) {
	div = document.getElementById(eid + divno);
	div.style.display = "block";
}
function hidemenu(divno, eid) {
	div = document.getElementById(eid + divno);
	div.style.display = "none";
}
function testvals(form, title, post, highn, user, group) {
    if (title.value == '' || post.value == '' || group.value == '') {
	alert('You must provide all the requested details. Please try again');
	return false;
    }
   
    // Finally submit the form. 
    form.submit();
    return true;
}
</script>
</head>
<body>
<!-- Upper Forum Posting HTML-->
<div class="mainpage">
<table width="100%" cellpadding="6" cellspacing="0">
<thead><tr><th colspan="5"><?php echo $COMPANY?></th></tr></thead>
  <tbody>
<?php

// returns true if there is no filename or blank filename
function isnullempty($str)
{
    return (!isset($str) || trim($str) === '');
}

// save starting post matter to a file
function savepost($tfile, $tcont)
{
    global $DRVPOST;  // Drive location of post folder
    
    if( !is_dir("$DRVPOST/") )
	    if( !mkdir("$DRVPOST/") )
		    die('Failed 1 to create folder table!');

    if( is_file($tfile) )
    {
	    return 0;
    } else {
	    file_put_contents($tfile, $tcont);
    }
    
    return 1;

} // End of savepost()

// save user reply matter to starting post file 
function savereply($tfile, $tcont)
{
    global $DRVPOST;  // Drive location of post folder
    
    if( !is_dir("$DRVPOST/") )
	    if( !mkdir("$DRVPOST/") )
		    die('Failed 1 to create folder table!');

    if( is_file($tfile) )
    {
	file_put_contents($tfile, $tcont, FILE_APPEND | LOCK_EX);
    } else {
	return 0;
    }
    
    return 1;

} // End of savereply()

// read the starting post file with user replies.
// also creates the inner html for each reply.
function readpost($flname)
{
	global $REPLYSORT;

	$zb = 0; // zebra stripe color flag
	$incr = 0; // used to number the starting Post and Reply details
	$webtxt = ''; // save all the text post and replies to html
	$replyArr = []; // saves each reply to array for sorting
	$totrply = 0;	// total topic replies

	// initialize to zero all variables
	$posted = ''; $ptitle = ''; $parea = ''; $puser = ''; $pgroup = ''; $lastkey = '';
	$posted = ''; $rplyarea = ''; $rplyuser = '';
	$blankln = 0; $rtitle = '';

	// Read Post file to an Array for processing.
	if( is_file($flname) )
		$postArr = file($flname);
	else
		echo '<font color="#990000">Error: Missing text file :</font><br>' .
		basename($flname) . ' :--' . $pstln . '--<br>';

	// Major loop to convert text replies to html
	for($i = 0; $i < count($postArr); $i++)
	{
		// trim the start and end spaces
		$pstln = $postArr[$i];
		
		// match post separation line with only -----
		if( preg_match("/([-]{20,})/", $pstln) )
		{
			// Alernate row color for zebra stripe
			$bgcolor = ($zb++%2==1) ? '#DCDCDC' : '#EBEBEB';

			// if starting post details
			if( $ptitle && $parea )
			{
				$webtxt .=
				'<table width="100%" align="left" cellpadding="6" cellspacing="0"><tbody>' .
				'<tr style="background-color: '.$bgcolor.'">' . "\n" .
				'<td> -'. ++$incr . ' </td>' . "\n" .
				'<td class="post"> '. nl2br($parea) . ' </td>' . "\n" .
				'<td class="post"> '. $posted . ' </td>' . "\n" .
				'<td class="post"> '. timetravel($posted) . ' </td>' . "\n";
				if( $puser )
					$webtxt .= '<td class="post" align="right"> '.
					$puser . ' </td>' . "\n";
				else 
					$webtxt .= '<td>&nbsp;</td>' . "\n";
				$webtxt .= '</tr></tbody></table>' . "\n";
				$ptitle = ''; $parea = '';
			}
			if( $rplyarea ) // if user reply details
			{
				$tmptext =
				'<table width="100%" align="left" cellpadding="6" cellspacing="0"><tbody>' .
				'<tr style="background-color: '.$bgcolor.'">' . "\n" .
				'<td> -'. ++$incr . ' </td>' . "\n" .
				'<td class="post"> '. nl2br($rplyarea) . ' </td>' . "\n" .
				'<td class="post"> '. $posted . ' </td>' . "\n" .
				'<td class="post"> '. timetravel($posted) . ' </td>' . "\n";
				if( $rplyuser )
					$tmptext .= '<td class="post" align="right">&nbsp; '.
					$rplyuser . ' </td>' . "\n";
				else 
					$tmptext .= '<td>&nbsp;</td>' . "\n";
				$tmptext .= '</tr></tbody></table>' . "\n";
				$replyArr[] = $tmptext;
				$rplyarea = '';
			}
			$blankln = 0;
		} else { // if match is not separation line process accordingly

			// Get the key=value pairs
			if( preg_match("/^([a-z]+)=([\w\W]+)$/", $pstln, $matArr) )
			{
				$key = $matArr[1];
				$value = $matArr[2];
				if( $key == 'titlestr' ) {
					$ptitle = trim($value);
					$rtitle = trim($value);
				} elseif( $key == 'pstarea' ) {
					$parea = $value;
				} elseif( $key == 'pstuser' ) {
					$puser = trim($value);
				} elseif( $key == 'pstgroup' ) {
					$pgroup = trim($value);
				} elseif( $key == 'posted' ) {
					$posted = trim($value);
				} elseif( $key == 'rplyarea' ) {
					$rplyarea = $value;
					$totrply++;
				} elseif( $key == 'rplyuser' ) {
					$rplyuser = trim($value);
				}

				$lastkey = $key;

			  // if start post reply contains multiple blank/text lines
			} elseif( $lastkey === 'pstarea' )
			{
				if( preg_match("/^\s*$/", $pstln) )
				{
					if( $blankln ) continue;
					$parea .=  $pstln;
					$blankln = 1;
				} else {
					$parea .= $pstln;
					$blankln = 0;
				}
			  // if user replies contains multiple blank/text lines
			} elseif( $lastkey === 'rplyarea' )
			{
				if( preg_match("/^\s*$/", $pstln) )
				{
					if( $blankln ) continue;
					$rplyarea .= $pstln;
					$blankln = 1;
				} else {
					$rplyarea .= $pstln;
					$blankln = 0;
				}
			} else
			{
				echo '<font color="#990000">Error: Wrong text line format:</font><br>' .
					basename($flname) . ' :--' . $pstln . '--<br>';
			}
		}
	} // End of count(postArr)

	// get file extension
	$extn = substr(basename($flname), -7, 3);

	// Sort user replies in ascending or
	// descending order by date on file extension
	if( $REPLYSORT[$extn] == 'asc' )
	{
		ksort($replyArr);
	} else {
		krsort($replyArr);
	}
	foreach( $replyArr as $rtext )
		$webtxt .= $rtext;

	// Amount of time passed after this forum post
	$tmtrvl = timetravel($posted);

	return array($webtxt, $rtitle, $posted, $tmtrvl, $totrply);

} // End of readpost()

// Get Post Title string and convert to saveable filename
function titletofile($tmptitle, $pstgrp)
{
    global $DRVPOST; // Drive location of post folder

    //remove non alpha numeric characters
    $svefile = preg_replace("/[^A-Za-z0-9 ]/", '', $tmptitle);
        
    //replace more than one space to underscore
    $svefile = preg_replace('/([\s])\1+/', '_', $svefile );
        
    //convert any single spaces to underscrore
    $svefile = str_replace(" ", "_", $svefile);
    
    $svefile = "$DRVPOST/" . $svefile . $pstgrp . '.txt';

    // return false on blank string else return filename
    if( isnullempty($svefile) )
	return 0;
    else
	return $svefile;

} // End of titletofile()

// Get time duration from posting
function timetravel($passed)
{
	$nowdate = date("Y-m-d H:i:s");
	$timenow = new DateTime($nowdate);
	$timeback = new DateTime($passed);
	$timediff =  $timeback->diff($timenow);
	$ndays = $timediff->days;
	$nhrs = $timediff->h;
	$nmins = $timediff->i;
	
	$tmdiff = $nhrs . "hr " . $nmins . "m";

	if( $ndays > 0 )
		$tmdiff = $ndays . "dy " . $nhrs . "hr";

	return  $tmdiff;

} // End of timetravel()

//  Get the posted= timestamp and convert epoch time
function flctime($pfile)
{
    foreach( file($pfile) as $line )
    {
	$line = trim($line);
	if( strpos($line, 'osted=') )
	{
	    list($key, $value) = explode('=', $line);
	    // echo "<br> strtime : " . strtotime($value);
	    return $value;
	}
    }
} // End of flctime()

// Get the first Title Posting Html
function getpsthtml()
{
	global $TOPICHEADS;

	// get _fg, _it, _gt into an array
	$tpcKArr = array_keys($TOPICHEADS);

	$rethtml = 
	'<tr><td colspan="5" align="left">' .
	'Title:<br><input class="ipbox" type="text" size="90" ' .
	'name="titlestr" id="titlestr" /><br>' .
	'Details:<br><textarea class="ipbox" cols="93" rows="10" '.
	' name="pstarea" id="pstarea"></textarea><br>' .
	'<label class="ihbox" for="hname">Comments:</label> ' .
	'<textarea class="ihbox" cols="93" rows="10" name="hname" id="hname"></textarea>' .
	'Username:<br><input class="ipbox" type="text" size="50" maxlength="50" ' .
	' name="pstuser" id="pstuser" value="Optional username" onfocus="this.value=\'\'" /><br>' .
	'Post Group:<br>';
	foreach( $tpcKArr as $tkey )
	{
	   $rethtml .=
	   '<label class="ilab" for="'. $tkey .'">' . $TOPICHEADS[$tkey] . '</label>' .
	   '<input class="irbx" type="radio" name="pstgroup" ' .
	   ' id="' . $tkey . '" value="' . $tkey . '" /> ';
	}
	$rethtml .=
	'<button class="savebutt" type="submit" onclick="return testvals(' .
	'this.form, this.form.titlestr, this.form.pstarea, this.form.hname, ' .
	'this.form.pstuser, this.form.pstgroup);">Save Post</button></td></tr>';

	return $rethtml;

} // End of getpsthtml()

// Get the reply posting Html
function getrplhtml($pstfile)
{
	return 
	'</tr><tr><td colspan="5" align="left">' .
	'Reply Details:<br><textarea class="ipbox"' .
	' cols="93" rows="10" name="rplyarea" id="rplyarea"></textarea><br>' .
	'<label class="ihbox" for="hname">Comments:</label> ' .
	'<textarea class="ihbox" cols="93" rows="10" name="hname" id="hname"></textarea>' .
	'Username:<br><input class="ipbox" type="text" size="50" ' .
	' maxlength="50" name="rplyuser" id="rplyuser" value="Optional username" '.
	' onfocus="this.value=\'\'" /><br>' .
	'<input type="hidden" name="postfile" value="' . $pstfile . '" />' .
	'<button class="savebutt" type="submit">Post Reply</button></td></tr>';

} // End getrplhtml()

	// Used to return to this page after Save Post process
    $returl = urlencode($url="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

	// Get current date and time
    $currdate = date("Y-m-d H:i:s");

	// get "Forum Guidelines", "Important Topics", "General Topics" into an array
    $tpcVArr = array_values($TOPICHEADS);

    	// This is HTML Page Menu header
    $savehtml = '<tr><td colspan="5" align="left">' ;
    foreach( $tpcVArr as $tval )
    {
	$savehtml .=
	'<a class="blueknob" href="#' . $tval . '">' . $tval . '</a> ';
    }
    $savehtml .=
    ' <a class="blueknob" href="index.php">Start New Topic</a>' .
    '</td></tr>';

	// Show the bottom Topics Html only when tpcflg is 1
    $tpcflg = 1;

	// MVC controller for respective "Save Post New Topic", "Save Post on Reply" 
	// and "Open Topic"
    if( !empty($_POST['hname']) ) // End of POST[postfile]
    {
	// This is default New Topic posting for form submission bots.
	echo '<tr><td colspan="5">Thank your for you submission '.
	'</td></tr></tbody></table></body></html>';
	exit();

    } elseif( isset($_POST['titlestr']) && ($_POST['titlestr'] !== 'Post Title') &&
	    isset($_POST['pstarea']) && isset($_POST['pstgroup']) )
    {
	// This is Successful New Topic post saved page.

	$titlebox =  $_POST['titlestr'];

	$fcont = "posted=" . $currdate . "\n";
	$postgroup = '';

	if( isset($_POST['pstgroup']) )
		$postgroup = $_POST['pstgroup'];

	//add all post vars to pstArr array
	foreach($_POST as $key => $value)
	{
		// $pstArr[$key] = filter_var($value, FILTER_SANITIZE_STRING);
		if( $key === 'returl' )
			continue;
		if( ($key === 'pstuser') && ($value === 'Optional username') )
			continue;
		if( $key === 'hname' )
			continue;
		$fcont .= $key . '=' . $value . "\n";
	}
	$fcont .= '--------------------------------------' . "\n";

	$savefile = titletofile($titlebox, $postgroup);
	if( !$savefile )
	{
		$savehtml .=
		'<tr><td colspan="5" align="center"><br><font color="#CC1111">' .
		'<b>Post file is missing! ' . $titlebox . '</b></font></td></tr>';
	}

	// Save starting Post to a file
	if( !savepost($savefile, $fcont) )
		$savehtml .=
		'<tr><td colspan="5" align="center"><br><font color="#CC1111">' .
		'Error: Title already exists! Please Change the "Title" : <b>' . $titlebox .
		'</b></font></td></tr>';
	else
		$savehtml .=
		'<tr><td colspan="5" align="center"><br><font color="#CC1111">' .
		'<b>Post saved to file : ' . $titlebox . '</b></font></td></tr>';
	
	$savehtml .= getpsthtml();

    } elseif( isset($_POST['rplyarea']) ) // End of POST[postfile]
    {
	// This is Successful Reply to post/topic saved page.

	$rplyarea =  $_POST['rplyarea'];
	$postfile =  $_POST['postfile'];

	$fcont = "posted=" . $currdate . "\n";

	//add all post vars to pstArr array
	foreach($_POST as $key => $value)
	{
		if( $key === 'returl' )
			continue;
		if( $key === 'postfile' )
			continue;
		if( $key === 'hname' )
			continue;
		$fcont .= $key . '=' . $value . "\n";
	}
	$fcont .= '--------------------------------------' . "\n";
	
	$savefile = "$DRVPOST/" . $postfile;
	if( !$savefile )
		$savehtml .=
		'<tr><td colspan="5" align="center"><br><font color="#CC1111">' .
		'<b>Reply Post file is missing! ' . $savefile . '</b></font></td></tr>';

	// Append Reply details to starting post file
	if( !savereply($savefile, $fcont) )
		$savehtml .= '<tr><td colspan="5" align="center"><br><font color="#CC1111">' .
		'Error: Reply already exists! Please Change the "Reply Details" : <b>' . $rplyarea .
		'</b></font></td></tr>';
	else
		$savehtml .=
		'<tr><td colspan="5" align="center"><br><font color="#CC1111">' .
		'<b>Reply posting saved : ' . $rplyarea . '</b></font></td></tr>';

	$savehtml .= getpsthtml();

    } elseif( isset($_POST['postfile']) )   // End of POST[rplyarea]
    {
	// This is Reply to open post/topic page

	$postfile = $_POST['postfile'];
	$pstser = $_POST['postser'];

	$pstfile = "$DRVPOST/" . $postfile;

	list($postcont, $pstitle, $tmdate, $tmtxt, $allrply) = readpost($pstfile);
	$savehtml .=
	'<tr><td colspan="5" align="center"><br><font color="#CC1111"> ' .
	'<b>Retrieved Post : </b></font> ' . $pstitle . '</td></tr>';

	$savehtml .= getrplhtml($postfile);

	$savehtml .=
	'<tr><td class="svtbl">'.
	'<table width="100%" align="left" cellpadding="6" cellspacing="0"><tbody>' .
	'<tr><td><div><font color="#990000"><b>' . $pstser . '</b></font>' .
	'</td><td>' . $pstitle . '</td><td>' . $tmdate . '</td><td>' . $tmtxt .
	'</td><td>&nbsp;</td></tr></tbody></table>' .
	'<div>'. $postcont .  '</div>' .
	'</td></tr>' . "\n";

	// Hide bottom Topics Html, becuase it is a replying to post page
	$tpcflg = 0;

    } else // End of POST[postfile] 
    {
	// This is default New Topic posting and all Topics Display Page.
	$savehtml .= getpsthtml();
    }
    // End of MVC Controller

    // Save each post textfile to inner html
    $posthtml = ' ';

    // Save the Bottom Topics html content
    $topcshtml = ' ';

    // Handling all the _fg.txt _it.txt and _gt.txt text files
    // and converting to Topics Html
    if( ($tpcflg == 1) && is_dir("$DRVPOST/") )
    {

	// Collating all topic files into one html block
	$nincr = 1;	// increment posting number
	$pincr = 1;	// increment posting number

	foreach( $TOPICHEADS as $textn => $sectitle )
	{
	    $fileArr = glob("$DRVPOST" . '/*' . $textn . '.txt');
	    
	    // Sort user replies in ascending or descending order of time
	    // Sorts using the post creation date present in the text file
	    if( $TOPICSORT[$textn] == 'asc' )
	    {
		usort($fileArr, function($x, $y) {
			return flctime($y) < flctime($x);
		});
	    } else {
		usort($fileArr, function($x, $y) {
			return flctime($x) < flctime($y);
		});
	    }
	    
	    // Sort user replies in ascending or descending order of time
	    // Sorts using the system file creation and modification timestamp
	    /*
	    if( $TOPICSORT[$textn] == 'asc' )
	    {
		usort($fileArr, function($x, $y) {
			return filectime($y) < filectime($x);
		});
	    } else {
		usort($fileArr, function($x, $y) {
			return filemtime($y) < filemtime($x);
		});
	    }
	    */
	    
	    // if found a posting file 
	    $fndflg = 0;
	    
	    $tpcheads = 0;	// total topic heads
	    $tpcrplys = 0;	// total topic replies

	    if( count($fileArr) > 0 )
	    {
    		$posthtml = ' ';
		foreach( $fileArr as $pfile )
		{
			// incr topic head count
			$tpcheads++;

			list($postcont, $pstitle, $tmdate, $tmtxt, $allrply) = readpost($pfile);

			// incr topic reply count
			$tpcrplys += $allrply;

			$postfile = basename($pfile);
			$posthtml .=
			'<tr><td class="svtbl">'.
			'<table width="100%" align="left" cellpadding="6" cellspacing="0"><tbody>' .
			'<tr><td><div><font color="#990000"><b>' . $pincr . '</b></font>' .
			' <a class="itm" href="#" onclick="showmenu(' . $pincr . ', \'pxt\');">+</a> ' .
			' <a class="itm" href="#" onclick="hidemenu(' . $pincr . ', \'pxt\');">-</a> ' .
			'</td><td>' . $pstitle . '</td><td>' . $tmdate . '</td><td>' . $tmtxt .
			'</td><td><input class="irbx" type="radio" name="postfile" id="postfile" ' .
			' onclick="javascript: submit()" value="' . $postfile . '" /> ' .
			'<label class="olab" for="postfile">reply</label></div> ' .
			'</td></tr></tbody></table>' .
			'<div id="pxt' . $pincr . '">' . $postcont .  '</div>' .
			'<input type="hidden" name="postser" value="' . $pincr . '" />' .
			'</td></tr>' . "\n";

			$pincr++;
			$fndflg = 1;

		} // End of foreach(fileArr)
	    } // End of if(fileArr)
	    
	    // Append topcshtml only if a file has been read above
	    if( $fndflg )
	    {
		$topcshtml .=
		'<br><font color="#3355AA"><b><a id="' . $sectitle . '">' . $sectitle .
		':</a></b></font><br> ' .
		'<a class="itm" href="#" onclick="showall(' . $nincr . ', ' . $pincr .
		', \'pxt\');">++</a> '.
		' | <a class="itm" href="#" onclick="hideall(' . $nincr . ', ' . $pincr .
		', \'pxt\');">--</a><br><br>' .
		' <table class="svtbl" width="100%"> ' .  $posthtml .
		'<script type="text/javascript">hideall(' . $nincr . ', ' . $pincr .
		', \'pxt\');</script>' .
		'</table>' . "\n" .
		'<table width="100%" cellpadding="6" cellspacing="0"><tbody>' .
		'<tr><td>Total Topics: ' . $tpcheads . '</td><td>' .
		'<td>Total Replies: ' . $tpcrplys . '</td><td>' .
		'</table><br>' . "\n" ;
    		$fndflg = 0;
		$nincr = $pincr;
	    }

	} // End of foreach(titleArrr)
    } // End of if DRVPOST

?>
    <tr><td colspan="5">
<form method="post" action="index.php">
	<?php echo $savehtml ?>
	<input type="hidden" name="returl" value="<?php echo $returl; ?>" />
</form>
    </td></tr>
</tbody>
</table>
</div>
<!-- Bottom Topics Section HTML Form-->
<div class="mainpage">
<table width="100%" cellpadding="0" cellspacing="0">
<tbody>
<tr><td>
<form method="post" action="index.php">
	<?php echo $topcshtml ?>
	<input type="hidden" name="returl" value="<?php echo $returl; ?>" />
</form>
</td></tr>
</tbody>
</table>
</div>
<div class="mainpage">
<ul> PieBill 2021, license under GPL2.0+ </ul>
</div>
</body>
</html>
