<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Case $this->case
 * @param bool $this->disableButtons
 */
$location = View::escape($this->case->getLocation());
$assigned = View::escape($this->case->getPersonData('assignedPerson','fullname'));
$enteredBy = View::escape($this->case->getPersonData('enteredByPerson','fullname'));
$referred = View::escape($this->case->getPersonData('referredPerson','fullname'));

$status = $this->case->getStatus();
if ($this->case->getResolution()) {
	$status.= ': '.$this->case->getResolution();
}

$assignButton = '';
$referralButton = '';
$editStatusButton = '';
$changeLocationButton = '';

if (!$this->disableButtons && userIsAllowed('Cases')) {
	$editStatusButton = $this->template->linkButton(
		'Edit Status',
		BASE_URL.'/cases/changeStatus.php?case_id='.$this->case->getId(),
		'edit'
	);

	if ($this->case->getStatus()!='closed') {
		$assignButton = $this->template->linkButton(
			'Assign',
			BASE_URL.'/cases/assignCase.php?case_id='.$this->case->getId(),
			'edit'
		);

		$return_url = new URL(BASE_URL.'/cases/referCase.php?case_id='.$this->case->getId());
		$referralButton = $this->template->linkButton(
			'Refer',
			BASE_URL."/people?return_url=$return_url",
			'edit'
		);
	}

	$changeLocationButton = $this->template->linkButton(
		'Change Location',
		BASE_URL.'/cases/changeLocation.php?case_id='.$this->case->getId(),
		'edit'
	);
}

echo "
<div class=\"caseInfo\">
	<h1>Case #{$this->case->getId()}</h1>
	<div>$location $changeLocationButton</div>
	<table>
		<tr><th>Date Opened</th>
			<td>{$this->case->getEnteredDate(DATE_FORMAT)}</td>
			<td></td>
		</tr>
		<tr><th>Status</th>
			<td>$status</td>
			<td>$editStatusButton</td>
		</tr>
		<tr><th>Assigned To:</th>
			<td>$assigned</td>
			<td>$assignButton</td>
		</tr>
		<tr><th>Referred To:</th>
			<td>$referred</td>
			<td>$referralButton</td>
		</tr>
		<tr><th>Received By</th>
			<td>$enteredBy</td>
		</tr>
	</table>
</div>
";
