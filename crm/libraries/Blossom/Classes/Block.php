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
 * @copyright 2006-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;

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
		parent::__construct($vars);

		$this->file = $file;
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

		if (is_file(SITE_HOME.$block)) {
			$file = SITE_HOME.$block;
		}
		elseif (is_file(APPLICATION_HOME.$block)) {
			$file = APPLICATION_HOME.$block;
		}
		elseif (is_file(BLOSSOM.$block)) {
			$file = BLOSSOM.$block;
		}
		else {
			throw new \Exception('unknownBlock/'.$this->file);
		}

		ob_start();
		include $file;
		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * Includes the given filename.
	 *
	 * Supports SITE_HOME overriding.
	 * Specify a relative path starting from /blocks/
	 * $file paths should not start with a slash.
	 *
	 * @param string $file
	 */
	public function _include($file)
	{
		if (is_file(SITE_HOME."/blocks/$file")) {
			include SITE_HOME."/blocks/$file";
		}
		else {
			include APPLICATION_HOME."/blocks/$file";
		}
	}
}
