<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Category $this->category
 */
use Application\View;

echo "
<service_definition>
    <service_code>{$this->category->getId()}</service_code>
    <attributes>
";
    $customFields = $this->category->getCustomFields();
    if ($customFields) {
        foreach ($customFields as $i=>$d) {
            $order = $i+1;
            $code  = self::escape($d['name']);
            $datatype    = self::escape($d['type'    ] ?? 'string';
            $description = self::escape($d['label'   ] ?? $code;
            $required    = self::escape($d['required'] ?? false;
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
            ";
                if (isset($d['values'])) {
                    echo '<values>';
                    foreach ($d['values'] as $value) {
                        $value = self::escape($value);
                        echo "
                        <value>
                            <key>$value</key>
                            <name>$value</name>
                        </value>
                        ";
                    }
                    echo '</values>';
                }
            echo "
            </attribute>
            ";
        }
    }
echo "
    </attributes>
</service_definition>
";
