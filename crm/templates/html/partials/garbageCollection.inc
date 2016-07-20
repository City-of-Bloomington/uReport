<?php
//---------------------------------------------------------------------
// Before we quit, handle any Garbage Collection that needs to be done
//---------------------------------------------------------------------
// If we're on a form, clean out any old data
if (isset($_SESSION['oldPostData'])) {
	unset($_SESSION['oldPostData']);
}

# Calculate the process time
global $startTime;
$endTime = microtime(1);
$processTime = $endTime - $startTime;
echo "<!-- Process Time: $processTime -->";
