<?php
//---------------------------------------------------------------------
// Before we quit, handle any Garbage Collection that needs to be done
//---------------------------------------------------------------------
// If we're on a form, clean out any old data
if (isset($_SESSION['oldPostData'])) {
	unset($_SESSION['oldPostData']);
}
