<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->groups An array of group names
 */
?>
<div class="groups.inc">
	<h1>Category Groups</h1>
	<ul>
	<?php
	foreach ($this->groups as $group) {
		$group = View::escape($group);
		echo "<li>$group</li>";
	}
	?>
	</ul>
</div>