<?php
/*
 * set.system:refid,item_description_id
 *
 * Creates a new system.
 *
 * Parameters:
 * 	refid
 * 	item_description_id
 *
 * Returns:
 * 	null
 */
 \db::setQuery('sysdata', 'MYSQL', 'set.system:refid,item_description_id', '
 	INSERT INTO system(refid, item_description_id) VALUES(:refid, :item_description_id)
 ');

/*
 * get.user_system_permission:user_id,system_id
 *
 * Returns the setting assigned to given system permission.
 *
 * Parameters
 * 	user_id
 * 	system_refid
 *
 * Returns
 * 	permission_refid
 * 	setting
 */
\db::setQuery('sysdata', 'MYSQL', 'get.user_system_permission:user_id,system_id', '

 	SELECT
 		permission.refid AS permission_refid,
 		user_system_permission.setting AS setting
 	FROM user_system_permission
 		INNER JOIN permission ON (user_system_permission.permission_id = permission.id)
 	WHERE
 		user_id = :user_id AND
 		system_id = :system_id

');

/*
 * set.user_system_permission:user_id,system_id,permission_id,setting
 *
 * Returns the setting assigned to given system permission.
 *
 * Parameters
 * 	user_id
 * 	system_id
 * 	permission_id
 * 	setting
 *
 * Returns
 * 	permission_refid
 * 	setting
 */
\db::setQuery('sysdata', 'MYSQL', 'set.user_system_permission:user_id,system_id,permission_id,setting', '

	INSERT INTO user_system_permission(user_id, system_id, permission_id, setting)
	VALUES (:user_id, :system_id, :permission_id, :setting)

');
?>
