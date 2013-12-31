<?php
include('../../../cnf/db.php');
include('../../../dryden/db/driver.class.php');
include('../../../dryden/debug/logger.class.php');
include('../../../dryden/runtime/dataobject.class.php');
include('../../../dryden/sys/versions.class.php');
include('../../../dryden/ctrl/options.class.php');
include('../../../dryden/ctrl/auth.class.php');
include('../../../dryden/ctrl/users.class.php');
include('../../../dryden/fs/director.class.php');
include('../../../inc/dbc.inc.php');
session_start();
if (isset($_SESSION['zpuid'])) {
    $userid = $_SESSION['zpuid'];
    $currentuser = ctrl_users::GetUserDetail($userid);
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
            <title>ZPanel &gt; Run Daemon</title>
            <link href="../../../etc/styles/<?php echo $currentuser['usertheme']; ?>/css/<?php echo $currentuser['usercss']; ?>.css" rel="stylesheet" type="text/css">
            <script src="../assets/ajaxsbmt.js" type="text/javascript"></script>
            <script src="//code.jquery.com/jquery-latest.js"></script>
        <body style="background: #F3F3F3; font-size:12px">
            <div style="margin-left:20px;margin-right:20px;">
                <h2>ZPanel Daemon</h2>
                <div id="RunSubmit" style="height:100%;margin:auto;">
                    <p>Please note that depending on your configuration, running the daemon may take a lot of time.  Be patient until the script is complete and the results are displayed.</p>
                    <form>
                        <button class="fg-button ui-state-default ui-corner-all" id="SubmitRun" type="submit" value="">Run Now</button>
                    </form>
                </div>
                <div id="RunResult" style="display:block;height:100%;margin:auto;"></div>

            </div>
        </body>
    </html>
    <?php } else {
    ?>
    <body style="background: #F3F3F3;">
        <h2>Unauthorized Access!</h2>
        You have no permission to view this module.
    </body>
<?php } ?>

<script type="text/javascript">
jQuery(document).ready(function() { 
	jQuery("#SubmitRun").click(function(){
		event.preventDefault();
		jQuery("#RunSubmit").hide();
		jQuery("#RunResult").html("Running the daemon! Please wait...<br /><img src='../assets/bar.gif' />");
		jQuery.ajax({
			type: "POST",
			url: '/?module=zpanelconfig&action=RunDaemon'
		}).done(function (msg) {
			jQuery('#RunResult').fadeOut("slow", function() {
				jQuery('#RunResult').html(msg).fadeIn("slow");
			});
			
		});
	}); 
});
</script>