<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Person;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class LoginController extends Controller
{
	private $return_url;

	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->return_url = !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL;
	}

	/**
	 * Attempts to authenticate users via CAS
	 */
	public function cas()
	{
		// If they don't have CAS configured, send them onto the application's
		// internal authentication system
		if (defined('CAS_SERVER')) {
            \phpCAS::client(CAS_VERSION_2_0, CAS_SERVER, 443, CAS_URI, false);
            \phpCAS::setNoCasServerValidation();
            \phpCAS::forceAuthentication();
            // at this step, the user has been authenticated by the CAS server
            // and the user's login name can be read with phpCAS::getUser().

            $this->registerUser(\phpCAS::getUser());
        }
		header('Location: '.BASE_URL.'/login?return_url='.$this->return_url);
		exit();
	}

	/**
	 * Authenticate users against Auth0.com
	 */
	public function auth0()
	{
        global $AUTH0;

        if (isset($AUTH0)) {
            $auth0 = new Auth0([
                'domain'        => $AUTH0['domain'       ],
                'client_id'     => $AUTH0['client_id'    ],
                'client_secret' => $AUTH0['client_secret'],
                'redirect_uri'  => BASE_URL.'/login/auth0',
                'audience'      => "https://$AUTH0[domain]/userinfo",
                'scope'         => 'openid profile'
            ]);
            $user = $auth0->getUser();
            if ($user) {
                $this->registerUser($user['name']);
            }
            else {
                $auth0->login();
            }
        }
		header('Location: '.BASE_URL.'/login?return_url='.$this->return_url);
		exit();
	}

	/**
	 * Attempts to authenticate users based on AuthenticationMethod
	 */
	public function index()
	{
		if (isset($_POST['username'])) {
			try {
				$person = new Person($_POST['username']);
				if ($person->authenticate($_POST['password'])) {
					$_SESSION['USER'] = $person;
					header('Location: '.$this->return_url);
					exit();
				}
				else {
					throw new \Exception('invalidLogin');
				}
			}
			catch (\Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}

		$this->template->title = $this->template->_('login');
		$this->template->blocks[] = new Block('loginForm.inc', ['return_url'=>$this->return_url]);
	}

	public function logout()
	{
		session_destroy();
		header('Location: '.$this->return_url);
		exit();
	}

	/**
	 * Checks for a user account with the given username.
	 * If they exist it will register the user into the session and redirect.
	 * Writes to $_SESSION[errorMessages] if there's a problem.
	 */
	private function registerUser(string $username)
	{
        try {
            $user = Person::findByUsername($username);
            if ($user) {
                $_SESSION['USER'] = $user;
                header("Location: {$this->return_url}");
                exit();
            }
            throw new \Exception(Person::ERROR_UNKNOWN_PERSON);
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
        }
	}
}
