<?php
// ---- Menu Routes ----------------------------------------------------------//

\Routes::createGroup('main.header_menu')
	->createRoute('/systems', 'Systems')
;

\Routes::createGroup('main.header_menu.admin')
	->createRoute('/admin', 'Management')
;

\Routes::createGroup('systems.index.left_menu')
	->createRoute('/updates', 'View logs')
	->createRoute('/user/op/systems/add', 'Add a new system')
;

\Routes::createGroup('systems.system.left_menu')
	->createRoute('/systems/:system/updates', 'View update logs')
;

\Routes::createGroup('systems.system.left_menu.admin')
	->createRoute('/systems/:system/manage', 'Manage access')
	->createRoute('/systems/:system/edit','Edit details')
	->createRoute('/systems/:system/delete', 'Delete')
;

?>
