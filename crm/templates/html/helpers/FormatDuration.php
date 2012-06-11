<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class FormatDuration
{
	public function __construct() { }

	public function formatDuration($durationInSeconds) {
		$duration = '';
		$days     = floor($durationInSeconds / 86400);
		$durationInSeconds -= $days * 86400;
		$hours    = floor($durationInSeconds / 3600);
		$durationInSeconds -= $hours * 3600;
		$minutes  = floor($durationInSeconds / 60);
		$seconds  = $durationInSeconds - $minutes * 60;

		if     ($days    > 0) { $duration = "$days days";       }
		elseif ($hours   > 0) { $duration = "$hours hours";     }
		elseif ($minutes > 0) { $duration = "$minutes minutes"; }
		else                  { $duration = "$seconds seconds"; }

		return $duration;
	}
}