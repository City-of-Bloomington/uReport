<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class TypesController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('two-column');
	}

	public function index()
	{
		$this->template->blocks[] = new Block('lookups/typeList.inc');
	}

	public function update()
	{
		if (isset($_POST['types'])) {
			try {
				Lookups::save('types',$_POST['types']);
				header('Location: '.BASE_URL.'/types');
				exit();
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->blocks[] = new Block('lookups/updateTypesForm.inc');
	}
}