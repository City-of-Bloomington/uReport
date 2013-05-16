<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';
// Check for Open311 routes
if (false !== strpos($_SERVER['REQUEST_URI'],'open311')) {
	$resource = 'open311';
	if (preg_match(
				'#'.BASE_URI.'/open311/discovery(\.([a-z]+))?#',
				$_SERVER['REQUEST_URI'],
				$matches)) {
		$action = 'discovery';
		$_REQUEST['format'] = !empty($matches[2]) ? $matches[2] : 'html';
	}
	elseif (preg_match(
				'#'.BASE_URI.'/open311/v2/(services|requests)(/(\w+))?(\.([a-z]+))?#',
				$_SERVER['REQUEST_URI'],
				$matches)) {
		$action = $matches[1];
		if (!empty($matches[3])) {
			$action_id = $action=='services' ? 'service_code' : 'service_request_id';
			$_REQUEST[$action_id] = $matches[3];
		}
		$_REQUEST['format'] = !empty($matches[5]) ? $matches[5] : 'html';
	}
}
// Check for Media thumbnail requests
elseif (preg_match(
			'#'.BASE_URI.'/media/\d{4}/\d{1,2}/\d{1,2}/(\d+)/([a-f0-9]+\.[a-z]+)#',
			$_SERVER['REQUEST_URI'],
			$matches)) {
			$resource = 'media';
			$action   = 'resize';
			$_REQUEST['size']     = $matches[1];
			$_REQUEST['media_id'] = $matches[2];
}
// Check for default routes
elseif (preg_match('#'.BASE_URI.'(/([a-zA-Z0-9]+))?(/([a-zA-Z0-9]+))?#',$_SERVER['REQUEST_URI'],$matches)) {
	$resource = isset($matches[2]) ? $matches[2] : 'index';
	$action   = isset($matches[4]) ? $matches[4] : 'index';
}

// Create the Template
$format = !empty($_REQUEST['format']) ? $_REQUEST['format'] : 'html';
$template = new Template('default', $format);

// Execute the Controller::action()
if (isset($resource) && isset($action) && $ZEND_ACL->has($resource)) {
	$role = isset($_SESSION['USER']) ? $_SESSION['USER']->getRole() : 'Anonymous';
	if ($ZEND_ACL->isAllowed($role, $resource, $action)) {
		$controller = ucfirst($resource).'Controller';
		$c = new $controller($template);
		$c->$action();
	}
	else {
		header('HTTP/1.1 403 Forbidden', true, 403);
		$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	}
}
else {
	header('HTTP/1.1 404 Not Found', true, 404);
	$template->blocks[] = new Block('404.inc');
}

if (!empty($_REQUEST['partial'])) {
	$template->setFilename('partial');
}
echo $template->render();
