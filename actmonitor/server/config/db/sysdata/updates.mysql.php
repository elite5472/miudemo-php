<?php
/*
get.updates.all:user_id,last_system_update_id,upper_limit

Parameters:
	:user_id
	:last_system_update_id
	:upper_limit

Returns:
	update_id
	update_time (UNIX_TIMESTAMP)
	status

	system_refid
	system_name

	component_refid
	component_name
*/
\db::setQuery('sysdata', 'MYSQL', 'get.updates.all:user_id,last_system_update_id,upper_limit', "
SELECT
	visible_update.system_name AS system_name,
	visible_update.system_refid AS system_refid,
	system_component.refid AS component_refid,
	item_description.name AS component_name,
	visible_update.update_id AS update_id,
	system_component_update.current_status AS current_status,
	UNIX_TIMESTAMP(visible_update.update_time) AS update_time
FROM(
	SELECT * FROM
	(
		SELECT
			system.id AS system_id,
			system.refid AS system_refid,
			system_description.name AS system_name,
			system_update.id AS update_id,
			system_update.updated AS update_time
		FROM system
		INNER JOIN user_system_permission ON (system.id = user_system_permission.system_id)
		INNER JOIN permission ON (user_system_permission.permission_id = permission.id)
		LEFT OUTER JOIN item_description AS system_description ON (system.item_description_id = system_description.id)
		INNER JOIN system_update ON (system.id = system_update.system_id)
		INNER JOIN system_component_update ON (system_update.id = system_component_update.system_update_id)

		WHERE
			user_system_permission.user_id = :user_id AND
			permission.refid = 'system.read' AND
			user_system_permission.setting = 'true' AND
			(system_update.id < :last_system_update_id OR :last_system_update_id = 0)

		ORDER BY system_update.id DESC
	) AS visible_update
	GROUP BY visible_update.update_id
	ORDER BY visible_update.update_id DESC
	LIMIT :upper_limit
) AS visible_update

INNER JOIN system_component_update ON (visible_update.update_id = system_component_update.system_update_id)
INNER JOIN system_component ON (system_component_update.system_component_id = system_component.id)
LEFT OUTER JOIN item_description ON (system_component.item_description_id = item_description.id)
");

/*
get.updates.all.remaining:user_id,last_system_update_id

Parameters:
	:user_id
	:last_system_update_id

Returns:
	remaining (COUNT)
*/
\db::setQuery('sysdata', 'MYSQL', 'get.updates.all.remaining:user_id,last_system_update_id', "
SELECT COUNT(*) AS remaining
FROM (
SELECT visible_component_update.system_update_id
FROM
(
	SELECT system_component_update.system_update_id AS system_update_id
	FROM
		(
			SELECT system.id AS system_id
			FROM system
				INNER JOIN user_system_permission ON (system.id = user_system_permission.system_id)
				INNER JOIN permission ON (user_system_permission.permission_id = permission.id)
			WHERE user_system_permission.user_id = :user_id AND permission.refid = 'system.read' AND user_system_permission.setting = 'true'
		) AS readable_system
		INNER JOIN system_component ON (readable_system.system_id = system_component.system_id)
		INNER JOIN system_component_update ON (system_component.id = system_component_update.system_component_id)
	WHERE system_update_id < :last_system_update_id OR :last_system_update_id = 0
) AS visible_component_update
GROUP BY system_update_id ) AS visible_update
");

/*
get.updates.all.previous:user_id,previous_system_update_id,upper_limit

Parameters:
	:user_id
	:last_system_update_id

Returns:
	previous
*/
\db::setQuery('sysdata', 'MYSQL', 'get.updates.all.previous:user_id,previous_system_update_id,upper_limit', "
SELECT MAX(previous) AS previous FROM
(
	SELECT visible_update.update_id AS previous FROM
	(
		SELECT
			system.id AS system_id,
			system.refid AS system_refid,
			system_update.id AS update_id,
			system_update.updated AS update_time
		FROM system
		INNER JOIN user_system_permission ON (system.id = user_system_permission.system_id)
		INNER JOIN permission ON (user_system_permission.permission_id = permission.id)
		INNER JOIN system_update ON (system.id = system_update.system_id)
		INNER JOIN system_component_update ON (system_update.id = system_component_update.system_update_id)

		WHERE
			user_system_permission.user_id = :user_id AND
			permission.refid = 'system.read' AND
			user_system_permission.setting = 'true' AND
			(system_update.id >= :previous_system_update_id AND :previous_system_update_id != 0)

		ORDER BY system_update.id DESC
	) AS visible_update
	GROUP BY visible_update.update_id
	ORDER BY visible_update.update_id ASC
	LIMIT :upper_limit
) AS visible_update
");

/*
get.updates:system_refid,lower_limit,upper_limit

Parameters:
	:system_refid
	:last_system_update
	:upper_limit

Returns:
	update_id
	update_time (UNIX_TIMESTAMP)
	status

	system_refid
	system_name

	component_refid
	component_name
*/
\db::setQuery('sysdata', 'MYSQL', 'get.updates:system_refid,lower_limit,upper_limit', "
SELECT
	readable_update.system_name AS system_name,
	readable_update.system_refid AS system_refid,
	readable_update.component_refid AS component_refid,
	readable_update.component_name AS component_name,
	readable_update.update_id AS update_id,
	readable_update.current_status AS current_status,
	UNIX_TIMESTAMP(readable_update.update_time) AS update_time
FROM
	(
		SELECT visible_component_update.system_update_id
		FROM
		(
			SELECT system_component_update.system_update_id AS system_update_id
			FROM system
				INNER JOIN system_component ON (system.id = system_component.system_id)
				INNER JOIN system_component_update ON (system_component.id = system_component_update.system_component_id)
			WHERE system.refid = :system_refid AND (system_update_id < :last_system_update_id OR :last_system_update_id = 0)
			ORDER BY system_component_update.system_update_id DESC
			LIMIT :upper_limit
		) AS visible_component_update
		GROUP BY system_update_id
	) AS visible_update
	INNER JOIN (
	SELECT
		system.refid AS system_refid,
		system_description.name AS system_name,

		system_component.refid AS component_refid,
		component_description.name AS component_name,

		system_component_update.system_update_id AS update_id,
		system_component_update.current_status AS current_status,
		system_update.updated AS update_time
	FROM system
		INNER JOIN system_component ON (system.id = system_component.system_id)
		INNER JOIN system_component_update ON (system_component.id = system_component_update.system_component_id)
		INNER JOIN system_update ON (system_component_update.system_update_id = system_update.id)

		LEFT OUTER JOIN item_description AS system_description ON (system.item_description_id = system_description.id)
		LEFT OUTER JOIN item_description AS component_description ON (system_component.item_description_id = system_description.id)
	WHERE system.refid = :system_refid
	) AS readable_update ON (readable_update.update_id = visible_update.system_update_id)
");

?>
