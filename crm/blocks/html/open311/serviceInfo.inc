<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 */
$name = View::escape($this->category->getName());
echo "
<div class=\"serviceInfo\">
	<h1>$name Service</h1>
	<h2>{$this->category->getId()}</h2>
	<h3>Attributes</h3>
	<table>
		<thead>
			<tr><th>order</th>
				<th>code</th>
				<th>variable</th>
				<th>datatype</th>
				<th>required</th>
				<th>values</th>
				<th>description</th>
				<th>datatype_description</th>
			</tr>
		</thead>
		<tbody>
";
		$customFields = $this->category->getCustomFields();
		if ($customFields) {
			foreach ($customFields as $i=>$d) {
				$order = $i+1;
				$code        = isset($d->name)     ? View::escape($d->name)   : '';
				$datatype    = isset($d->type)     ? View::escape($d->type)   : 'string';
				$values      = isset($d->values)   ? View::escape($d->values) : array();
				$description = isset($d->label)    ? View::escape($d->label)  : $code;
				$required    = isset($d->required) ? $d->required             : false;

				$values = implode(',', $values);
				$required = $required ? "true" : "false";
				echo "
				<tr><th>$order</th>
					<td>$code</td>
					<td>true</td>
					<td>$datatype</td>
					<td>$required</td>
					<td>$values</td>
					<td>$description</td>
					<td></td>
				</tr>
				";
			}
		}
echo "
		</tbody>
	</table>
</div>
";