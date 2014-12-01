<?php

print '<footer>';

$today = date("F j, Y");

// this is needed since the the format we display is not considered valid for the time element
$todayDateValue = date("Y-m-d");

print '<p><b>Today is: <time datetime="' . $todayDateValue . '">' . $today . "</time></b></p>\n";
print '</footer>';
?>