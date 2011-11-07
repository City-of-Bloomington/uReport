<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 */
echo "
<service_definition>
	<service_code>{$this->category->getId()}</service_code>
	<attributes>
";
	$customFields = $this->category->getCustomFields();
	if ($customFields) {
		foreach ($customFields as $i=>$definition) {
			$order = $i+1;
			$code  = View::escape($definition['name']);
			$datatype = isset($definition['type']) ? View::escape($definition['type']) : 'string';
			$description = isset($definition['label']) ? View::escape($definition['label']) : $code;
			$required = isset($definition['required']) ? $definition['required'] : false;
			$required = $required ? "true" : "false";
			echo "
			<attribute>
				<variable>true</variable>
				<code>$code</code>
				<order>$order</order>
				<datatype>$datatype</datatype>
				<datatype_description></datatype_description>
				<required>$required</required>
				<description>$description</description>
				<values>
			";
				if (isset($definition['values'])) {
					foreach ($definition['values'] as $value) {
						$value = View::escape($value);
						echo "
						<value>
							<key>$value</key>
							<name>$value</name>
						</value>
						";
					}
				}
			echo "
				</values>
			</attribute>
			";
		}
	}
echo "
	</attributes>
</service_definition>
";