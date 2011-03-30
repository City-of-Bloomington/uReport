<?php
/**
 * @copyright 2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class FormButton
{
	public function __construct() { }
	
	public function FormButton($label,$type,$class=null,$id=null,$onclick=null,$outerClass='button')
	{
		$type = $type ? "type=\"$type\"" : '';
		$class = $class ? "class=\"$class\"" : '';
		$id = $id ? "id=\"$id\"" : '';
		$outerClass = $outerClass ? "class=\"$outerClass\"" : '';
		$onclick = $onclick ? "onclick=\"$onclick\"" : '';
		
		return "
		<span $outerClass>
			<span $class>
				<button $type $id $onclick>$label</button>
			</span>
		</span>
		";
	}
}
