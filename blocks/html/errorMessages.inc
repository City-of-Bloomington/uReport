<?php
/**
 * @copyright 2007,2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->errorMessages
 */
?>
<div id="errorMessages">
	<h1>No That's wrong</h1>
	<?php
		foreach ($this->errorMessages as $e)
		{
			$error = $e->getMessage();
			if (file_exists(APPLICATION_HOME."/blocks/html/errorMessages/$error.inc"))
			{
				include APPLICATION_HOME."/blocks/html/errorMessages/$error.inc";
			}
			else
			{
				echo "<p>$error</p>";
			}
		}
	?>
</div>