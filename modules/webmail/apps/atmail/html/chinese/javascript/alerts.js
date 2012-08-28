var alertType = "$var['jsalert']";

var alerts = new Array();

alerts['csv_import_bad_filetype'] = "不正確的檔案-請只匯入.csv或.文本（純文字文件）";
alerts['csv_import_file_oversize'] = "進口文件太大-嘗試在進口階段";
alerts['csv_import_failed'] = "進口失敗";
alerts['csv_import_file_empty'] = "進口文件是空的";

if (alertType) {
    alert(alerts[alertType]);
}
