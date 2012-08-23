var alertType = "$var['jsalert']";

var alerts = new Array();

alerts['csv_import_bad_filetype'] = "Ungültiger Dateityp. Bitte importieren Sie ausschliesslich .csv und .txt Dateien";
alerts['csv_import_file_oversize'] = "Die importierte Datei ist zu gross. Versuchen Sie es stattdessen mit mehreren kleineren Dateien.";
alerts['csv_import_failed'] = "Der Import ist fehlgeschlagen.";
alerts['csv_import_file_empty'] = "Die importierte Datei ist leer.";

if (alertType) {
    alert(alerts[alertType]);
}
