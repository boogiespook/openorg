<?php
function connectDB() {
## Database stuff
global $db;
$db = ($GLOBALS["___mysqli_ston"] = mysqli_connect('localhost', 'root', 'XXXXXXXXX'));
	if (!$db) {
    printf("Errormessage: %s\n", mysqli_error($db));
	die("Unable to connect to database");
	}
if (!mysqli_select_db($GLOBALS["___mysqli_ston"], 'readytoi_spider')) {
		die("Unable to access spider database");
	}
}
?>
