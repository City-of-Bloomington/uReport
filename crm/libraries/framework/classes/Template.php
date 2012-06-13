<?php
/**
 * Defines the overall page layout
 *
 * The template collects all the blocks from the controller
 *
 * @copyright 2006-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Template extends View
{
	private $path;
	private $filename;

	public $outputFormat = 'html';
	public $blocks   = array();
	private $assets  = array();
	private $helpers = array();

	/**
	 * @param string $filename
	 * @param string $outputFormat
	 * @param array $vars
	 */
	public function __construct($filename='default',$outputFormat='html',array $vars=null)
	{
		$this->path = APPLICATION_HOME.'/templates';
		$this->filename = $filename;
		$this->outputFormat = preg_replace('/[^a-zA-Z]/','',$outputFormat);

		// Make sure the output format exists
		if (!is_file("{$this->path}/{$this->outputFormat}/{$this->filename}.inc")) {
			$this->filename = 'default';
			$this->outputFormat = 'html';
		}

		if (count($vars)) {
			foreach ($vars as $name=>$value) {
				$this->vars[$name] = $value;
			}
		}
	}

	/**
	 * @param string $filename
	 */
	public function setFilename($filename)
	{
		if (is_file("{$this->path}/{$this->outputFormat}/$filename.inc")) {
			$this->filename = $filename;
		}
		else {
			throw new Exception('unknownTemplate');
		}
	}

	/**
	 * @param string $format
	 */
	public function setOutputFormat($format)
	{
		$format = preg_replace('/[^a-zA-Z]/','',$format);
		if (is_file("{$this->path}/$format/{$this->filename}.inc")) {
			$this->outputFormat = $format;
		}
		else {
			throw new Exception('unknownOutputFormat');
		}
	}

	/**
	 * Returns all the rendered content of the template
	 *
	 * Template files must include a call to $this->includeBlocks(),
	 * when they're ready for content
	 *
	 * @return string
	 */
	public function render()
	{
		ob_start();
		include "{$this->path}/{$this->outputFormat}/{$this->filename}.inc";
		return ob_get_clean();
	}

	/**
	 * Callback function for template files
	 *
	 * Renders blocks for the main content area, unless $panel is given.  If $panel is given
	 * it will render any blocks that the controllers have assigned to that panel.
	 *
	 * Template files make calls to this function to render all the blocks that the controller
	 * has loaded for this Template.  Controllers will populate the blocks array with content.
	 * If a template file can render content in a panel that is not the main content panel,
	 * the template file will need to include the panel's name in the includeBlocks() call.
	 *
	 * $this->blocks is a multi-dimensional array.  The top level elements, non-array elements
	 * are for the default, main content area.  Other panels will be arrays in $this->blocks with
	 * the panel name as the key.
	 *
	 * Panels are nothing but a name on a div, the $panel string can be whatever the template
	 * author thinks makes sense.  Controllers are expected to know what the template authors
	 * have written.
	 *
	 * $this->blocks[] = "main content block one";
	 * $this->blocks[] = "main content block two";
	 * $this->blocks['panel-one'][] = "left sidebar block one";
	 * $this->blocks['panel-one'][] = "left sidebar block two";
	 * $this->blocks['panel-two'][] = "right sidebar block one";
	 *
	 * @param string $panel
	 * @return string
	 */
	private function includeBlocks($target=null)
	{
		ob_start();
		if ($target) {
			// Render any blocks for the given panel
			if (isset($this->blocks[$target]) && is_array($this->blocks[$target])) {
				foreach ($this->blocks[$target] as $block) {
					echo $block->render($this->outputFormat,$this);
				}
			}
			else {
				// Go through the template looking for what they asked for
				foreach ($this->blocks as $key=>$value) {
					// If we find a block that matches, render that block
					if ($value instanceof Block) {
						if ($value->getFile() == $target) {
							echo $value->render($this->outputFormat,$this);								continue;
						}
					}
					// The block they asked for might be inside a panel
					else {
						foreach ($value as $block) {
							if ($block->getFile() == $target) {
								echo $block->render($this->outputFormat,$this);
								continue;
							}
						}
					}
				}
			}
		}
		else {
			// Render only the blocks for the main content area
			foreach ($this->blocks as $block) {
				if (!is_array($block)) {
					echo $block->render($this->outputFormat,$this);
				}
			}
		}
		return ob_get_clean();
	}

	/**
	 * Adds data to an asset, making sure to not duplicate existing data
	 *
	 * @param string $name The name of the asset
	 * @param mixed $data
	 */
	public function addToAsset($name,$data)
	{
		if (!isset($this->assets[$name]) || !is_array($this->assets[$name])) {
			$this->assets[$name] = array();
		}
		if (!in_array($data,$this->assets[$name])) {
			$this->assets[$name][] = $data;
		}
	}

	/**
	 * Loads and returns a helper object
	 */
	public function getHelper($functionName)
	{
		if (!array_key_exists($functionName, $this->helpers)) {
			$class = ucfirst($functionName);
			require_once APPLICATION_HOME."/templates/{$this->outputFormat}/helpers/$class.php";
			$this->helpers[$functionName] = new $class();
		}
		return $this->helpers[$functionName];
	}
}
