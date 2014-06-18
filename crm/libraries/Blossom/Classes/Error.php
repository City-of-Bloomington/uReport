<?php
/**
 * Class for custom error handling
 *
 * Applications need to do more with errors than just display or log the error.
 * Error handling should be configurable in the configuration.inc
 * The $ERROR_REPORTING variable declared in configuration.inc controls the
 * various possibile things to do with errors.
 *
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;

class Error
{
	/**
	* Provide nicely formatted error messages when PHP bombs out.
	*/
	public static function customErrorHandler($errno, $errstr, $errfile, $errline)
	{
		global $ERROR_REPORTING;

		if (isset($ERROR_REPORTING)) {
			if (in_array('PRETTY_PRINT',$ERROR_REPORTING)) {
				echo "
				<div id=\"errorMessages\">
					<p><em>from ".ADMINISTRATOR_NAME.":</em>
							There is an error in the code on this page that is through no fault of your own.
							Errors of this sort need to be fixed immediately, though.
							Please help us out by copying and pasting the following error message into an email and sending it to me at
							<a href=\"mailto:".ADMINISTRATOR_EMAIL."\">".ADMINISTRATOR_EMAIL."</a>.
					</p>
					<p><strong>Code Error:</strong>  Error on line $errline of file $errfile:</p>
					<p>$errstr</p>
				</div>
				";
			}
			if (in_array('EMAIL_ADMIN',$ERROR_REPORTING)) {
				$subject = APPLICATION_NAME.' Error';
				$message = "\t$_SERVER[REQUEST_URI]\n\nError on line $errline of file $errfile:\n$errstr\n\n";
				$message.= print_r(debug_backtrace(),true);
				mail(ADMINISTRATOR_EMAIL,$subject,$message,"From: apache@$_SERVER[SERVER_NAME]");
			}

			if (in_array('EMAIL_USER',$ERROR_REPORTING)
					&& isset($_SESSION['USER'])
					&& $_SESSION['USER']->getEmail()) {
				$subject = APPLICATION_NAME.' Error';
				$message = "\t$_SERVER[REQUEST_URI]\n\nError on line $errline of file $errfile:\n$errstr\n\n";
				$message.= print_r(debug_backtrace(),true);
				mail($_SESSION['USER']->getEmail(),
					$subject,
					$message,
					"From: apache@$_SERVER[SERVER_NAME]");
			}
			if (in_array('SKIDDER',$ERROR_REPORTING)) {
				$script = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'];
				$message = "$script\nError on line $errline of file $errfile:\n$errstr\n";
				$message.= print_r(debug_backtrace(),true);

				$skidder = curl_init(SKIDDER_URL);
				curl_setopt($skidder,CURLOPT_POST,true);
				curl_setopt($skidder,CURLOPT_HEADER,true);
				curl_setopt($skidder,CURLOPT_RETURNTRANSFER,true);
				curl_setopt($skidder,CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($skidder,
							CURLOPT_POSTFIELDS,
							array('application_id'=>SKIDDER_APPLICATION_ID,
								'script'=>$script,
								'type'=>$errstr,
								'message'=>$message));
				curl_exec($skidder);
			}
		}
	}

	/**
	 * Object oriented exceptions are handled differently from other PHP errors.
	 */
	public static function customExceptionHandler($exception)
	{
		global $ERROR_REPORTING;

		if (isset($ERROR_REPORTING)) {
			if (in_array('PRETTY_PRINT',$ERROR_REPORTING)) {
				echo "
				<div id=\"errorMessages\">
					<p><em>from ".ADMINISTRATOR_NAME.":</em>
							There is an error in the code on this page that is through no fault of your own.
							Errors of this sort need to be fixed immediately, though.
							Please help me out by copying and pasting the following error message into an email and sending it to me at
							<a href=\"mailto:".ADMINISTRATOR_EMAIL."\">".ADMINISTRATOR_EMAIL."</a>.
					</p>
					<p><strong>Uncaught exception:</strong>
						Exception on line {$exception->getLine()} of file {$exception->getFile()}:
					</p>
					<p>{$exception->getMessage()}</p>
				</div>
				";
			}
			if (in_array('EMAIL_ADMIN',$ERROR_REPORTING)) {
				$subject = APPLICATION_NAME.' Exception';
				$message = "\t$_SERVER[REQUEST_URI]\n\nException on line {$exception->getLine()} of file {$exception->getFile()}:\n{$exception->getMessage()}\n\n";
				$message.= print_r(debug_backtrace(),true);
				mail(ADMINISTRATOR_EMAIL,$subject,$message,"From: apache@$_SERVER[SERVER_NAME]");
			}
			if (in_array('EMAIL_USER',$ERROR_REPORTING)
					&& isset($_SESSION['USER'])
					&& $_SESSION['USER']->getEmail()) {
				$subject = APPLICATION_NAME.' Exception';
				$message = "\t$_SERVER[REQUEST_URI]\n\nException on line {$exception->getLine()} of file {$exception->getFile()}:\n{$exception->getMessage()}\n\n";
				$message.= print_r(debug_backtrace(),true);
				mail($_SESSION['USER']->getEmail(),
					$subject,
					$message,
					"From: apache@$_SERVER[SERVER_NAME]");
			}
			if (in_array('SKIDDER',$ERROR_REPORTING)) {
				$script = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'];
				$message = "Error on line {$exception->getLine()} of file {$exception->getFile()}:\n{$exception->getMessage()}\n";
				$message.= print_r(debug_backtrace(),true);

				$skidder = curl_init(SKIDDER_URL);
				curl_setopt($skidder,CURLOPT_POST,true);
				curl_setopt($skidder,CURLOPT_HEADER,true);
				curl_setopt($skidder,CURLOPT_RETURNTRANSFER,true);
				curl_setopt($skidder,CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($skidder,
							CURLOPT_POSTFIELDS,
							array('application_id'=>SKIDDER_APPLICATION_ID,
								'script'=>$script,
								'type'=>'Uncaught Exception',
								'message'=>$message));
				curl_exec($skidder);
			}
		}
	}
}
