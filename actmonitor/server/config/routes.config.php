<?php
// ---- ROUTES ---------------------------------------------------------------//
/*
 * Here are defined all the routes of the application.
 */
$router = \miu\app\Router::getInstance();

$router->setRoute('ANY', '/', '\controller\MainController', 'Index');

// ---- SYSTEM ---------------------------------------------------------------//
$router->setRoute('ANY', '/systems', '\controller\system\SystemHTMLController', 'Index');
$router->setRoute('ANY', '/systems/:system', '\controller\system\SystemHTMLController', 'System');
$router->setRoute('ANY', '/user/op/systems/add', '\controller\system\SystemHTMLController', 'AddSystem');
$router->setRoute('ANY', '/updates', '\controller\system\UpdateHTMLController', 'Index');

// ---- SYSTEM SERVER --------------------------------------------------------//
$router->setRoute('ANY', '/server/update.xml', '\controller\system\SystemXMLServerController', 'SystemUpdate');

// ---- SECURITY -------------------------------------------------------------//
$router->setRoute('ANY', '/security/login', '\controller\security\SecurityHTMLController', 'Login');
$router->setRoute('ANY', '/security/logout', '\controller\security\SecurityHTMLController', 'Logout');
$router->setRoute('ANY', '/security/register', '\controller\security\SecurityHTMLController', 'Register');

$router->setRoute('ANY', '/security/login.xml', '\controller\security\SecurityXMLController', 'Login');
$router->setRoute('ANY', '/security/logout.xml', '\controller\security\SecurityXMLController', 'Logout');

// ---- TEST -----------------------------------------------------------------//
$router->setRoute('ANY', '/test', '\controller\TestController', 'Index');
$router->setRoute('ANY', '/test/authenticate', '\controller\TestController', 'Authenticate');
$router->setRoute('ANY', '/test/create_regkey', '\controller\TestController', 'CreateRegkey');
?>
