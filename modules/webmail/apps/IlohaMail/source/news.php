<?php
/////////////////////////////////////////////////////////
//	
//	source/news.php
//
//	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL 
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	AUTHOR: Ryo Chijiiwa <ryo@ilohamail.org>
	FILE: source/news.php
	PURPOSE:
		New aggregator.
		
********************************************************/

	include("../include/stopwatch.inc");
	
	$timer = new stopwatch(true);
	$timer->register("start");
	
	$override_cs = 'UTF-8';
	include('../include/super2global.inc');
	include('../include/header_main.inc');
	include('../include/langs.inc');
	include('../include/icl.inc');	
	include('../lang/'.$my_prefs["lang"].'bookmarks.inc');
	include_once("../include/fs_path.inc");	
	if (!$DB_BOOKMARKS_TABLE) $dm_override_backend="FS";	
	include('../include/data_manager.inc');  
	include('../include/rss_aggregator.inc');
	
	$timer->register("included");	
	
	//make sure feature is not disabled
	if ($DISABLE_BOOKMARKS || $DISABLE_NEWS){
		echo $bmError[2];
		echo "</body></html>\n";
		$timer->dump();
		exit;
	}
	$timer->register("authenticated");
	
	//initialize some globals
	$MAX_ITEMS_CHANNEL = 10;
	$MAX_ITEMS_TOTAL = 50;

	//open DM connection
	$dm = new RSSAggregator_obj($MAX_ITEMS_CHANNEL,$MAX_ITEMS_TOTAL,$my_prefs['timezone'],$IS_PUBLIC);
	if ($dm->initialize($loginID, $host, $DB_BOOKMARKS_TABLE, $DB_TYPE)){
	}else{
		echo "Data Manager initialization failed:<br>\n";
		$dm->showError();
	}
	
	echo '<!-- js usage: '.$my_prefs['js_usage'].' js_mode:'.$sess_js_mode.'//-->'."\n";
	if ($my_prefs['js_usage']=='h'){
	}

	//get caregories
	$groups = $dm->fetchGroups();
	
	//fetch id,name
	$names = $dm->fetchNames($show_group);
	
	//get rss URLs
	if ($show_feed){
		$feeds = $dm->fetchSingleFeed($show_feed);
	}else if ($show_group){
		$feeds = $dm->fetchGroupFeeds($show_group);
	}else if ($url){
		$feeds = array($url);
	}else{
		$feeds = $dm->fetchAllFeeds();
	}
	
	$timer->register("finished DB ops");
	$this_url = 'news.php?user='.$user.($show_group?'&show_group='.$show_group:'').($show_feed?'&show_feed='.$show_feed:'');
	
	
	//fetch entries from all feeds into single array, with timestamp as key
	$num_channels = $dm->aggregateFeeds($feeds, $channels, $entries);
	if (is_array($entries)) krsort($entries);
	$timer->register("fetched");
	
	if ($format=='atom'){
		header('Content-type: text/xml');
		$data = $dm->createAtom($channels, $entries, $show_group, $my_prefs);
		$timer->purge();
		echo $data;
		exit;
	}
	
	if ($IS_PUBLIC) $title = str_replace('%name', $my_prefs['user_name'], $bmStrings['pubtitlenews']);
	else $title = $bmStrings['rss feeds'];
	?>
	<table border="0" cellspacing="2" cellpadding="0" width="100%">
		<tr class="dk"><td>
			<span class="bigTitle"><?php echo $title ?></span>
			&nbsp;
			<span class="mainHeadingSmall">
				[<a href="<?php echo $this_url ?>" class="mainHeadingSmall"><?php echo $bmStrings['refresh']?></a>]
				[<a href="bookmarks.php?user=<?php echo $user ?>" class="mainHeadingSmall"><?php echo $bmStrings['bookmarks']?></a>]
			<?php if (!$IS_PUBLIC){ ?>
				[<a href="bookmarks.php?user=<?php echo $user ?>#frm" class="mainHeadingSmall"><?php echo $bmStrings['add']?></a>]
			<?php } ?>
			</span>
		</td></tr>
	</table>

	<span class="error"><?php echo $error?></span>
	<p>
	
	<?php

	//show error if any
	$error.=$dm->error;
	if ($error) echo '<div class="error">'.$error.'</div>';



	//start table, and show category|feed select form
	echo "<center>\n";
	echo '<table border="0" cellspacing="1" cellpadding="2" class="md" width="95%">';
	echo '<tr class="dk"><td>';
		echo '<form method="GET" action="'.$_SERVER['PHP_SELF'].'">';
		echo '<input type="hidden" name="user" value="'.$user.'">';
		echo '<div class="mainHeading" style="float:left">Category:';
		echo '<select name="show_group">';
		echo '<option value="">All'."\n";
		echo showoptions1d($groups, false, $show_group);
		echo '</select>';
		
		echo '&nbsp;&nbsp;&nbsp;';
		//echo '<span class="mainHeading">Feed:</span>';
		echo '<select name="show_feed">';
		echo '<option value="">All'."\n";
		echo showoptions2d($names, "id", "name", $show_feed);
		echo '</select>';
		
		echo '&nbsp;&nbsp;&nbsp;';
		echo '<input type="submit" name="submit" value="Show">';
	//echo '</td>';
	//echo '<td align="right">';
		$feed_url = getPublicURL($dataID).'&show_group='.$show_group.'&show_feed='.$show_feed.'&format=atom';
		echo '</div><div class="mainHeading" style="float:right;text-align:right">Re-syndicate:</span><a href="'.$feed_url.'">';
		echo '<img src="themes/'.$my_prefs['theme'].'/images/xml.gif" border=0>';
		echo '</a></span> ';
		echo '</form>';
	echo '</div>';
	echo '</tr>';
	
	//list in reverse chronological order
	if (is_array($entries) && count($entries)>0){
		reset($entries);
		$num_items = 0;
		while(list($timestamp,$entry)=each($entries)){
			//echo "<!--\n";
			//print_r($entry);
			//echo "//-->\n";
			echo '<tr class="lt">';
				echo '<td valign="middle" colspan=2>';
				$cid = $entry["channel_id"];
				echo '<a href="'.$entry['link'].'" class="nae">'.$entry['title'].'</a>'; //nae
				echo '<br>';
				echo '<span class="nadt">'.date("M d, Y h:i:s A", $timestamp).'</span> ';
				echo '<a href="'.$channels[$cid]['link'].'" class="nac">'.$channels[$cid]['title'].'</a>'; //nac
				echo '<br>';
				$content='';
				if ($entry['description']) $content = $entry['description'];
				else if ($entry['atom_content']) $content = $entry['atom_content'];
				echo '<div class="nads">';
				echo $content;
				if (is_array($entry['enclosure'])){
					$enclosure = $entry['enclosure'][0];
					$pos = strrpos($enclosure['url'], '/');
					$file = substr($enclosure['url'], $pos+1);
					echo '<p>Podcast: <a href="'.$enclosure['url'].'">'.$file.'</a> ';
					echo ' ('.$enclosure['type'].', '.ShowBytes($enclosure['length']).')';
				}
				echo '</span>';
				echo '</td>';
			echo "</tr>\n";
			$num_items++;
			if ($num_items >= $MAX_ITEMS_TOTAL) break;
		}
	}
	echo "</table>";
	
?>
</BODY></HTML>
<!--
<?php
$timer->register("stop");
$timer->dump();
?>
//-->