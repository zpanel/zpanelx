var alertType = "$var['jsalert']";

var alerts = new Array();

alerts['csv_import_bad_filetype'] = "$lang['lang_alerts_csv_import_bad_filetype']";
alerts['csv_import_file_oversize'] = "$lang['lang_alerts_csv_import_file_oversize']";
alerts['csv_import_failed'] = "$lang['lang_alerts_csv_import_failed']";
alerts['csv_import_file_empty'] = "$lang['lang_alerts_csv_import_file_empty']";

if (alertType) {
    alert(alerts[alertType]);
}
