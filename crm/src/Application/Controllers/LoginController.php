<?php
/**
 * @copyright 2012-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Models\Person;

use Facile\OpenIDClient\Client\ClientBuilder;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClient\Service\Builder\AuthorizationServiceBuilder;
use Facile\OpenIDClient\Service\Builder\UserInfoServiceBuilder;
use GuzzleHttp\Psr7\ServerRequest;

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
            $config   = $AUTHENTICATION['oidc'];
            $request  = ServerRequest::fromGlobals();

            $issuer   = (new IssuerBuilder())->build("$config[server]/.well-known/openid-configuration");
            $metadata = ClientMetadata::fromArray(['client_id'     => $config['client_id'    ],
                                                   'client_secret' => $config['client_secret'],
                                                   'redirect_uris' => [BASE_URL.'/login/oidc'],
                                                   'token_endpoint_auth_method' => 'client_secret_basic'
                                                  ]);
            $service  = (new AuthorizationServiceBuilder())->build();
            $client   = (new ClientBuilder())
                      ->setIssuer($issuer)
                      ->setClientMetadata($metadata)
                      ->build();


            if (isset($_REQUEST['id_token'])) {
                $params  = $service->getCallbackParams($request, $client);
                $tokens  = $service->callback($client, $params);
                $idToken = $tokens->getIdToken();
                /** @var array<string, mixed> $claims */
                $claims  = $tokens->claims();

                $nonce   = $_SESSION['nonce'] ?? '';
                if (!isset($claims['nonce']) || $claims['nonce']!=$nonce) {
                    header('HTTP/1.1 403 Forbidden', true, 403);
                    $_SESSION['errorMessages'][] = 'noAccessAllowed';
                    return;
                }

                unset($_SESSION['nonce']);

                if (empty($claims[$config['claims']['username']])) {
                    header('HTTP/1.1 403 Forbidden', true, 403);
                    $_SESSION['errorMessages'][] = 'ldap/unknownUser';
                    return;
                }

                $user = Person::findByUsername($claims[$config['claims']['username']]);
                if ($user) {
                    $_SESSION['USER'] = $user;
                    header("Location: {$this->return_url}");
                    exit();
                }
                else {
                    header('HTTP/1.1 403 Forbidden', true, 403);
                    $_SESSION['errorMessages'][] = 'people/unknown';
                    return;
                }
            }

            $_SESSION['nonce'] = bin2hex(random_bytes(32));
            $idp_url  = $service->getAuthorizationUri($client, [
                            'response_mode' => 'form_post',
                            'response_type' => 'id_token',
                            'nonce'         => $_SESSION['nonce']
                        ]);
            header("Location: $idp_url");
            exit();
        }

        header('HTTP/1.1 404 Not Found', true, 404);
        $this->template->blocks[] = new Block('404.inc');
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
}
