<?php

/*
 * Copyright (C) 2002-2012 AfterLogic Corp. (www.afterlogic.com)
 * Distributed under the terms of the license described in COPYING
 */


defined('WM_ROOTPATH') || define('WM_ROOTPATH', (dirname(__FILE__).'/'));
require_once WM_ROOTPATH.'application/include.php';
$oInput = new api_Http();

require_once WM_ROOTPATH.'common/inc_constants.php';
require_once WM_ROOTPATH.'common/class_convertutils.php';

require_once WM_ROOTPATH.'calendar/accounts.php';
require_once 'libraries/afterlogic/DAV/autoload.php';

@ob_start('obStartNoGzip');

/* @var $oApiWebmailManager CApiWebmailManager */
$oApiWebmailManager = CApi::Manager('webmail');

/* @var $oApiUsersManager CApiUsersManager */
$oApiUsersManager = CApi::Manager('users');

$iUserId = CSession::Get(APP_SESSION_USER_ID);
		
$iAccountId = $oApiUsersManager->GetDefaultAccountId($iUserId);

/* @var $oAccount CAccount */
$oAccount = $oApiUsersManager->GetAccountById($iAccountId);

if (!$oAccount)
{
	exit('
<script type="text/javascript">
	if (parent && parent.HideCalendar) { parent.HideCalendar("error", 1); } else { document.location = "index.php?error=1"; }
</script>');
}

AppIncludeLanguage($oAccount->User->DefaultLanguage);

$skin = $oAccount->User->DefaultSkin;
$lang = $oAccount->User->DefaultLanguage;

$oApiCollaborationManager = CApi::Manager('collaboration');
$bCalendarSharingSupported = $oApiCollaborationManager && $oApiCollaborationManager->IsCalendarSharingSupported();
$bCalendarAppointmentsSupported = $oApiCollaborationManager && $oApiCollaborationManager->IsCalendarAppointmentsSupported();

$accountDiv = null;

if (null !== $iAccountId)
{
	$accountDiv = new AccountDiv($oAccount);	
}
else
{
	CSession::Set(SEPARATED, true);
}

/* <a href="webmail.php?start=3" target="_blank">'.JS_LANG_Contacts.'</a> */
$hideContacts = (!$oAccount->User->AllowContacts) ? '' :
    '<span class="wm_accountslist_contacts">
		<a href="#" onclick="parent.HideCalendar(\'contacts\'); return false;">'.JS_LANG_Contacts.'</a>
	</span>';

$headerClass = ((bool) CSession::Get(SEPARATED, false)) ? 'wm_hide' : 'wm_accountslist';

@header('Content-type: text/html; charset=utf-8');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <link rel="shortcut icon" href="favicon.ico" />
        <title><?php echo $oAccount->Domain->SiteName;?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link type="text/css" rel="stylesheet" href="./calendar/skins/<?php echo ConvertUtils::AttributeQuote($skin); ?>/calendar_styles.css?v=<?php echo ConvertUtils::GetJsVersion(); ?>" />
        <link type="text/css" rel="stylesheet" href="./skins/<?php echo ConvertUtils::AttributeQuote($skin); ?>/styles.css?v=<?php echo ConvertUtils::GetJsVersion(); ?>" />
        <script type="text/javascript" src="./calendar/langs.js.php?v=<?php echo ConvertUtils::GetJsVersion(); ?>&lang=<?php echo ConvertUtils::AttributeQuote($lang); ?>"></script>
<?php 
		
$aLoadScripts = $oApiWebmailManager->GetJsFilesList(array('jquery', 'common', 'cal_f', 'cal'));
if (is_array($aLoadScripts) && 0 < count($aLoadScripts))
{
	foreach ($aLoadScripts as $sScriptName)
	{
		echo '<script type="text/javascript" src="'.$sScriptName.'"></script>';
	}
}

?>	
        <script type="text/javascript">
            var calendarTitle = "<?php echo ConvertUtils::ClearJavaScriptString($oAccount->Domain->SiteName, '"'); ?>";
			var calendarSkinName = "<?php echo ConvertUtils::ClearJavaScriptString($oAccount->Domain->DefaultSkin, '"'); ?>";
			var CsrfToken = "<?php echo ConvertUtils::ClearJavaScriptString(CApi::CsrfBrowserToken($oInput), '"'); ?>";
            var calendarTableStart, calendarTableEnd, calendarInManager, timeSelector, SharingForm, EventForm, CalendarForm, calendarRepeatUntil, Grid, QuickMenu, ChooseForm, timeSelectorTill, timeSelectorFrom, Calendars, Selection, Resize;
            var calendarType = CALENDAR_MAIN, view = WEEK, isLoaded = false;
            var processing_url = "./calendar/processing.php", publication_url = "/go.php";
            var Seporated = <?php echo (CSession::Get(SEPARATED, false)) ? 'true' : 'false'; ?>;
			var Browser, WebMail;
			
			var sharedCalendars = <?php echo $bCalendarSharingSupported ? 'true' : 'false'; ?>;
			var allowAppointments = <?php echo $bCalendarAppointmentsSupported ? 'true' : 'false'; ?>;

            function initCalendar()
			{
				Browser = (parent && parent.Browser) ? parent.Browser : new CBrowser();
				WebMail = (parent && parent.WebMail) ? parent.WebMail : null/*new CWebMail(calendarTitle, calendarSkinName)*/;

                if (parent && parent.DisplayCalendarHandler) parent.DisplayCalendarHandler();
                PopupMenus = new CPopupMenus();
                <?php
                if ($accountDiv && $accountDiv->Count() > 1) {
				?>
				var accountsListPopupMenu = new CPopupMenu(document.getElementById("popup_menu_1"), document.getElementById("popup_control_1"), "wm_account_menu", document.getElementById("popup_replace_1"), document.getElementById("popup_replace_1"), "", "", "", "");
				PopupMenus.addItem(accountsListPopupMenu);
                <?php
				}
                ?>
                var transport = GetXMLHTTPRequest();
                if (!transport)
				{
					document.location = "";
				}
				else
				{
					PreventIEFlickering();
					var tbl = $id('info_cont');
					tbl.className = 'wm_hide';
					
					LoadDataFromServer();
				}
			};
			window.onresize = Resizer;
        </script>
    </head>
    <body onclick="PopupMenus.checkShownItems();" onload="initCalendar()">
		<table class="wm_information wm_status_information" cellpadding="0" cellspacing="0" style="right: auto; width: auto; top: 0px; left: 604px;" id="info_cont">
			<tr style="position:relative;z-index:20">
				<td class="wm_shadow" style="width:2px;font-size:1px;"></td>
				<td>
					<div class="wm_info_message" id="info_message">
						<span><?php echo InfoLoading;?></span>
					</div>
					<div class="a">&nbsp;</div>
					<div class="b">&nbsp;</div>
				</td>
				<td class="wm_shadow" style="width:2px;font-size:1px;">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" class="wm_shadow" style="height:2px;background:none;">
					<div class="a">&nbsp;</div>
					<div class="b">&nbsp;</div>
				</td>
			</tr>
			<tr style="position:relative;z-index:19">
				<td colspan="3" style="height:2px;">
					<div class="a wm_shadow" style="margin:0px 2px;height:2px; top:-4px; position:relative; border:0px;background:#555;">&nbsp;</div>
				</td>
			</tr>
		</table>
        <span id="allspan">
            <div class="wm_content">
                <div id="drager" style="position: absolute; top: 0px; left: 0px; border: 3px solid #cccccc; display:none;"> </div>
                <div class="wm_logo" id="logo"></div>
                <table class="<?php echo $headerClass; ?>" id="accountslist">
                    <tr>
                        <td>
                        <?php
                            if ($accountDiv) {
                                echo $accountDiv->doTitle();
                                echo $accountDiv->ToHideDiv();
                            }
                            echo $hideContacts;
                            ?>
                            <span class="wm_accountslist_calendar wm_active_tab">
                                <a href="javascript:void(0);"><?php echo Calendar;?></a>
                            </span>
                            <span class="wm_accountslist_logout">
                                <a href="#" onclick="parent.HideCalendar('logout'); return false;"><?php echo JS_LANG_Logout;?></a>
                            </span>
                            <span id="settings" class="wm_accountslist_settings">
                                <a href="#" onclick="parent.HideCalendar('settings'); return false;"><?php echo JS_LANG_Settings;?></a>
                            </span>
                        </td>
                    </tr>
                </table>

				<div class="wm_toolbar cal_toolbar" id="toolbar">
					<span class="wm_toolbar_content">
						<div class="time_tabs">
							<div id="tab_3" class="time_tabs_outer" style="right:210px;"><div><?php echo TabMonth;?></div></div>
							<div id="tab_2" class="time_tabs_outer" style="right:346px;"><div><?php echo TabWeek;?></div></div>
							<div id="tab_1" class="time_tabs_outer" style="right:482px;"><div><?php echo TabDay;?></div></div>
						</div>
						<span class="wm_toolbar_item" id="toolbar_new_event">
							<img src="./calendar/skins/<?php echo $skin; ?>/menu/new_event.gif" alt="<?php echo AltNewEvent;?>" title="<?php echo AltNewEvent;?>"/>
							<span><?php echo ToolNewEvent;?></span>
						</span><span class="wm_toolbar_item" id="toolbar_back" style="display:none">
							<img src="./calendar/skins/<?php echo $skin; ?>/menu/back.gif" alt="<?php echo AltBack;?>" title="<?php echo AltBack;?>"/>
							<span><?php echo ToolBack;?></span>
						</span><span class="wm_toolbar_item" id="toolbar_today">
							<img src="./calendar/skins/<?php echo $skin; ?>/menu/today.gif" alt="<?php echo AltToday;?>" title="<?php echo AltToday;?>" />
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
                                                                <div style="top:4.5ex;" id="dr1" class="hrule_odd"></div>
                                                                <div style="top:9ex;" id="dr2" class="hrule"></div>
                                                                <div style="top:13.5ex;" id="dr3" class="hrule_odd"></div>
                                                                <div style="top:18ex;" id="dr4" class="hrule"></div>
                                                                <div style="top:22.5ex;" id="dr5" class="hrule_odd"></div>
                                                                <div style="top:27ex;" id="dr6" class="hrule"></div>
                                                                <div style="top:31.5ex;" id="dr7" class="hrule_odd"></div>
                                                                <div style="top:36ex;" id="dr8" class="hrule"></div>
                                                                <div style="top:40.5ex;" id="dr9" class="hrule_odd"></div>
                                                                <div style="top:45ex;" id="dr10" class="hrule"></div>
                                                                <div style="top:49.5ex;" id="dr11" class="hrule_odd"></div>
                                                                <div style="top:54ex;" id="dr12" class="hrule"></div>
                                                                <div style="top:58.5ex;" id="dr13" class="hrule_odd"></div>
                                                                <div style="top:63ex;" id="dr14" class="hrule"></div>
                                                                <div style="top:67.5ex;" id="dr15" class="hrule_odd"></div>
                                                                <div style="top:72ex;" id="dr16" class="hrule"></div>
                                                                <div style="top:76.5ex;" id="dr17" class="hrule_odd"></div>
                                                                <div style="top:81ex;" id="dr18" class="hrule"></div>
                                                                <div style="top:85.5ex;" id="dr19" class="hrule_odd"></div>
                                                                <div style="top:90ex;" id="dr20" class="hrule"></div>
                                                                <div style="top:94.5ex;" id="dr21" class="hrule_odd"></div>
                                                                <div style="top:99ex;" id="dr22" class="hrule"></div>
                                                                <div style="top:103.5ex;" id="dr23" class="hrule_odd"></div>
                                                                <div style="top:108ex;" id="dr24" class="hrule"></div>
                                                                <div style="top:112.5ex;" id="dr25" class="hrule_odd"></div>
                                                                <div style="top:117ex;" id="dr26" class="hrule"></div>
                                                                <div style="top:121.5ex;" id="dr27" class="hrule_odd"></div>
                                                                <div style="top:126ex;" id="dr28" class="hrule"></div>
                                                                <div style="top:130.5ex;" id="dr29" class="hrule_odd"></div>
                                                                <div style="top:135ex;" id="dr30" class="hrule"></div>
                                                                <div style="top:139.5ex;" id="dr31" class="hrule_odd"></div>
                                                                <div style="top:144ex;" id="dr32" class="hrule"></div>
                                                                <div style="top:148.5ex;" id="dr33" class="hrule_odd"></div>
                                                                <div style="top:153ex;" id="dr34" class="hrule"></div>
                                                                <div style="top:157.5ex;" id="dr35" class="hrule_odd"></div>
                                                                <div style="top:162ex;" id="dr36" class="hrule"></div>
                                                                <div style="top:166.5ex;" id="dr37" class="hrule_odd"></div>
                                                                <div style="top:171ex;" id="dr38" class="hrule"></div>
                                                                <div style="top:175.5ex;" id="dr39" class="hrule_odd"></div>
                                                                <div style="top:180ex;" id="dr40" class="hrule"></div>
                                                                <div style="top:184.5ex;" id="dr41" class="hrule_odd"></div>
                                                                <div style="top:189ex;" id="dr42" class="hrule"></div>
                                                                <div style="top:193.5ex;" id="dr43" class="hrule_odd"></div>
                                                                <div style="top:198ex;" id="dr44" class="hrule"></div>
                                                                <div style="top:202.5ex;" id="dr45" class="hrule_odd"></div>
                                                                <div style="top:207ex;" id="dr46" class="hrule"></div>
                                                                <div style="top:211.5ex;" id="dr47" class="hrule_odd"></div>
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
                                                                <div style="top:4.5ex;" id="wr1" class="hrule_odd"></div>
                                                                <div style="top:9ex;" id="wr2" class="hrule"></div>
                                                                <div style="top:13.5ex;" id="wr3" class="hrule_odd"></div>
                                                                <div style="top:18ex;" id="wr4" class="hrule"></div>
                                                                <div style="top:22.5ex;" id="wr5" class="hrule_odd"></div>
                                                                <div style="top:27ex;" id="wr6" class="hrule"></div>
                                                                <div style="top:31.5ex;" id="wr7" class="hrule_odd"></div>
                                                                <div style="top:36ex;" id="wr8" class="hrule"></div>
                                                                <div style="top:40.5ex;" id="wr9" class="hrule_odd"></div>
                                                                <div style="top:45ex;" id="wr10" class="hrule"></div>
                                                                <div style="top:49.5ex;" id="wr11" class="hrule_odd"></div>
                                                                <div style="top:54ex;" id="wr12" class="hrule"></div>
                                                                <div style="top:58.5ex;" id="wr13" class="hrule_odd"></div>
                                                                <div style="top:63ex;" id="wr14" class="hrule"></div>
                                                                <div style="top:67.5ex;" id="wr15" class="hrule_odd"></div>
                                                                <div style="top:72ex;" id="wr16" class="hrule"></div>
                                                                <div style="top:76.5ex;" id="wr17" class="hrule_odd"></div>
                                                                <div style="top:81ex;" id="wr18" class="hrule"></div>
                                                                <div style="top:85.5ex;" id="wr19" class="hrule_odd"></div>
                                                                <div style="top:90ex;" id="wr20" class="hrule"></div>
                                                                <div style="top:94.5ex;" id="wr21" class="hrule_odd"></div>
                                                                <div style="top:99ex;" id="wr22" class="hrule"></div>
                                                                <div style="top:103.5ex;" id="wr23" class="hrule_odd"></div>
                                                                <div style="top:108ex;" id="wr24" class="hrule"></div>
                                                                <div style="top:112.5ex;" id="wr25" class="hrule_odd"></div>
                                                                <div style="top:117ex;" id="wr26" class="hrule"></div>
                                                                <div style="top:121.5ex;" id="wr27" class="hrule_odd"></div>
                                                                <div style="top:126ex;" id="wr28" class="hrule"></div>
                                                                <div style="top:130.5ex;" id="wr29" class="hrule_odd"></div>
                                                                <div style="top:135ex;" id="wr30" class="hrule"></div>
                                                                <div style="top:139.5ex;" id="wr31" class="hrule_odd"></div>
                                                                <div style="top:144ex;" id="wr32" class="hrule"></div>
                                                                <div style="top:148.5ex;" id="wr33" class="hrule_odd"></div>
                                                                <div style="top:153ex;" id="wr34" class="hrule"></div>
                                                                <div style="top:157.5ex;" id="wr35" class="hrule_odd"></div>
                                                                <div style="top:162ex;" id="wr36" class="hrule"></div>
                                                                <div style="top:166.5ex;" id="wr37" class="hrule_odd"></div>
                                                                <div style="top:171ex;" id="wr38" class="hrule"></div>
                                                                <div style="top:175.5ex;" id="wr39" class="hrule_odd"></div>
                                                                <div style="top:180ex;" id="wr40" class="hrule"></div>
                                                                <div style="top:184.5ex;" id="wr41" class="hrule_odd"></div>
                                                                <div style="top:189ex;" id="wr42" class="hrule"></div>
                                                                <div style="top:193.5ex;" id="wr43" class="hrule_odd"></div>
                                                                <div style="top:198ex;" id="wr44" class="hrule"></div>
                                                                <div style="top:202.5ex;" id="wr45" class="hrule_odd"></div>
                                                                <div style="top:207ex;" id="wr46" class="hrule"></div>
                                                                <div style="top:211.5ex;" id="wr47" class="hrule_odd"></div>
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

                                <div class="calendar_manager" id="manager_box">
                                    <div class="calendar_header" id="calhead2">
                                        <div class="calendar_header_text"><?php echo CalendarsManager;?></div>
                                    </div>
                                    <div id="manager_list" class="manager_list">
                                        <div class="new_calendar"><a href="javascript:void(0);"  onclick="CalendarForm.CalendarFormCreate();"><span>+</span>&nbsp;<?php echo CalendarActionNew;?></a></div>
                                        <div id="my_calendars"><div class="calendar_header1"><?php echo Title_MyCalendars;?></div></div>
                                        <div class="calendar_header1">
                                            <span id="sharedCalendarsHeader" style="display:none;"><?php echo Title_SharedCalendars;?></span>
                                        </div>
                                        <div id="shared_calendars"></div>
                                    </div>
                                    <div id="checkSharedCalendarsHeader" class="check_shared_calendar"><a href="javascript:void(0);" onclick="RefreshData()"><?php echo Title_CheckSharedCalendars; ?></a></div>
                                    <div class="quick_menu" id="quick_menu"></div>
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
                        <div style="height:auto; position:relative;"><!-- top: -125px;"-->
                            <input type="hidden" name="evform_id" id="evform_id" value="0" />
                            <input type="hidden" id="id_calendar" value="0" />
                            <div class="event_black" id="event_black">
                                <div class="a"></div>
                                <div class="b"></div>
                                <div class="event_middle" id="edit_event_shadow"></div>
                                <div class="b"></div>
                                <div class="a"></div>
                            </div>
                            <div class="eventcontainer_2" id="eventcontainer" style="position:relative; width:435px;">
                                <div class="event_edit" id="eventEdit">
                                    <div class="a"></div>
                                    <div class="b"></div>
                                    <div class="event_middle">
                                        <div class="event_text">
                                            <div class="time" id="ef_fulldate"><?php echo EventHeaderNew;?></div>
                                            <div id="eventLeftContainer" style="float:left; width: 425px; overflow:visible;">
												<div class="text" style="width:425px;">
	                                                <div class="em_spacer"></div>
	                                                <div class="row_title"><?php echo EventSubject;?></div>
	                                                <div><input id="EventSubject" class="input" name="EventSubject" style="width:338px;" maxlength="50" tabIndex="1" /></div>
	                                                <div class="em_spacer"></div>
	                                                <div class="row_title"><?php echo EventCalendar;?></div>
	                                                <div style="float:left; padding-right:10px; overflow:visible;">
	                                                    <div class="eventcontainer_bw" id="edit_select_box">
	                                                        <div class="a"></div>
	                                                        <div class="b"></div>
	                                                        <div class="event_middle">
	                                                            <div class="calendar_text" style="cursor:pointer; width:145px; position:relative;">
	                                                                <div id="color_calendar_now" class="color_pick"></div>
																	<div id="calen_sal" class="text" style="white-space:nowrap;"></div>
	                                                                <div id="calendar_arrow" class="vis_check calendar_arrow"></div>
	                                                            </div>
	                                                        </div>
	                                                        <div class="b"></div>
	                                                        <div class="a"></div>
	                                                    </div>
	                                                    <div class="calendar_list" id="edit_select_box_list"></div>
	                                                </div>
	                                                <div id="allday_container">
	                                                    <input type="checkbox" id="alldayCbk" class="wm_checkbox" />&nbsp;
	                                                    <label for="alldayCbk"><?php echo Allday; ?></label>
	                                                </div>
	                                                <div class="em_spacer"></div>
													<table border="0" cellpadding="0" cellspacing="0" style="float:left;" >
	                                                    <tr>
	                                                        <td id="tmp_id" class="row_title"><?php echo EventFrom;?></td>
	                                                        <td style="width:60px">
	                                                            <input id="EventTimeFrom" name="EventTimeFrom" class="input" style="display:block; width:60px; text-align: center; margin-right:4px; padding:0px" value="" tabIndex="2" />
	                                                            <div style="position:absolute;" id="EventTimeFrom_dropdown"></div>
	                                                        </td>
	                                                        <td style="width:84px">
	                                                            <input id="EventDateFrom" name="EventDateFrom" class="input" style="display:block;width:80px; text-align: center;padding:0px" value="" tabIndex="3" />
	                                                            <div style="position:absolute;margin-top:-1px;" id="st">
	                                                                <table class="Calendar_Title">
	                                                                    <tr>
	                                                                        <td class="arrow_left"><img class="calendar_switcher" id="st_prevMonthSwitcher" alt="<?php echo AltPrevMonth; ?>" title="<?php echo AltPrevMonth; ?>" src="./calendar/skins/calendar/minicalendar_arrow_left_activ.gif" /></td>
	                                                                        <td class="middle" id="st_monthName"></td>
	                                                                        <td class="arrow_right"><img class="calendar_switcher" id="st_nextMonthSwitcher" alt="<?php echo AltNextMonth; ?>" title="<?php echo AltNextMonth; ?>" src="./calendar/skins/calendar/minicalendar_arrow_right_activ.gif" /></td>
	                                                                    </tr>
	                                                                </table>
	                                                                <table id="st_calendarBlock" class="calendar_block" cellpadding="0" cellspacing="0">
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
																	<input id="st_currentMonth" name="st_currentMonth" type="hidden" />
																</div>
															</td>
														</tr>
													</table>
													<table border="0" cellpadding="0" cellspacing="0" style="float:left;">
	                                                    <tr>
	                                                        <td class="row_title" style="text-align:center; float:none; width:36px;"><?php echo EventTill;?></td>
	                                                        <td style="width:60px">
	                                                            <input id="EventTimeTill" name="EventTimeTill" class="input" style="display:block;width:60px; text-align: center; margin-right:4px;padding:0px" value="" tabIndex="4" />
	                                                            <div style="position:absolute;" id="EventTimeTill_dropdown"></div>
	                                                        </td>
	                                                        <td style="width:84px">
	                                                            <input id="EventDateTill" name="EventDateTill" class="input" style="display:block; width:84px; text-align: center;padding:0px" value="" tabIndex="5" />
	                                                            <div style="position:absolute;overflow:visible;margin-top:-1px;" id="en">
	                                                                <table class="Calendar_Title">
	                                                                    <tr>
	                                                                        <td class="arrow_left"><img class="calendar_switcher" id="en_prevMonthSwitcher" alt="<?php echo AltPrevMonth; ?>" title="<?php echo AltPrevMonth; ?>" src="./calendar/skins/calendar/minicalendar_arrow_left_activ.gif" /></td>
	                                                                        <td class="middle" id="en_monthName"></td>
	                                                                        <td class="arrow_right"><img class="calendar_switcher" id="en_nextMonthSwitcher" alt="<?php echo AltNextMonth; ?>" title="<?php echo AltNextMonth; ?>" src="./calendar/skins/calendar/minicalendar_arrow_right_activ.gif" /></td>
	                                                                    </tr>
	                                                                </table>
	                                                                <table id="en_calendarBlock" class="calendar_block" cellpadding="0" cellspacing="0">
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
	                                                                <input id="en_currentMonth" name="en_currentMonth" type="hidden" />
	                                                            </div>
	                                                        </td>
	                                                    </tr>
	                                                </table>
	                                                <div class="em_spacer"></div>
	                                                <div class="row_title"><?php echo CalendarDescription;?></div>
	                                                <div><textarea name="EventDescription" id="EventDescription" class="input" style="background-color:white; color:#696969; width:338px; height:4em;" tabIndex="6"></textarea></div>
	                                                <div class="em_spacer"></div>
	                                                <div id="repeatExclusionWarning" style="color:#696969;">
	                                                    <div class="row_title"></div>
	                                                    <div><?php echo RepeatEventNotPartOfASeries;?> <a href="javascript:void(0)" style="color:#7E9BAF" id="undoOneRepeat"><?php echo UndoRepeatExclusion;?></a></div>
	                                                </div>
													<div class="em_spacer"></div>
													<div id="expandRepeatLinkCont">
	   													<a href="javascript:void(0)" class="link1 row_offset" id="expandRepeatLink"><?php echo RepeatEvent; ?></a>
	                                                </div>
	                                                <div id="repeatCont" class="repeatCont eventcontainer_gray" style="width:408px;">
	                                                    <div class="a"></div>
	                                                    <div class="b"></div>
	                                                    <div class="event_middle">
	                                                        <div class="event_text">
	                                                            <img id="closeRepeatForm" class="closeRepeatForm" src="./calendar/skins/calendar/close-popup.png" alt="<?php echo CancelRecurrence; ?>" title="<?php echo CancelRecurrence; ?>" />
																<ul class="form_string nolist">
	                                                                <li class="row_offset"><div class="repeatResult" id="repeatResult"></div></li>
	                                                                <li>
	                                                                    <div class="row_title"><?php echo EventRepeats;?></div>
	                                                                    <select id="repeatOpt" name="repeatOpt" class="input">
																			<option value="0"><?php echo DailyRepeats;?></option>
																			<option value="1"><?php echo WeeklyRepeats;?></option>
																			<option value="2"><?php echo MonthlyRepeats;?></option>
																			<option value="3"><?php echo YearlyRepeats;?></option>
																		</select>
																	</li>
																	<li id="weekdaysRepeatChbk" class="weekdaysRepeatChbk" style="position:relative; background-color:#fff;">
	                                                                    <div class="row_title"><?php echo OnDays;?></div>
	                                                                    <div>
	                                                                        <input type="checkbox" id="weekDay[0]" name="weekDay" class="wm_checkbox" value="0" /><label for="weekDay0"><?php echo DayToolSunday; ?></label>&nbsp;
	                                                                        <input type="checkbox" id="weekDay[1]" name="weekDay" class="wm_checkbox" value="1" /><label for="weekDay1"><?php echo DayToolMonday; ?></label>&nbsp;
	                                                                        <input type="checkbox" id="weekDay[2]" name="weekDay" class="wm_checkbox" value="2" /><label for="weekDay2"><?php echo DayToolTuesday; ?></label>&nbsp;
	                                                                        <input type="checkbox" id="weekDay[3]" name="weekDay" class="wm_checkbox" value="3" /><label for="weekDay3"><?php echo DayToolWednesday; ?></label>&nbsp;
	                                                                        <input type="checkbox" id="weekDay[4]" name="weekDay" class="wm_checkbox" value="4" /><label for="weekDay4"><?php echo DayToolThursday; ?></label>&nbsp;
	                                                                        <input type="checkbox" id="weekDay[5]" name="weekDay" class="wm_checkbox" value="5" /><label for="weekDay5"><?php echo DayToolFriday; ?></label>&nbsp;
	                                                                        <input type="checkbox" id="weekDay[6]" name="weekDay" class="wm_checkbox" value="6" /><label for="weekDay6"><?php echo DayToolSaturday; ?></label>
	                                                                    </div>
	                                                                </li>
	                                                                <li id="advancedRepeatCont1">
	                                                                    <div class="row_title"></div>
	                                                                    <div class="select_box" style="z-index:2;">
	                                                                        <div style="position:relative;width:100%">
	                                                                            <div class="advanced_repeat" id="advanced_repeat1">
	                                                                                <div class="a_round_left"></div>
	                                                                                <div class="b_round_left"></div>
	                                                                                <div class="event_middle"><div class="event_text">
	                                                                                        <div id="repeatOnDay"></div>
	                                                                                        <div id="repeatWeekDays">
	                                                                                            <?php echo Every;?>
	                                                                                            <select id="weekOrderSelect">
	                                                                                                <option value="0"><?php echo First; ?></option>
	                                                                                                <option value="1"><?php echo Second; ?></option>
	                                                                                                <option value="2"><?php echo Third; ?></option>
	                                                                                                <option value="3"><?php echo Fourth; ?></option>
	                                                                                                <option value="4"><?php echo Last; ?></option>
	                                                                                            </select>
	                                                                                            <select id="weekdaysRepeatSel">
	                                                                                                <option value="0"><?php echo FullDaySunday; ?></option>
	                                                                                                <option value="1"><?php echo FullDayMonday; ?></option>
	                                                                                                <option value="2"><?php echo FullDayTuesday; ?></option>
	                                                                                                <option value="3"><?php echo FullDayWednesday; ?></option>
	                                                                                                <option value="4"><?php echo FullDayThursday; ?></option>
	                                                                                                <option value="5"><?php echo FullDayFriday; ?></option>
	                                                                                                <option value="6"><?php echo FullDaySaturday; ?></option>
	                                                                                            </select>
	                                                                                            <span id="yearDate" style="vertical-align:middle;"></span>
	                                                                                        </div>
																						</div>
																					</div>
																					<div class="b_round_left"></div>
																					<div class="a_round_left"></div>
																				</div>
																				<div class="arrow">
	                                                                                <div class="a_round_right"></div>
	                                                                                <div class="b_round_right"></div>
	                                                                                <div class="event_middle"><div class="event_text" id="advanced_repeat_arrow1"></div></div>
	                                                                                <div class="b_round_right"></div>
	                                                                                <div class="a_round_right"></div>
	                                                                            </div>
	                                                                        </div>
	                                                                        <div id="advanced_list1"  class="select_box_list"  style="z-index:2;">
	                                                                            <div class="a qmenu1"></div>
	                                                                            <div class="b qmenu1"></div>
	                                                                            <div class="event_middle"><div class="event_text">
	                                                                                    <iframe scrolling="no" frameborder="0" style="position: absolute; top: 0; z-index:1; left: 0; width: 100%; height:30px; filter:alpha(opacity=0)"></iframe>
	                                                                                    <div id="repeatMonthOpt0" class="select_box_opt" style="z-index:2; position:relative;"></div>
	                                                                                    <div id="repeatMonthOpt1" class="select_box_opt" style="z-index:2; border-bottom:0;">
	                                                                                        <?php echo Every; ?>&nbsp;
	                                                                                        <select id="weekOrderSelect1" disabled="disabled">
	                                                                                            <option value="0"><?php echo First; ?></option>
	                                                                                            <option value="1"><?php echo Second; ?></option>
		                                                                                        <option value="2"><?php echo Third; ?></option>
	                                                                                            <option value="3"><?php echo Fourth; ?></option>
	                                                                                            <option value="4"><?php echo Last; ?></option>
	                                                                                        </select>
	                                                                                        <select id="weekdaysRepeatSel1" disabled="disabled">
	                                                                                            <option value="0"><?php echo FullDaySunday; ?></option>
	                                                                                            <option value="1"><?php echo FullDayMonday; ?></option>
	                                                                                            <option value="2"><?php echo FullDayTuesday; ?></option>
	                                                                                            <option value="3"><?php echo FullDayWednesday; ?></option>
	                                                                                            <option value="4"><?php echo FullDayThursday; ?></option>
	                                                                                            <option value="5"><?php echo FullDayFriday; ?></option>
	                                                                                            <option value="6"><?php echo FullDaySaturday; ?></option>
	                                                                                        </select>
	                                                                                        <span id="yearDate1" style="vertical-align:middle;"></span>
	                                                                                    </div>
		                                                                            </div></div>
			                                                                    <div class="b"></div>
				                                                                <div class="a"></div>
					                                                        </div>
						                                                </div>
							                                            <div class="em_spacer" style="height:0"></div>
								                                    </li>
									                                <li class="repeatsEvery" id="repeatOrderCont" style="position:relative;">
										                                <div class="row_title"><?php echo RepeatsEvery; ?></div>
											                            <select id="repeatOrder" name="repeatOrder">
												                            <option value="0">1</option>
													                        <option value="1">2</option>
														                    <option value="2">3</option>
															                <option value="3">4</option>
																            <option value="4">5</option>
																	        <option value="5">6</option>
																		    <option value="6">7</option>
																			<option value="7">8</option>
	                                                                        <option value="8">9</option>
		                                                                    <option value="9">10</option>
			                                                                <option value="10">11</option>
				                                                            <option value="11">12</option>
					                                                        <option value="12">13</option>
						                                                    <option value="13">14</option>
							                                                <option value="14">15</option>
								                                            <option value="15">16</option>
									                                        <option value="16">17</option>
										                                    <option value="17">18</option>
											                                <option value="18">19</option>
												                            <option value="19">20</option>
													                        <option value="20">21</option>
														                    <option value="21">22</option>
															                <option value="22">23</option>
																            <option value="23">24</option>
																	        <option value="24">25</option>
																		    <option value="25">26</option>
																			<option value="26">27</option>
	                                                                        <option value="27">28</option>
		                                                                    <option value="28">29</option>
			                                                                <option value="29">30</option>
				                                                        </select>
						                                                    &nbsp;<span id="repeatIntervalText"></span>
								                                    </li>
							                                        <li id="repeatExpiresCont" style="height:auto">
																		<div class="row_title" style="line-height:1.9em;"><?php echo SetRepeatEventEnd;?></div>
																		<div class="select_box" style="z-index:1; ">
	                                                                        <div style="position:relative; width:100%;">
	                                                                            <div class="advanced_repeat" id="advanced_repeat2">
		                                                                            <div class="a_round_left"></div>
			                                                                        <div class="b_round_left"></div>
				                                                                    <div class="event_middle"><div class="event_text">
					                                                                        <div id="advanced_repeat_text21"><?php echo NoEndRepeatEvent;?></div>
						                                                                    <div id="advanced_repeat_text22">
							                                                                    <?php echo EndRepeatEventAfter;?>&nbsp;<input type="text" id="repeatTimes" maxlength="3" class="input" style="width:30px;" />&nbsp;<?php echo Occurrences;?>
								                                                            </div>
									                                                        <div id="advanced_repeat_text23">
										                                                        <?php echo EndRepeatEventBy;?>&nbsp;<input type="text" class="input" id="repeatUntil" style="width:80px;" />
											                                                </div>
												                                        </div></div>
													                                <div class="b_round_left"></div>
														                            <div class="a_round_left"></div>
															                    </div>
																                <div class="arrow">
																	                <div class="a_round_right"></div>
																		            <div class="b_round_right"></div>
																			        <div class="event_middle"><div class="event_text" id="advanced_repeat_arrow2">&nbsp;</div></div>
																				    <div class="b_round_right"></div>
																					<div class="a_round_right"></div>
																				</div>
																			</div>
	                                                                        <div id="calRepeatUntil">
	                                                                            <table class="Calendar_Title">
		                                                                            <tr>
			                                                                            <td class="arrow_left"><img class="calendar_switcher" id="calRepeatUntil_prevMonthSwitcher" alt="<?php echo AltPrevMonth; ?>" title="<?php echo AltPrevMonth; ?>" src="./calendar/skins/calendar/minicalendar_arrow_left_activ.gif" /></td>
				                                                                        <td class="middle" id="calRepeatUntil_monthName"></td>
					                                                                    <td class="arrow_right"><img class="calendar_switcher" id="calRepeatUntil_nextMonthSwitcher" alt="<?php echo AltNextMonth; ?>" title="<?php echo AltNextMonth; ?>" src="./calendar/skins/calendar/minicalendar_arrow_right_activ.gif" /></td>
						                                                            </tr>
							                                                    </table>
								                                                <table id="calRepeatUntil_calendarBlock" class="calendar_block" cellpadding="0" cellspacing="0">
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
																				<input id="calRepeatUntil_currentMonth" name="calRepeatUntil_currentMonth" type="hidden" />
	                                                                        </div><!-- calendarRepeatUntil -->
		                                                                    <div id="advanced_list2"  class="select_box_list">
			                                                                    <div class="a qmenu1"></div>
				                                                                <div class="b qmenu1"></div>
					                                                            <div class="event_middle"><div class="event_text">
						                                                                <div id="repeatExpire0" class="select_box_opt"><?php echo NoEndRepeatEvent;?></div>
							                                                            <div id="repeatExpire1" class="select_box_opt">
								                                                            <?php echo EndRepeatEventAfter;?>&nbsp;<input id="repeatTimesDisabled" type="text" value="2" maxlength="3" class="input" style="width:30px;" disabled="disabled" />&nbsp;<?php echo Occurrences;?>
									                                                    </div>
										                                                <div id="repeatExpire2" class="select_box_opt" style="border-bottom:0;">
											                                                <?php echo EndRepeatEventBy;?>&nbsp;<input type="text" class="input" style="width:80px;" disabled="disabled" />
												                                        </div>
													                                </div></div>
														                        <div class="b"></div>
															                    <div class="a"></div>
																            </div>
																	    </div>
																		<div class="em_spacer" style="height:0"></div>
	                                                                </li>
		                                                        </ul>
			                                                    <div class="em_spacer"></div>
				                                            </div>
					                                    </div>
						                                <div class="b"></div>
							                            <div class="a"></div>
								                    </div>
									                <div class="em_spacer"></div>
													<div id="expandRemindLinkCont" style="padding-top: 2px; display:none;">
											            <a href="javascript:void(0)" class="link1 row_offset" id="expandRemindLink"><?php echo AddReminder; ?></a>
												    </div>
													<div id="remindCont" style="display:none;">
														<div class="row_offset" style="padding-bottom:3px;">
	                                                        <div id="remindParent" style="position: relative;"></div>
		                                                    <div class="remind_line" id="moreRemindLinkCont"><a id="moreRemindLink" href="javascript:void(0)" class="link1"><?php echo AddMoreReminder;?></a></div>
			                                            </div>
				                                    </div>
													<div class="em_spacer"></div>
						                            <div id="expandAppointmentLinkCont" class="row_offset">
														<div class="b"><?php echo AppointmentAddGuests; ?></div>
														<a href="javascript:void(0)" class="link1" id="expandAppointmentLink"><?php echo AppointmentAddGuests; ?></a>
														<div class="b bottom"><?php echo AppointmentAddGuests; ?></div>
													</div>
													<div class="em_spacer" style="height:1.5em;"></div>
												</div>
												<div class="em_spacer" style="height:1.5em;"></div>
											</div>
											<div id="eventRightContainer" class="appointments" style="float:right; width:315px; padding: 0px 5px;">
												<div class="em_spacer"></div>
			                                    <div id="appointmentCont" style="height:300px; vertical-align:top; overflow:auto; padding-left:10px; margin-top:5px;">
													<div class="subtitle" id="yesLinesOpener">
														<div class="lines_open_mode" id="yesArrow"></div><?php echo AppointmentParticipants;?> <span id="yesCount" class="count"></span>
													</div>
													<div id="linesYes" ></div>
													<div class="subtitle" id="maybeLinesOpener" style="margin-top:5px;">
														<div class="lines_open_mode" id="maybeArrow"></div><?php echo AppointmentRespondMaybe;?> <span id="maybeCount" class="count"></span>
													</div>
													<div id="linesMaybe" ></div>
													<div class="subtitle" id="awaitingLinesOpener" style="margin-top:5px;">
														<div class="lines_open_mode" id="awaitingArrow"></div><?php echo AppointmentAwaitingResponse;?> <span id="awaitingCount" class="count"></span>
													</div>
													<div id="linesAwaiting"></div>
													<div class="subtitle" id="noLinesOpener" style="margin-top:5px;">
														<div class="lines_close_mode" id="noArrow"></div><?php echo AppointmentRefused;?> <span id="noCount" class="count"></span>
													</div>
													<div id="linesNo"></div>
												</div>
												<div id="appointmentGuestsRightsCont" style="margin:5px 0;">
													<input type="checkbox" id="appointmentGuestsRights" style="vertical-align:middle;" /><label for="appointmentGuestsRights" style="vertical-align:middle;"><?php echo AppointmentGuestsChangeEvent; ?></label>
												</div>
												<div id="appointmentRespondCont" class="appointment_respond" style="text-align:center; display:none;">
													<div id="appointmentRespond" style="text-indent: 0px; text-align: center; margin-bottom: 3px;"></div>
													<span id="appointmentChangeRespond1" class="appointment_respond_change"></span>
													<span id="appointmentChangeRespond2" class="appointment_respond_change"></span>
													<span id="appointmentChangeRespond3" class="appointment_respond_change"></span>
												</div>
												<div id="appointmentAddGuestsCont" style="vertical-align:bottom; padding-top: 5px;">
						                            <div id="closeAppointmentLinkCont" style="margin-right:10px">
							                            <a href="javascript:void(0)" class="link1" id="closeAppointmentLink" style="font-weight:bold; float:right;"><?php echo AppointmentRemoveGuests; ?></a>
									                </div>
													<div class="em_spacer" style="height:10px;"></div>
													<div style="margin-bottom: 3px; margin-left:5px;"><?php echo AppointmentListEmails;?></div>
													<textarea name="newGuests" id="newGuests" class="input" tabIndex="5" cols="0" rows="0"></textarea>
												</div>
												<div class="em_spacer" style="height:1.5em;"></div>
											</div>
											<div class="clear"></div>
											<div class="buttons" id="eventButtons">
												<input type="button" id="closebut" value="<?php echo ButtonClose;?>" class="wm_button" tabIndex="9" />
												<input type="button" id="button_save" value="<?php echo ButtonSave;?>" class="wm_button" tabIndex="7" />&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="button" id="cancelbut" value="<?php echo ButtonCancel;?>" class="wm_button normal" tabIndex="8" />&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="button" name="delbut" id="delbut" value="<?php echo ButtonDelete;?>" class="wm_button normal" tabIndex="9" />
											</div>
											<div class="clear"></div>
										</div>
		                            </div>
			                        <div class="b"></div>
					                <div class="a"></div>
				                </div>
						    </div>
						</div>
					</div>
				</div><!--#edit_window-->

	            <div id="manager_window">
                    <div class="mask"></div>
                    <!-- manager form -->
                    <div id="manager_form" class="edit_form">
                        <div style="height:auto; position:relative;left:-225px; top: -125px;">
                            <input type="hidden" name="clndform_id" id="clndform_id" value="0" />
                            <input type="hidden" id="calendarColorNumber" value="0"/>

                            <div class="event_black">
                                <div class="a"></div>
                                <div class="b"></div>
                                <div class="event_middle" id="manager_shadow"></div>
                                <div class="b"></div>
                                <div class="a"></div>
                            </div>
                            <div class="eventcontainer_2" id="calendarcontainer" style="position:relative">
                                <div class="event_edit">
                                    <div class="a"></div>
                                    <div class="b"></div>
                                    <div class="event_middle">
                                        <div class="event_text">
                                            <div class="time" style="text-indent:92px;" id="ef_fulldate_calendar"><?php echo CalendarHeaderNew;?></div>
                                            <div class="text">
                                                <div class="em_spacer"></div>
                                                <div class="row_title" style="width:8em"><?php echo CalendarName;?></div>
                                                <div><input id="CalendarSubject" name="CalendarSubject" maxlength="50" class="input" style="width:320px;" tabIndex="1" /></div>
                                                <div class="em_spacer"></div>
                                                <div class="row_title" style="width:8em"><?php echo CalendarDescription;?></div>
                                                <div><textarea name="CalendartDescription" id="CalendarDescription" class="input" style="width:320px; height:50px;" tabIndex="2"></textarea></div>
                                                <div class="em_spacer"></div>
                                                <div class="row_title" style="width:8em"><?php echo CalendarColor;?></div>
                                                <div class="calendar_text input" style="width:105px; height: 35px; padding:0;">
                                                    <div id="color_1" class="color_pick" style="background-color: #ef9554;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(1, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>

                                                    <div id="color_2" class="color_pick" style="background-color: #f58787;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(2, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>

                                                    <div id="color_3" class="color_pick" style="background-color: #6fd0ce;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(3, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>

                                                    <div id="color_4" class="color_pick" style="background-color: #90bbe0;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(4, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>

                                                    <div id="color_5" class="color_pick" style="background-color: #baa2f3;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(5, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>

                                                    <div id="color_6" class="color_pick" style="background-color: #f68bcd;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(6, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>

                                                    <div id="color_7" class="color_pick" style="background-color: #d987da;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(7, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>

                                                    <div id="color_8" class="color_pick" style="background-color: #4affb8;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(8, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>

                                                    <div id="color_9" class="color_pick" style="background-color: #9f9fff;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(9, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>

                                                    <div id="color_10" class="color_pick" style="background-color: #5cc9c9;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(10, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>

                                                    <div id="color_11" class="color_pick" style="background-color: #76cb76;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(11, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>

                                                    <div id="color_12" class="color_pick" style="background-color: #aec9c9;"
                                                         onclick="CalendarForm.SelectColorForNewCalendar(12, this);"
                                                         onmouseover="this.className='color_pick_hover'"
                                                         onmouseout="this.className='color_pick'"></div>
                                                </div>
                                                <div class="buttons">
													<input type="button" id="savebut_calendar" value="<?php echo ButtonSave;?>" class="wm_button normal" tabIndex="8" />&nbsp;&nbsp;&nbsp;&nbsp;
													<input type="button" id="calncelbut_calendar" value="<?php echo ButtonCancel;?>" class="wm_button normal" tabIndex="9" />&nbsp;&nbsp;&nbsp;&nbsp;
													<input type="button" name="delbut" id="delbut_calendar" value="<?php echo ButtonDelete;?>" class="wm_button normal" tabIndex="10" />
												</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="b"></div>
                                    <div class="a"></div>
                                </div><!--#event_edit-->
                            </div>
                        </div>
                    </div><!--#manager_form-->
                </div><!--#manager_window-->

                <div id="repeat_choose" style="display:none;">
                    <div class="mask"></div>
                    <div id="repeat_choose_form" class="edit_form">
                        <div style="height:auto; position:relative;left:-300px; top: -125px;">
                            <div class="event_black">
                                <div class="a"></div>
                                <div class="b"></div>
                                <div class="event_middle" id="repeat_chose_shadow"></div>
                                <div class="b"></div>
                                <div class="a"></div>
                            </div>
                            <div class="eventcontainer_2" id="choosecontainer" style="position:relative">
                                <div class="event_edit">
                                    <div class="a"></div>
                                    <div class="b"></div>
                                    <div class="event_middle">
                                        <div class="event_text">
                                            <div class="time" id="ef_fulldate"><?php echo RepeatEventHeaderEdit; ?></div>
                                            <div class="text" style="margin: 0 5px; width:98%; text-align:justify;">
                                                <div class="em_spacer"></div>
                                                <div style="color:#696969;"><?php echo ConfirmEditRepeatEvent;?></div>
                                                <div class="em_spacer"></div>
												<div class="buttons" style="padding-bottom:0;">
		                                            <input type="button" id="savebut_one" value="<?php echo ThisInstance; ?>" class="wm_button normal" tabIndex="1" />
	                                                <input type="button" id="savebut_all" value="<?php echo AllEvents ;?>"  class="wm_button normal" tabIndex="2" />
													<input type="button" id="savebut_next" value="<?php echo AllFollowing; ?>" style="display:none;"  class="wm_button normal" tabIndex="3" />
													<input type="button" id="cancelbut_repeat1" value="<?php echo ButtonCancel;?>" class="wm_button normal" tabIndex="4" />
												</div>
                                                <div class="em_spacer"></div>
                                                <br />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="b"></div>
                                    <div class="a"></div>
                                </div><!--#event_edit-->
                            </div>
                        </div>
                    </div><!--#-->
                </div>

                <div id="confirm_window">
                    <div class="mask"></div>
                </div>

                <div id="share_window">
                    <div class="mask"></div>
                    <div id="share_form" class="edit_form">
                        <div style="height:auto; position:relative;left:-225px; top: -125px;">
                            <input type="hidden" id="publicationHash" value="" />
                            <input type="hidden" id="iCalHash" value="" />
                            <input type="hidden" id="shareform_id" value="0" />

                            <div class="event_black">
                                <div class="a"></div>
                                <div class="b"></div>
                                <div class="event_middle" id="share_shadow"></div>
                                <div class="b"></div>
                                <div class="a"></div>
                            </div>
                            <div class="eventcontainer_2" id="sharecontainer" style="position:relative">
                                <div class="event_edit">
                                    <div class="a"></div>
                                    <div class="b"></div>
                                    <div class="event_middle">
                                        <div class="event_text">
                                            <div class="time" style="text-indent:92px;"><?php echo ShareHeaderEdit;?></div>
                                            <div class="text">

                                                <div class="em_spacer"></div>
                                                <div class="edit_lightgray" style="padding:0 0.5em 0 0.4em;">
                                                    <div class="a"></div>
                                                    <div class="b"></div>
                                                    <div class="event_middle wm_hide" style="padding:0.5em 0 0.5em 0.3em ;">
                                                        <div class="row_title" style="width:7.4em">Principal URL:</div>
                                                        <div class="inline_string">
														<textarea id="principalUrl" class="input link_container" rows="2" tabIndex="4" readonly></textarea>
														</div>
                                                    </div>
                                                    <div class="event_middle" style="padding:0.5em 0 0.5em 0.3em ;">
                                                        <div class="row_title" style="width:7.4em">Calendar URL:</div>
                                                        <div class="inline_string">
                                                        <textarea id="calendarUrl" class="input link_container" rows="2" tabIndex="4" readonly></textarea>
														</div>
                                                    </div>
                                                    <div class="b"></div>
                                                    <div class="a"></div>
                                                </div>
												
                                                <div class="em_spacer" style="height:1.5em"></div>
                                                <div class="edit_lightgray" style="padding:0 0.5em 0 0.4em;">
                                                    <div class="a"></div>
                                                    <div class="b"></div>
                                                    <div class="event_middle" style="padding:0.5em 0 0.5em 0.3em ;">
                                                        <div class="row_title" style="width:7.4em"></div>
                                                        <div class="inline_string">
                                                            <input type="checkbox" id="publicateCalendar" name="publicateCalendar" value="1" tabIndex="3" class="wm_checkbox" />
                                                            <label for="publicateCalendar"><?php echo CalendarPublicate; ?></label>
                                                        </div>

                                                        <div id="publicationLevelContainer">
                                                            <div class="em_spacer"></div>
                                                            <div class="row_title" style="width:7.4em"></div>
                                                            <div class="wm_hide">
                                                                <select id="publicationLevel" name="publicationLevel" class="input">
                                                                    <option value="1"><?php echo SharePermission3; ?></option>
                                                                    <option value="2"><?php echo SharePermission4; ?></option>
                                                                </select>
                                                            </div>
                                                            <div class="em_spacer"></div>
                                                            <div class="row_title" style="width:7.4em"><?php echo CalendarPublicationLink; ?></div>
                                                            <div>
                                                                <textarea id="publicationUrl" class="input link_container" rows="2" tabIndex="4" readonly></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="em_spacer" style="height:1.5em; display: none;"></div>

                                                        <div class="row_title" style="width:7.4em"></div>
                                                        <div class="inline_string" style="display: none;">
                                                            <input type="checkbox" id="iCalAllow" name="iCalAllow" value="1" tabIndex="3" class="wm_checkbox" />
                                                            <label for="iCalAllow"><?php echo ExportToICalendar; ?></label>
                                                        </div>
                                                        <div id="iCalContainer">
                                                            <div class="em_spacer"></div>
                                                            <div class="row_title" style="width:7.4em"><?php echo CalendarPublicationLink; ?></div>
                                                            <div>
                                                                <textarea id="iCalUrl" class="input link_container" rows="2" tabIndex="4" readonly></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="b"></div>
                                                    <div class="a"></div>
                                                </div>

                                                <div class="em_spacer" style="height:1.5em"></div>
                                                <div class="edit_lightgray" style="padding:0 0.5em 0 0.4em;">
                                                    <div class="a"></div>
                                                    <div class="b"></div>
                                                    <div class="event_middle" id="share_middle" style="padding:0.3em 0 0.3em 0.3em ;">
                                                        <div class="row_title" style="width:7.4em"></div><div style="color:#696969; padding-bottom:0.5em;"><?php echo ShareCalendar; ?></div>
                                                        <div id="shareParent" style="display:inline;"></div>
                                                        <div class="share">
                                                            <div class="col1">
                                                                <input type="text" id="sharedEmail" name="sharedEmail" maxlength="100" value="<?php echo SharedTitleEmail; ?>" class="input" style="color:#aaa;" tabIndex="5" />
                                                            </div>
                                                            <div class="col2">
                                                                <select id="sharedPermission" name="sharedPermission" tabIndex="6" class="input">
                                                                    <!--option value="0"><?php echo SharePermission1; ?></option-->
                                                                    <option value="1"><?php echo SharePermission2; ?></option>
                                                                    <option value="2" selected="selected"><?php echo SharePermission3; ?></option>
                                                                    <!--option value="3"><?php echo SharePermission4; ?></option-->
                                                                </select>
                                                            </div>
                                                            <div class="col3"><img src="./calendar/skins/calendar/add.png" class="imgbtn" alt="<?php echo JS_LANG_Add; ?>" title="<?php echo JS_LANG_Add; ?>" id="addShare" /></div>
                                                            <div class="em_spacer" style="height:0.3em;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="b"></div>
                                                    <div class="a"></div>
                                                </div>
                                                <div class="buttons">
													<input type="button" id="share_save" value="<?php echo ButtonSave;?>" class="wm_button" tabIndex="8" />&nbsp;&nbsp;&nbsp;&nbsp;
													<input type="button" id="share_cancel" value="<?php echo ButtonCancel;?>" class="wm_button normal" tabIndex="9" />&nbsp;&nbsp;&nbsp;&nbsp;
                                                </div>
                                            </div><!--text-->
                                        </div>
                                    </div>
                                    <div class="b"></div>
                                    <div class="a"></div>
                                </div><!--#event_edit-->
                            </div><!--eventcontainer_2-->
                        </div>
                    </div><!--share_form-->
                </div><!--share_window-->
            </div><!--#wm_content-->
        </span>
    </body>
</html>