<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Date extends DateTime
{
	/**
	 * Handles array dates passed in the constructor.
	 *
	 * Wrapper for DateTime constructor.  If arrays are passed, they will be
	 * handled here.  Anything else will be passed to the DateTime constructor.
	 * Arrays should be in the form of PHP's getdate() array
	 *
	 * @param array $date
	 */
	public function __construct($date=null)
	{
		if (is_array($date)) {
			if ($date['year'] && $date['mon'] && $date['mday']) {
				$dateString = "$date[year]-$date[mon]-$date[mday]";

				if (isset($date['hours']) || isset($date['minutes']) || isset($date['seconds'])) {
					$time = (isset($date['hours']) && $date['hours']) ? "$date[hours]:" : '00:';
					$time.= (isset($date['minutes']) && $date['minutes']) ? "$date[minutes]:" : '00:';
					$time.= (isset($date['seconds']) && $date['seconds']) ? $date['seconds'] : '00';

					$dateString.= " $time";
				}
				$date = $dateString;
			}
		}
		if (is_int($date)) {
			$date = date('Y-m-d',$date);
		}
		if (!$date instanceof DateTime) {
			parent::__construct($date);
		}
	}

	public function __toString()
	{
		return $this->format(DATE_FORMAT);
	}
}