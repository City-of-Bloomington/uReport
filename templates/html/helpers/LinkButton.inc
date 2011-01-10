<?php
/**
 * @copyright 2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class LinkButton
{
	public function __construct() { }
	
	public function linkButton($label,$url=null,$class=null,$id=null,$onclick=null)
	{
		$href = $url ? "href=\"$url\"" : '';
		$class = $class ? "class=\"$class\"" : '';
		$id = $id ? "id=\"$id\"" : '';
		$onclick = $onclick ? "onclick=\"$onclick\"" : '';
		
		return "
		<span class=\"button\">
			<span $class>
				<a $id $href $onclick>$label</a>
			</span>
		</span>
		";
	}
}
