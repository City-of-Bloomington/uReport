<?php
header('Content-type: text/json; charset=utf-8');
include BLOSSOM.'/errorMessages.php';
echo $this->includeBlocks();
