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
 * @copyright 2006-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application;

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
	public function __construct($file, array $vars=null)
	{
		parent::__construct($vars);

		$this->file = $file;
	}

	/**
	 * @param string $file Path to file from /blocks directory
	 * @return bool
	 */
	public function exists($file)
	{
        return (($this->theme && is_file(   "{$this->theme}/blocks/$file"))
                ||              (is_file(APPLICATION_HOME."/blocks/$file")));
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


		if ($this->theme && is_file($this->theme.$block)) {
			$file = $this->theme.$block;
		}
		elseif (is_file(APPLICATION_HOME.$block)) {
			$file = APPLICATION_HOME.$block;
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
	 * Supports THEME overriding.
	 * Specify a relative path starting from /blocks/
	 * $file paths should not start with a slash.
	 *
	 * @param string $file
	 */
	public function _include($file)
	{
        $format = $this->template->outputFormat;

		if ($this->theme
            && is_file($this->theme."/blocks/$format/$file")) {
			include    $this->theme."/blocks/$format/$file";
		}
		else {
			include APPLICATION_HOME."/blocks/$format/$file";
		}
	}
}
