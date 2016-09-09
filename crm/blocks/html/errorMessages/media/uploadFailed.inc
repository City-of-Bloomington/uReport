<p><?= $this->_('media/uploadFailed', 'errors'); ?></p>
<?php
    switch ($e->getCode()) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            echo "<p>{$this->_('media/fileTooBig', 'errors')}</p>";
        break;

        case UPLOAD_ERR_PARTIAL:
        case UPLOAD_ERR_NO_FILE:
            echo "<p>{$this->_('media/networkProblem', 'errors')}</p>";
        break;

        default:
            echo "<p>{$this->('media/unknownProblem', 'errors')}</p>";
    }
?>