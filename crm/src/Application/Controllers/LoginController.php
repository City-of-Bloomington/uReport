<?php
/**
 * @copyright 2012-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Person;
use Jumbojett\OpenIDConnectClient;

use Application\Block;
use Application\Controller;
use Application\Template;

class LoginController extends Controller
{
    private $return_url;

    public function __construct(Template $template)
    {
        parent::__construct($template);
        $this->return_url = !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL;
    }

    public function oidc()
    {
        // If they don't have OpenID configured, send them onto the application's
        // internal authentication system
        global $AUTHENTICATION;
        if ( !empty(  $AUTHENTICATION['oidc']['client_id'])) {
            $config = $AUTHENTICATION['oidc'];
            $oidc   = new OpenIDConnectClient($config['server'], $config['client_id'], $config['client_secret']);
            $oidc->addScope(['openid', 'allatclaims', 'profile']);
            $oidc->setAllowImplicitFlow(true);
            $oidc->setRedirectURL(BASE_URL.'/login/oidc');

            $success = null;
            try { $success = $oidc->authenticate(); }
            catch (\Exception $e) { }
            if (!$success) {
                $_SESSION['errorMessages'][] = 'invalidLogin';
            }

            // at this step, the user has been authenticated by the OIDC server
            $info = $oidc->getVerifiedClaims();

            if (!$info->{$config['claims']['username']}) {
                $_SESSION['errorMessages'][] = 'ldap/unknownUser';
            }
            // They may be authenticated according to ADFS,
            // but that doesn't mean they have person record
            // and even if they have a person record, they may not
            // have a user account for that person record.
            $this->registerUser($info->{$config['claims']['username']});
        }

        header('Location: '.BASE_URL.'/login?return_url='.$this->return_url);
        exit();
    }

    public function index()
    {
        header('Location: '.BASE_URL.'/login/oidc');
        exit();
    }

    public function logout()
    {
        session_destroy();
        header('Location: '.$this->return_url);
        exit();
    }

    public function cas()
    {
        http_response_code(404);
        header('HTTP/1.1 404 Not Found', true, 404);
        $this->template->blocks = [ new Block('404.inc') ];
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
            throw new \Exception('people/unknown');
        }
        catch (\Exception $e) {
            $_SESSION['errorMessages'][] = $e;
        }
    }
}
