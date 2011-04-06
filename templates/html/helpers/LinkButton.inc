<?php
/**
 * @copyright 2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class LinkButton
{
	public function __construct() { }

	public function linkButton($label,$url=null,$innerClass=null,$id=null,$onclick=null,$outerClass='icon')
	{
		$href = $url ? "href=\"$url\"" : '';
		$innerClass = $innerClass ? "class=\"$innerClass\"" : '';
		$id = $id ? "id=\"$id\"" : '';
		$onclick = $onclick ? "onclick=\"$onclick\"" : '';

		return "
		<span class=\"$outerClass\">
			<span $innerClass>
				<a $id $href $onclick>$label</a>
			</span>
		</span>
		";
	}
}
