<?php
include FRAMEWORK.'/errorMessages.php';
if (false !== strpos($_GET['partial'],'panel')) {
	echo $this->includeBlocks($_GET['partial']);
}
else {
	echo $this->includeBlocks();
}