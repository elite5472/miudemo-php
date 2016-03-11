<?php
//---- SYSTEM --------------------------------------------------------------------------//
//--------------------------------------------------------------------------------------//
/*
get.system.all

Returns all systems with item descriptions.
*/
\db::setQuery('sysdata', 'MYSQL', 'get.system.all', <<<_END
SELECT * FROM system
	INNER JOIN item_description ON (system.item_description_id = item_description.id);
_END
);
//--------------------------------------------------------------------------------------//
\db::setQuery('sysdata', 'MYSQL', 'get.system:refid', <<<_END
SELECT
	system.id AS id,
	system.refid AS refid,
	item_description.name AS name,
	item_description.description AS description
FROM system
	LEFT OUTER JOIN item_description ON (system.item_description_id = item_description.id)
WHERE refid = :refid;
_END
);
//--------------------------------------------------------------------------------------//
\db::setQuery('sysdata', 'MYSQL', 'get.system:id', 'select * from system left outer join item_description on (system.item_description_id = item_description.id) where id = :id;');

//--------------------------------------------------------------------------------------//
/*
get.system.all_with_status:status

Returns all systems with their latest update and the count of components with a given status.

Parameters:
	:status

Returns:
	id
	refid
	name
	description
	update_id
	update_time (UNIX_TIMESTAMP)
	status
	status_count (COUNT)
*/
\db::setQuery('sysdata', 'MYSQL', 'get.system.all_with_status:status', <<<_END
SELECT
	system.id  AS id,
	system.refid AS refid,
	item_description.name AS name,
	item_description.description AS description,
	last_updates.id AS update_id,
	UNIX_TIMESTAMP(last_updates.updated) AS update_time,
	COALESCE(SUM(updated_components.current_status = :status), 0) AS status_count
FROM system
	LEFT OUTER JOIN item_description ON (system.item_description_id = item_description.id)
	LEFT OUTER JOIN (
			SELECT * FROM (
					SELECT * FROM system_update ORDER BY id DESC
				)
				AS ordered_updates
				GROUP BY ordered_updates.system_id
		)
		AS last_updates
		ON (last_updates.system_id = system.id)
	LEFT OUTER JOIN (
			SELECT
				system_component.id AS id,
				system_component.system_id AS system_id,
				last_status.current_status AS current_Status
			FROM system_component
				INNER JOIN (
						SELECT
							system_component_id,
							current_status
						FROM system_component_update
						ORDER BY system_update_id DESC
					)
					AS last_status
					ON (last_status.system_component_id = system_component.id)
			GROUP BY system_component.id
		)
		AS updated_components
		ON (system.id = updated_components.system_id)
GROUP BY system.id
_END
);

//--------------------------------------------------------------------------------------//
/*
get.system.all_with_status:user_id,status

Returns all systems belonging to a user with their latest update and the count of
components with a given status.

Parameters:
	:user_id
	:status

Returns:
	id
	refid
	name
	description
	update_id
	update_time (UNIX_TIMESTAMP)
	status
	status_count (COUNT)
*/
\db::setQuery('sysdata', 'MYSQL', 'get.system.all_with_status:user_id,status', <<<_END
SELECT
	system.id  AS id,
	system.refid AS refid,
	item_description.name AS name,
	item_description.description AS description,
	last_updates.id AS update_id,
	UNIX_TIMESTAMP(last_updates.updated) AS update_time,
	COALESCE(SUM(updated_components.current_status = :status), 0) AS status_count
FROM system
	INNER JOIN user_system_permission ON (system.id = user_system_permission.system_id)
	INNER JOIN permission ON (user_system_permission.permission_id = permission.id)
	LEFT OUTER JOIN item_description ON (system.item_description_id = item_description.id)
	LEFT OUTER JOIN (
			SELECT * FROM (
					SELECT * FROM system_update ORDER BY id DESC
				)
				AS ordered_updates
				GROUP BY ordered_updates.system_id
		)
		AS last_updates
		ON (last_updates.system_id = system.id)
	LEFT OUTER JOIN (
			SELECT
				system_component.id AS id,
				system_component.system_id AS system_id,
				last_status.current_status AS current_Status
			FROM system_component
				INNER JOIN (
						SELECT
							system_component_id,
							current_status
						FROM system_component_update
						ORDER BY system_update_id DESC
					)
					AS last_status
					ON (last_status.system_component_id = system_component.id)
			GROUP BY system_component.id
		)
		AS updated_components
		ON (system.id = updated_components.system_id)
WHERE
	user_system_permission.user_id = :user_id AND
	permission.refid = 'system.read' AND
	user_system_permission.setting = 'true'
GROUP BY system.id
_END
);









//---- SYSTEM COMPONENT ----------------------------------------------------------------//
//--------------------------------------------------------------------------------------//
/*
get.system_component:refid,system_id

Returns a matching system_component.

Parameters:
	:refid
	:system_id

Returns:
	id
	refid
	name
	description
*/
\db::setQuery('sysdata', 'MYSQL', 'get.system_component:refid,system_id', <<<_END
SELECT
	system_component.id AS id,
	system_component.refid AS refid,
	system_component.system_id AS system_id,
	item_description.name AS name,
	item_description.description AS description
FROM system_component
	LEFT OUTER JOIN item_description ON ( system_component.item_description_id = item_description.id )
WHERE system_component.refid = :refid AND system_component.system_id = :system_id
_END
);
//--------------------------------------------------------------------------------------//
/*
get.system_component.last

Returns the last inserted system_component.

Parameters:
	none

Returns:
	id
	refid
	system_id
	name
	description
*/
\db::setQuery('sysdata', 'MYSQL', 'get.system_component.last', <<<_END
SELECT
	system_component.id  AS id,
	system_component.refid AS refid,
	system_component.system_id AS system_id,
	item_description.name AS name,
	item_description.description AS description
FROM system_component
	LEFT OUTER JOIN item_description ON ( system_component.item_description_id = item_description.id )
ORDER BY system_component.id DESC
LIMIT 1;
_END
);
//--------------------------------------------------------------------------------------//
/*
get.system_component.last_updates:system.id

Returns all the components on a system, with their latest status and update timestamp.

Parameters:
	:system_id

Returns:
	id
	refid
	system_id
	name
	description
	update_id
	update_time
	current_status
*/
\db::setQuery('sysdata', 'MYSQL', 'get.system_component.last_updates:system_id', <<<_END
SELECT
	system_component.id AS id,
	system_component.refid AS refid,
	system_component.system_id AS system_id,

	item_description.name AS name,
	item_description.description AS description,
	system_update.id AS update_id,
	UNIX_TIMESTAMP(system_update.updated) AS update_time,
	system_component_update.current_status AS current_status

FROM system_component
	LEFT OUTER JOIN item_description ON ( system_component.item_description_id = item_description.id )
	LEFT OUTER JOIN
		(SELECT system_component_id, system_update_id, current_status FROM system_component_update ORDER BY system_update_id DESC)
		AS system_component_update
		ON ( system_component.id = system_component_update.system_component_id )
	LEFT OUTER JOIN system_update ON ( system_component_update.system_update_id = system_update.id )
WHERE system_component.system_id = :system_id
GROUP BY system_component.id;
_END
);
//--------------------------------------------------------------------------------------//
/*
set.system_component:refid,system_id

Creates a new system component.

Parameters:
	:refid
	:system_id

Returns:
	null
*/
\db::setQuery('sysdata', 'MYSQL', 'set.system_component:refid,system_id', <<<_END
INSERT INTO system_component (refid, system_id) VALUES (:refid, :system_id);
_END
);












//---- SYSTEM UPDATE -------------------------------------------------------------------//
//--------------------------------------------------------------------------------------//
/*
get.system_update.last:system_id

Returns the last inserted system_update

Parameters:
	:system_id

Returns:
	id
	system_id
	updated (UNIX_TIMESTAMP)
*/
\db::setQuery('sysdata', 'MYSQL', 'get.system_update.last:system_id', <<<_END
SELECT
	id as id,
	system_id AS system_id,
	UNIX_TIMESTAMP(updated) AS updated
FROM system_update
WHERE system_id = :system_id
ORDER BY id DESC
LIMIT 1;
_END
);

//--------------------------------------------------------------------------------------//
/*
set.system_update:system_id

Inserts a new system update.

Parameters:
	:system_id

Returns:
	null
*/
\db::setQuery('sysdata', 'MYSQL', 'set.system_update:system_id', <<<_END
INSERT INTO system_update (system_id) VALUES (:system_id);
_END
);















//---- SYSTEM COMPONENT UPDATE ---------------------------------------------------------//
//--------------------------------------------------------------------------------------//
/*
set.system_component_update:system_component_id,system_update_id,status,reason,info

Inserts a new system component update.

Parameters:
	:system_component_id
	:system_update_id
	:status
	:reason
	:info

Returns:
	null
*/
\db::setQuery('sysdata', 'MYSQL', 'set.system_component_update:system_component_id,system_update_id,status,reason,info', <<<_END
INSERT INTO system_component_update (system_component_id, system_update_id, current_status, reason, info) VALUES(
	:system_component_id,
	:system_update_id,
	:status,
	:reason,
	:info
);
_END
);

