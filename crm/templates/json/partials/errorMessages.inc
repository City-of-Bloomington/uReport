<?php
/**
 * @copyright 2007-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
if (isset($_SESSION['errorMessages'])) {
    $errors   = [];
    $errorDir = "/blocks/json/errorMessages";
    foreach ($_SESSION['errorMessages'] as $e) {
        ob_start();
        $error = $e->getMessage();
        $file = "$errorDir/$error.inc";

        if ($this->theme
            && is_file($this->theme.$file)) {
            include    $this->theme.$file;
        }
        elseif (is_file(APPLICATION_HOME.$file)) {
            include     APPLICATION_HOME.$file;
        }
        else {
            echo "{\"description\":\"$error\"}";
        }
        $errors[] = ob_get_clean();
    }
    echo '['.implode(',',$errors).']';
}
