<?php
/**
 * @copyright 2007-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->errorMessages
 */
?>
<section id="alert">
	<?php
        $errorDir = "/blocks/html/errorMessages";

		foreach ($this->errorMessages as $e) {
            echo "
            <article>
                <header>
                    <h1>{$this->_('error')}</h1>
                </header>
            ";
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

			echo "
			</article>
			";
		}
	?>
</section>