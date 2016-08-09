<p>The file you attempted to upload never made it to the server.</p>
<?php
    switch ($e->getCode()) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            echo "<p>Your file was too big</p>";
        break;

        case UPLOAD_ERR_PARTIAL:
        case UPLOAD_ERR_NO_FILE:
            echo "<p>There was a network communication problem.</p>";
        break;

        default:
            echo "<p>There was a problem with the server.  You might try contacting the server admin.</p>";
    }
?>