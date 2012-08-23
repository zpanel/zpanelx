<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 * 
 */

defined('WM_ROOTPATH') || define('WM_ROOTPATH', (dirname(__FILE__).'/'));

require_once WM_ROOTPATH.'application/include.php';

require_once WM_ROOTPATH.'common/inc_constants.php';
require_once WM_ROOTPATH.'common/class_convertutils.php';

require_once WM_ROOTPATH.'calendar/accounts.php';

require_once WM_ROOTPATH.'libraries/afterlogic/DAV/autoload.php';

$oAccount = null;
$error_desc = $sSkin = $sLang = $sCalendarName = '';
if (empty($_GET['cal']) || !preg_match('/^[a-zA-Z0-9_\-.]+$/', $_GET['cal']))
{
	$error_desc = 'Can\'t load publicated calendar. Check calendar link.';
}
else
{
	$sCalendarHash = $_GET['cal'];

	/* @var $oApiUsersManager CApiUsersManager */
	$oApiUsersManager = CApi::Manager('users');

	$iAccountId = $oApiUsersManager->GetDefaultAccountId(CSession::Get(APP_SESSION_USER_ID));
	
	/* @var $oAccount CAccount */
	$oAccount = $oApiUsersManager->GetAccountById($iAccountId);
	
	/* @var $oCalUser CCalUser */
	$oCalUser = $oAccount ? $oApiUsersManager->GetOrCreateCalUserByUserId($oAccount->IdUser) : null;

	$oSettings =& CApi::GetSettings();

	$sSkin = $oSettings->GetConf('WebMail/DefaultSkin');
	$sLang = $oSettings->GetConf('Common/DefaultLanguage');
	$sSiteName = $oSettings->GetConf('Common/DefaultLanguage');
	
	$sDefaultTimeFormat = (int) $oSettings->GetConf('Common/DefaultTimeFormat');
	$sDefaultDateFormat = 1; // TODO Magic
	$sShowWeekEnds = (int) $oSettings->GetConf('Calendar/ShowWeekEnds');
	$sWorkdayStarts = (int) $oSettings->GetConf('Calendar/WorkdayStarts');
	$sWorkdayEnds = (int) $oSettings->GetConf('Calendar/WorkdayEnds');
	$sShowWorkDay = (int) $oSettings->GetConf('Calendar/ShowWorkDay');
	$sWeekStartsOn = (int) $oSettings->GetConf('Calendar/WeekStartsOn');
	$sDefaultTab = (int) $oSettings->GetConf('Calendar/DefaultTab');
	
	if ($oAccount && $oCalUser)
	{
		$sSkin = $oAccount->User->DefaultSkin;
		$sLang = $oAccount->User->DefaultLanguage;
		$sSiteName = $oAccount->Domain->SiteName;

		$sDefaultTimeFormat = $oAccount->User->DefaultTimeFormat;
		$sDefaultDateFormat = 1; // TODO Magic
		$sShowWeekEnds =  (int) $oCalUser->ShowWeekEnds;
		$sShowWorkDay = (int) $oCalUser->ShowWorkDay;
		$sWorkdayStarts = $oCalUser->WorkDayStarts;
		$sWorkdayEnds = $oCalUser->WorkDayEnds;
		$sWeekStartsOn = $oCalUser->WeekStartsOn;
		$sDefaultTab = $oCalUser->DefaultTab;
	}

	AppIncludeLanguage($sLang);
	
	/* @var $oApiCalendarManager CApiCalendarManager */
	$oApiCalendarManager = CApi::Manager('calendar');	
	$aPublicCalendar = $oApiCalendarManager->GetPublicCalendarByHash($sCalendarHash);
	
	if ($aPublicCalendar)
	{
		$sCalendarId = $aPublicCalendar->Id;
		$sCalendarName = $aPublicCalendar->DisplayName;
		
		CSession::Set(CALENDAR_ID, $sCalendarId);
		CSession::Set(ACCESS_LEVEL, 1);
	}
	else
	{
		$error_desc = "Can't load publicated calendar. Check calendar link.";
	}
}

/* @var $oApiWebmailManager CApiWebmailManager */
$oApiWebmailManager = CApi::Manager('webmail');

@header('Content-type: text/html; charset=utf-8');

if (strlen($error_desc) > 0) {

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<link rel="shortcut icon" href="favicon.ico" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Cache-Control" content="private,max-age=1209600" />
	<title>Error</title>
	<link type="text/css" rel="stylesheet" href="./calendar/skins/AfterLogic/calendar_styles.css" />
	<link type="text/css" rel="stylesheet" href="./skins/AfterLogic/styles.css" />
</head>
<body>
<div id="content" class="wm_content">
	<div class="wm_logo" id="logo" tabindex="-1" onfocus="this.blur();"></div>
	<div class="wm_login_error">
		<div class="wm_login_error_icon"></div>
	    <div class="wm_login_error_message"><?php echo $error_desc; ?></div>
	</div>
	<div class="wm_copyright" id="copyright">
<?php
		@require('inc.footer.php');
		exit('</div></div>');
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<link rel="shortcut icon" href="favicon.ico" />
	<title><?php echo $sCalendarName."&nbsp;&#8212;&nbsp;".$sSiteName;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link type="text/css" rel="stylesheet" href="./calendar/skins/<?php echo ConvertUtils::AttributeQuote($sSkin); ?>/calendar_styles.css" />
	<link type="text/css" rel="stylesheet" href="./skins/<?php echo ConvertUtils::AttributeQuote($sSkin); ?>/styles.css" />
	<script type="text/javascript" src="./calendar/langs.js.php?v=<?php echo ConvertUtils::GetJsVersion(); ?>&lang=<?php echo ConvertUtils::AttributeQuote($sLang); ?>"></script>
<?php 

	$aLoadScripts = $oApiWebmailManager->GetJsFilesList(array('jquery', 'cal_def', 'cal_f', 'cal_p'));
	if (is_array($aLoadScripts) && 0 < count($aLoadScripts))
	{
		foreach ($aLoadScripts as $sScriptName)
		{
			echo '<script type="text/javascript" src="'.$sScriptName.'"></script>';
		}
	}
?>
	<script type="text/javascript">
        var SITE_NAME = "<?php echo $sSiteName; ?>";
		var calendarTitle = "<?php echo ConvertUtils::ClearJavaScriptString($sSiteName, '"'); ?>";
		var calendarSkinName = "<?php echo ConvertUtils::ClearJavaScriptString($sSkin, '"'); ?>";
		var calendarInManager, EventForm, Grid;
		var calendarType = CALENDAR_PUBLISHED, view = WEEK, isLoaded = false;
		var processing_url = "./calendar/processing-pub.php";
		var WebMail, Browser;
		var CheckMailUrl = '';
    	var ErrorHandler, LoadHandler, TakeDataHandler;

		var setcache = {
            timeformat: <?php echo $sDefaultTimeFormat; ?>,
            dateformat: <?php echo $sDefaultDateFormat; ?>,
            showweekends: <?php echo $sShowWeekEnds; ?>,
            workdaystarts: <?php echo $sWorkdayStarts; ?>,
            workdayends: <?php echo $sWorkdayEnds; ?>,
            showworkday: <?php echo $sShowWorkDay; ?>,
            weekstartson: <?php echo $sWeekStartsOn; ?>,
            defaulttab: <?php echo $sDefaultTab; ?>
        };

		function initCalendar()
		{
			Browser = new CBrowser();
			WebMail = new CWebMail(calendarTitle, calendarSkinName);
    		WebMail.shown = true;
			PopupMenu = new CPopupMenus();
			var transport = GetXMLHTTPRequest();
			if (!transport)
			{
				document.location = "";
			} 
			else
			{
				PreventIEFlickering();
//				var tbl = $id('info_cont');
//				BuildInformation(tbl);
//				tbl = document.getElementById('info_cont');
//				tbl.style.right = 'auto';
//				var offsetWidth = tbl.offsetWidth;
//				tbl.style.left = Math.round((GetWidth() - offsetWidth)/2) + 'px';

				LoadDataFromServer();
                Grid = new CGrid(mydate);
                RenderCalendar();

				window.nowDate = new Date();
				//OperaAlldayScroll();
				calendarInManager = new CCalendarTable();
				FillEvents();
                Grid.LoadView();

				//document.onclick = documentOnClickHandler;
				EventForm = new CEventForm();
				isLoaded = true;
				HideInfo();
			}
		};
		window.onresize = Resizer;
	</script>
</head>
<body onload="initCalendar();" onclick="PopupMenu.checkShownItems();">
	
	<table class="wm_information wm_status_information" id="info_cont"><tr><td>
		<div class="wm_info_block">
			<div class="wm_shadow">
				<div class="a">&nbsp;</div>
			</div>
			<div class="wm_info_message" id="info_message">
				<?php echo InfoLoading;?>
			</div>
			<div class="a">&nbsp;</div>
			<div class="b">&nbsp;</div>
		</div>
	</td></tr></table>

	<span id="allspan">
	<div class="wm_content">
		<div id="drager" style="position: absolute; top: 0px; left: 0px; border: 3px solid #cccccc; display:none;"> </div>
		<div class="wm_logo" id="logo"></div>
		<div class="wm_toolbar cal_toolbar" id="toolbar">
					<span class="wm_toolbar_content">
						<div class="time_tabs">
							<div id="tab_3" class="time_tabs_outer" style="right:210px;"><div><?php echo TabMonth;?></div></div>
							<div id="tab_2" class="time_tabs_outer" style="right:346px;"><div><?php echo TabWeek;?></div></div>
							<div id="tab_1" class="time_tabs_outer" style="right:482px;"><div><?php echo TabDay;?></div></div>
						</div>
						<span class="wm_toolbar_item" id="toolbar_new_event">
							<img src="./calendar/skins/<?php echo $sSkin; ?>/menu/new_event.gif" alt="<?php echo AltNewEvent;?>" title="<?php echo AltNewEvent;?>"/>
							<span><?php echo ToolNewEvent;?></span>
						</span><span class="wm_toolbar_item" id="toolbar_back" style="display:none">
							<img src="./calendar/skins/<?php echo $sSkin; ?>/menu/back.gif" alt="<?php echo AltBack;?>" title="<?php echo AltBack;?>"/>
							<span><?php echo ToolBack;?></span>
						</span><span class="wm_toolbar_item" id="toolbar_today">
							<img src="./calendar/skins/<?php echo $sSkin; ?>/menu/today.gif" alt="<?php echo AltToday;?>" title="<?php echo AltToday;?>" />
							<span><?php echo ToolToday;?></span>
						</span>

						<div id="toolbar_interval_switch">
							<span class="wm_toolbar_item" onmouseover="this.className='wm_toolbar_item_over'"
								  onmouseout="this.className='wm_toolbar_item'" onclick="DateBrowse(-1);">
								<span class="calendar_arrow_left"></span>
							</span>

							<span id="time_title_1"></span>
							<span id="time_title_2"></span>
							<span id="time_title_3"></span>

							<span class="wm_toolbar_item" onmouseover="this.className='wm_toolbar_item_over'"
								  onmouseout="this.className='wm_toolbar_item'" onclick="DateBrowse(1);">
								<span class="calendar_arrow_right"></span>
							</span>
						</div>
					</span>
					<div class="clear"></div>
				</div>
		<div class="main_block" id="main_block">
			<div id="upper_indent" class="upper_indent"></div>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0" style="border-collapse:separate; ">
			<tr>
				<td class="mainbody">
					<div id="mainbody">
						<div id="work_area_day" style="display:none;">
							<div id="day_headers_day" class="day_headers_day">
								<div class="day_headers_outer" style="width:40px; left: -40px;"><div class="day_headers_inner"></div></div>
								<div class="day_headers_outer" style="left: 0%; width:100%" id="dh1"><div class="day_headers_inner"><span unselectable="on" id="day_header"></span></div></div>
								<div class="day_headers_outer" style="left: 100%; width:16px"><div class="day_headers_inner"></div></div>
							</div>
							<div id="area_1_day">
								<div class="calowner">
									<table>
										<tr>
											<td style="width:40px"></td>
											<td>
												<div id="grid_1d" class="grid_1">
													<div style="left:0%; height:100%;" id="c0" class="vrule"></div>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div id="area_2_day">
								<div class="calowner">
									<table>
										<tr>
											<td style="width:40px;">
												<div id="rheaders_1" class="rheaders"></div>
											</td>
											<td>
												<div id="grid_2d" class="grid_2">
													<div style="left: 0%;" id="c1" class="vrule"></div>
													<div style="top:4.5ex;" id="r1" class="hrule_odd"></div>
													<div style="top:9ex;" id="r2" class="hrule"></div>
													<div style="top:13.5ex;" id="r3" class="hrule_odd"></div>
													<div style="top:18ex;" id="r4" class="hrule"></div>
													<div style="top:22.5ex;" id="r5" class="hrule_odd"></div>
													<div style="top:27ex;" id="r6" class="hrule"></div>
													<div style="top:31.5ex;" id="r7" class="hrule_odd"></div>
													<div style="top:36ex;" id="r8" class="hrule"></div>
													<div style="top:40.5ex;" id="r9" class="hrule_odd"></div>
													<div style="top:45ex;" id="r10" class="hrule"></div>
													<div style="top:49.5ex;" id="r11" class="hrule_odd"></div>
													<div style="top:54ex;" id="r12" class="hrule"></div>
													<div style="top:58.5ex;" id="r13" class="hrule_odd"></div>
													<div style="top:63ex;" id="r14" class="hrule"></div>
													<div style="top:67.5ex;" id="r15" class="hrule_odd"></div>
													<div style="top:72ex;" id="r16" class="hrule"></div>
													<div style="top:76.5ex;" id="r17" class="hrule_odd"></div>
													<div style="top:81ex;" id="r18" class="hrule"></div>
													<div style="top:85.5ex;" id="r19" class="hrule_odd"></div>
													<div style="top:90ex;" id="r20" class="hrule"></div>
													<div style="top:94.5ex;" id="r21" class="hrule_odd"></div>
													<div style="top:99ex;" id="r22" class="hrule"></div>
													<div style="top:103.5ex;" id="r23" class="hrule_odd"></div>
													<div style="top:108ex;" id="r24" class="hrule"></div>
													<div style="top:112.5ex;" id="r25" class="hrule_odd"></div>
													<div style="top:117ex;" id="r26" class="hrule"></div>
													<div style="top:121.5ex;" id="r27" class="hrule_odd"></div>
													<div style="top:126ex;" id="r28" class="hrule"></div>
													<div style="top:130.5ex;" id="r29" class="hrule_odd"></div>
													<div style="top:135ex;" id="r30" class="hrule"></div>
													<div style="top:139.5ex;" id="r31" class="hrule_odd"></div>
													<div style="top:144ex;" id="r32" class="hrule"></div>
													<div style="top:148.5ex;" id="r33" class="hrule_odd"></div>
													<div style="top:153ex;" id="r34" class="hrule"></div>
													<div style="top:157.5ex;" id="r35" class="hrule_odd"></div>
													<div style="top:162ex;" id="r36" class="hrule"></div>
													<div style="top:166.5ex;" id="r37" class="hrule_odd"></div>
													<div style="top:171ex;" id="r38" class="hrule"></div>
													<div style="top:175.5ex;" id="r39" class="hrule_odd"></div>
													<div style="top:180ex;" id="r40" class="hrule"></div>
													<div style="top:184.5ex;" id="r41" class="hrule_odd"></div>
													<div style="top:189ex;" id="r42" class="hrule"></div>
													<div style="top:193.5ex;" id="r43" class="hrule_odd"></div>
													<div style="top:198ex;" id="r44" class="hrule"></div>
													<div style="top:202.5ex;" id="r45" class="hrule_odd"></div>
													<div style="top:207ex;" id="r46" class="hrule"></div>
													<div style="top:211.5ex;" id="r47" class="hrule_odd"></div>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div id="arrow_layer_day" class="arrow_layer" style="bottom:0px; z-index:2000; right:20px; height:500px; width:0px;">
								<div id="arrow_up_day" class="arrow_up" style=" display:none"
									onmouseover="this.className='arrow_up_hover'"
									onmouseout="this.className='arrow_up'"></div>

								<div id="arrow_down_day" class="arrow_down" style=" display:none"
									onmouseover="this.className='arrow_down_hover'"
									onmouseout="this.className='arrow_down'"></div>
							</div>
						</div><!--#work_area_day-->
						<div id="work_area_week" style="display:none">
							<div id="day_headers_week" class="day_headers_week"></div>
							<div id="area_1_week">
							 	<div class="calowner">
									<table>
										<tr>
											<td style="width:40px;"></td>
											<td>
												<div id="grid_1w" class="grid_1">
													<div style="left:0%; height:100%;" class="vrule"></div>
													<div style="left:14.2857%; height:100%;" class="vrule"></div>
													<div style="left:28.5714%; height:100%;" class="vrule"></div>
													<div style="left:42.8571%; height:100%;" class="vrule"></div>
													<div style="left:57.1429%; height:100%;" class="vrule"></div>
													<div style="left:71.4286%; height:100%;" class="vrule"></div>
													<div style="left:85.7143%; height:100%;" class="vrule"></div>
													<div id="current_day_1" style="left: 0%; height:100%;"></div>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div id="area_2_week">
								<div class="calowner">
									<table>
										<tr>
											<td style="width:40px">
												<div id="rheaders_2" class="rheaders"></div>
											</td>
											<td>
												<div id="grid_2w" class="grid_2">
														<div style="left: 0%;" id="c1" class="vrule"></div>
														<div style="left: 14.2857%;" id="c2" class="vrule"></div>
														<div style="left: 28.5714%;" id="c3" class="vrule"></div>
														<div style="left: 42.8571%;" id="c4" class="vrule"></div>
														<div style="left: 57.1429%;" id="c5" class="vrule"></div>
														<div style="left: 71.4286%;" id="c6" class="vrule"></div>
														<div style="left: 85.7143%;" id="c7" class="vrule"></div>
														<div style="top:4.5ex;" id="r1" class="hrule_odd"></div>
														<div style="top:9ex;" id="r2" class="hrule"></div>
														<div style="top:13.5ex;" id="r3" class="hrule_odd"></div>
														<div style="top:18ex;" id="r4" class="hrule"></div>
														<div style="top:22.5ex;" id="r5" class="hrule_odd"></div>
														<div style="top:27ex;" id="r6" class="hrule"></div>
														<div style="top:31.5ex;" id="r7" class="hrule_odd"></div>
														<div style="top:36ex;" id="r8" class="hrule"></div>
														<div style="top:40.5ex;" id="r9" class="hrule_odd"></div>
														<div style="top:45ex;" id="r10" class="hrule"></div>
														<div style="top:49.5ex;" id="r11" class="hrule_odd"></div>
														<div style="top:54ex;" id="r12" class="hrule"></div>
														<div style="top:58.5ex;" id="r13" class="hrule_odd"></div>
														<div style="top:63ex;" id="r14" class="hrule"></div>
														<div style="top:67.5ex;" id="r15" class="hrule_odd"></div>
														<div style="top:72ex;" id="r16" class="hrule"></div>
														<div style="top:76.5ex;" id="r17" class="hrule_odd"></div>
														<div style="top:81ex;" id="r18" class="hrule"></div>
														<div style="top:85.5ex;" id="r19" class="hrule_odd"></div>
														<div style="top:90ex;" id="r20" class="hrule"></div>
														<div style="top:94.5ex;" id="r21" class="hrule_odd"></div>
														<div style="top:99ex;" id="r22" class="hrule"></div>
														<div style="top:103.5ex;" id="r23" class="hrule_odd"></div>
														<div style="top:108ex;" id="r24" class="hrule"></div>
														<div style="top:112.5ex;" id="r25" class="hrule_odd"></div>
														<div style="top:117ex;" id="r26" class="hrule"></div>
														<div style="top:121.5ex;" id="r27" class="hrule_odd"></div>
														<div style="top:126ex;" id="r28" class="hrule"></div>
														<div style="top:130.5ex;" id="r29" class="hrule_odd"></div>
														<div style="top:135ex;" id="r30" class="hrule"></div>
														<div style="top:139.5ex;" id="r31" class="hrule_odd"></div>
														<div style="top:144ex;" id="r32" class="hrule"></div>
														<div style="top:148.5ex;" id="r33" class="hrule_odd"></div>
														<div style="top:153ex;" id="r34" class="hrule"></div>
														<div style="top:157.5ex;" id="r35" class="hrule_odd"></div>
														<div style="top:162ex;" id="r36" class="hrule"></div>
														<div style="top:166.5ex;" id="r37" class="hrule_odd"></div>
														<div style="top:171ex;" id="r38" class="hrule"></div>
														<div style="top:175.5ex;" id="r39" class="hrule_odd"></div>
														<div style="top:180ex;" id="r40" class="hrule"></div>
														<div style="top:184.5ex;" id="r41" class="hrule_odd"></div>
														<div style="top:189ex;" id="r42" class="hrule"></div>
														<div style="top:193.5ex;" id="r43" class="hrule_odd"></div>
														<div style="top:198ex;" id="r44" class="hrule"></div>
														<div style="top:202.5ex;" id="r45" class="hrule_odd"></div>
														<div style="top:207ex;" id="r46" class="hrule"></div>
														<div style="top:211.5ex;" id="r47" class="hrule_odd"></div>
														<div id="current_day_2" style="left: 0%; height:216ex;"></div>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>

							<div style="position:absolute; bottom:0px; height:0px; right:0px; width:100%">
								<div style="margin:0px 16px 0px 40px;  width:auto; ">
									<div style=" width:100%; position:relative;">

										<div id="arrow_layer_week_0" class="arrow_layer" style="bottom:0px;  z-index:15; left:14.2857%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_1" class="arrow_layer" style="bottom:0px;  z-index:15; left:28.5714%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_2" class="arrow_layer" style="bottom:0px;  z-index:15; left:42.8571%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none;"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_3" class="arrow_layer" style="bottom:0px;  z-index:15; left:57.1429%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_4" class="arrow_layer" style="bottom:0px;  z-index:15; left:71.4286%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_5" class="arrow_layer" style="bottom:0px;  z-index:15; left:85.7143%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none;"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>

										<div id="arrow_layer_week_6" class="arrow_layer" style="bottom:0px;  z-index:15; left:100%; height:500px; width:0px;">
											<div class="arrow_up" style=" display:none"
											onmouseover="this.className='arrow_up_hover'"
											onmouseout="this.className='arrow_up'"></div>

											<div class="arrow_down" style=" display:none;"
											onmouseover="this.className='arrow_down_hover'"
											onmouseout="this.className='arrow_down'"></div>
										</div>
									</div>
								</div>
							</div>

						</div><!--#work_area_week-->
						<div id="work_area_month" style="display:none;">
							<div id="day_headers_month" class="day_headers_month" style="margin:0px; ">
									<div class="day_headers_outer" style="left: 0%;" id="dh1"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 14.285%;" id="dh2"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 28.5714%;" id="dh3"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 42.8571%;" id="dh4"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 57.1429%;" id="dh5"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 71.4286%;" id="dh6"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
									<div class="day_headers_outer" style="left: 85.7143%;" id="dh7"><div class="day_headers_inner"><span unselectable="on"></span></div></div>
							</div>
							<div id="area_2_month">
									<div class="calowner">
											<div id="grid_2_month" class="grid_2_month" style=" height:100%">
													<div style="left: 14.2857%; height: 100%;" id="c0" class="vrule"></div>
													<div style="left: 28.5714%; height: 100%;" id="c1" class="vrule"></div>
													<div style="left: 42.8571%; height: 100%;" id="c2" class="vrule"></div>
													<div style="left: 57.1429%; height: 100%;" id="c3" class="vrule"></div>
													<div style="left: 71.4286%; height: 100%;" id="c4" class="vrule"></div>
													<div style="left: 85.7143%; height: 100%;" id="c5" class="vrule"></div>
													<div id="month_cell_container"></div>
											</div>
									</div>
							</div>
					</div><!--#work_area_month-->
					</div><!--#mainbody-->
				</td>
				<td  class="spacer">&nbsp;</td>
				<td id="right" class="right">
					<div class="calendar_header" id="calhead1">
						<div class="calendar_header_text"><?php echo CalendarHeader;?></div>
					</div>
					
					<div class="small_calendars mini_calendar box" id="mini_calendar_box">
                        <table class="Calendar_Title">
							<tr>
								<td class="arrow_left"><img class="calendar_switcher" id="prevMonthSwitcher" alt="<?php echo AltPrevMonth; ?>" title="<?php echo AltPrevMonth; ?>" src="./calendar/skins/calendar/minicalendar_arrow_left_activ.gif" /></td>
								<td class="middle">
									<div class="middleTdSelector_">
										<div id="middleTdSelector" class="middleTdSelector">
											<span id="MonthSelector"></span>
											<img id="imgSelector" alt="" src="./calendar/skins/calendar/minicalendar_arrow_bottom.gif" />
										</div>
									</div>
									<div id="monthsList" class="event edit_gray monthsList"></div>
								</td>
								<td class="arrow_right"><img class="calendar_switcher" id="nextMonthSwitcher" alt="<?php echo AltNextMonth; ?>" title="<?php echo AltNextMonth; ?>" src="./calendar/skins/calendar/minicalendar_arrow_right_activ.gif" /></td>
							</tr>
						</table>
						<table id="calendarInManagerBlock" class="calendar_block" cellpadding="0" cellspacing="0">
							<tr>
								<td class="title"></td><td class="title"></td><td class="title"></td><td class="title"></td><td class="title"></td><td class="title"></td><td class="title"></td>
							</tr>
							<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
							<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
							<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
							<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
							<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
							<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
							<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
						</table>
                    </div>
					
					<div class="calendar_manager" id="manager_box" style="border-top:0;">
						<div id="manager_list" class="manager_list" style="background-color:transparent;"></div>
					</div>
				</td>
			</tr>
		</table>
			<div id="lower_indent" class="lower_indent"></div>
		</div><!--#main_block-->

		<div id="edit_window">
			<div class="mask"></div>
			<!-- edit form -->
			<div id="edit_form" class="edit_form">
				<div style="height:auto; position:relative;">
					<input type="hidden" name="evform_id" id="evform_id" value="0" />
					<input type="hidden" id="id_calendar" value="0" />
					<div class="event_black" id="event_black" style="width:435px;">
						<div class="a"></div>
						<div class="b"></div>
						<div class="event_middle" id="edit_event_shadow"></div>
						<div class="b"></div>
						<div class="a"></div>
					</div>
					<div class="eventcontainer_2" id="eventcontainer" style="position:relative; width:435px;">
						<div class="event_edit" id="eventEdit" style="width:435px;">
						<div class="a"></div>
						<div class="b"></div>
						<div class="event_middle">
							<div class="event_text">
								<div class="time" id="ef_fulldate"><?php echo EventHeaderNew;?></div>
								<div class="text" style="width:425px;">
									<div class="em_spacer"></div>
										<div class="row_title"><?php echo EventSubject;?></div>
										<div><input id="EventSubject" class="input" name="EventSubject" style="width:338px;" maxlength="50" tabIndex="1" readonly /></div>
										<div class="em_spacer"></div>
										<div class="row_title"><?php echo EventCalendar;?></div>
										<div class="eventcontainer_bw" id="edit_select_box" style="top:4em; left:68px; z-index:3001;">
											<div class="a"></div>
											<div class="b"></div>
											<div class="event_middle">
												<div class="calendar_text" style="width:136px; height:100%;position:relative;">
													<div id="color_calendar_now" class="color_pick"></div>
													<div id="calen_sal" class="text"></div>
												</div>
											</div>
											<div class="b"></div>
											<div class="a"></div>
										</div>
										
										<div class="em_spacer" style="height:0px;"></div>
										<table border="0" cellpadding="0" cellspacing="0" style="float:left;margin-top:0.6em" >
											<tr>
												<td id="tmp_id" class="row_title"><?php echo EventFrom;?></td>
												<td>
													<input id="EventTimeFrom" name="EventTimeFrom" class="input" style="display:block;width:60px; color: #696969; text-align: center; margin-right:4px; padding:0px" value="" tabIndex="2" readonly />
												</td>
												<td>
													<input id="EventDateFrom" name="EventDateFrom" class="input" style="display:block;width:74px; color: #696969; text-align: center;padding:0px" value="" tabIndex="3" readonly />
												</td>
											</tr>
										</table>
										<table border="0" cellpadding="0" cellspacing="0" style="float:left;margin-top:0.6em">
											<tr>
												<td class="row_title" style="text-align:center; float:none; width:5em;"><?php echo EventTill;?></td>
												<td>
													<input id="EventTimeTill" name="EventTimeTill" class="input" style="display:block;width:60px; color: #696969; text-align: center; margin-right:4px;padding:0px" value="" tabIndex="4" readonly />
												</td>
												<td>
													<input id="EventDateTill" name="EventDateTill" class="input" style="display:block; width:74px; color: #696969; text-align: center;padding:0px" value="" tabIndex="5" readonly />
												</td>
											</tr>
										</table>
										<div class="em_spacer"></div>
										<div class="row_title"><?php echo CalendarDescription;?></div>
										<div><textarea name="EventDescription" id="EventDescription" class="input" style="background-color:white; color:#696969; width:338px; height:4em; padding-left:3px;" tabIndex="6" readonly></textarea></div>
										<div class="em_spacer"></div>
										<div class="row_title"></div>
										
										<div class="em_spacer"></div>
										<div class="em_spacer" style="height:0px"></div>
										<div class="row_title" style="height:3em;"></div>
										<input type="button" id="cancelbut" value="<?php echo ButtonClose;?>" class="wm_button" tabIndex="9" />
										<div class="em_spacer"></div>
									<br>
								</div>
							</div>
						</div>
						<div class="b"></div>
						<div class="a"></div>
					</div>
					</div>
				</div>
			</div>
		</div><!--#edit_window-->

	</div><!--#wm_content-->
</span>
</body>
</html>
