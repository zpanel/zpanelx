var alertType = "$var['jsalert']";

var alerts = new Array();

alerts['csv_import_bad_filetype'] = "Incorrect filetype - please import only .csv or .txt (plain text files)";
alerts['csv_import_file_oversize'] = "The import file is too large - try importing in stages";
alerts['csv_import_failed'] = "The import failed";
alerts['csv_import_file_empty'] = "The import file was empty";

if (alertType) {
    alert(alerts[alertType]);
}
