<?xml version="1.0" encoding="UTF-8"?>
<?php
include FRAMEWORK.'/errorMessages.php';
if (isset($_GET['partial'])) {
	echo $this->includeBlocks($_GET['partial']);
}
else {
	echo $this->includeBlocks();
}