<?php
header('Content-type: text/json; charset=utf-8');
$this->_include('partials/errorMessages.inc');
echo $this->includeBlocks();
