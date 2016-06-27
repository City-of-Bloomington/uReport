<?php
/**
 * @copyright 2007-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->errorMessages
 */
?>
<section id="errorMessages">
	<h1><?= $this->_('error'); ?></h1>
	<?php
        $errorDir = "/blocks/html/errorMessages";

		foreach ($this->errorMessages as $e) {
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
				echo "<p>{$this->_($error, 'errors')}</p>";
			}
		}
	?>
</section>