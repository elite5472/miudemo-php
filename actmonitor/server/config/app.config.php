<?php
// ---- APP CONFIG -----------------------------------------------------------//
\env::set('settings.name', 'Activity Monitor');
\env::set('settings.encoding', 'utf-8');
\env::set('settings.view.dependencies.autoload', true);

// ---- DEPENDENCIES ---------------------------------------------------------//
\env::set('dependencies.css.global', array(
	'/global/base'
));

// ---- SYSTEMS --------------------------------------------------------------//

\env::set('app.systems.display_status', 'RUNNING');
\env::set('app.systems.display_color.none', 'Red');
\env::set('app.systems.display_color.some', 'Green');
\env::set('app.systems.display_color.unknown', 'Purple');

\env::set('app.components.status.color.RUNNING', 'Green');
\env::set('app.components.status.color.STARTING', 'Blue');
\env::set('app.components.status.color.STOPPED', 'Red');
\env::set('app.components.status.color.ERROR', 'DarkRed');
\env::set('app.components.status.color.WARNING', 'Orange');
\env::set('app.components.status.color.UNKNOWN', 'Purple');
\env::set('app.components.status.color.DEFAULT', 'Gray');

\env::set('app.updates.show_amount', 15);

// ---- CONSTANTS ------------------------------------------------------------//
\env::set('constants.xml.header', '<?xml version="1.0" encoding="' . env::get('settings.encoding') . '" ?>');
\env::set('constants.timestamp.short', 'm-d-Y H:i:s');
\env::set('constants.timestamp.long', '\O\n l F jS, Y \a\t h:ia');
\env::set('constants.timestamp.time_only', 'h:ia');

// ---- SECURITY -------------------------------------------------------------//
\env::set('security.session.length', 'PT20M');

\env::set('security.register.mode', 'key'); //Modes: free, key, locked
\env::set('security.register.key.expires', 'P5D');
\env::set('security.register.default_user_class', 'admin');

?>
