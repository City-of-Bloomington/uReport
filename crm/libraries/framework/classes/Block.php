<?php
/**
 * Represents a block of content in a template
 *
 * Blocks are partial view scripts.
 * They are contained in APPLICATION/blocks
 * They are organized by $outputFormat
 * APPLICATION_HOME/blocks/html/...
 * APPLICATION_HOME/blocks/xml/...
 * APPLICATION_HOME/blocks/json/..
 *
 * @copyright 2006-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Block extends View
{
	private $file;
	private $template;

	/**
	 * Establishes the block script to use for rendering
	 *
	 * Blocks are files contained in the base path of:
	 * APPLICATION_HOME/blocks/$outpuform
	 *
	 * @param string $file
	 * @param array $vars An associative array of variables to set
	 */
	public function __construct($file,array $vars=null)
	{
		$this->file = $file;
		if (count($vars)) {
			foreach ($vars as $name=>$value) {
				$this->vars[$name] = $value;
			}
		}
	}

	/**
	 * Includes the block script and returns the output as a string
	 *
	 * We allow for passing the Template that this block is being rendered in.
	 * This allows the blocks to update information in the template on the fly.
	 * This is most commonly used in adding script urls to the Template
	 *
	 * @param string $outputFormat
	 * @return string
	 */
	public function render($outputFormat='html', Template $template=null)
	{
		$block = "/blocks/$outputFormat/{$this->file}";
		$this->template = $template;

		if (file_exists(APPLICATION_HOME.$block)) {
			ob_start();
			include APPLICATION_HOME.$block;
			return ob_get_clean();
		}
		elseif (file_exists(FRAMEWORK.$block)) {
			ob_start();
			include FRAMEWORK.$block;
			return ob_get_clean();
		}

		throw new Exception('unknownBlock/'.$this->file);
	}

	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}
}
