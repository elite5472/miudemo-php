<?php
// ---- SECURITY QUERIES -----------------------------------------------------//
// User and authentication related queries go inside this file.

/**
 * get.user:refid
 *
 * Gets an existing user.
 *
 * Parameters:
 * refid
 *
 * Returns:
 * id
 * refid
 * email
 * user_class_refid
 * user_class_name
 */
\db::setQuery('sysdata', 'MYSQL', 'get.user:refid', '
	SELECT
		user.id AS id,
		user.refid AS refid,
		user.email AS email,
		user.password AS password,
		user_class.refid AS user_class_refid
	FROM user
		INNER JOIN user_class ON (user.user_class_id = user_class.id)
	WHERE user.refid = :refid
	LIMIT 1;
');

/**
 * get.user:id
 *
 * Gets an existing user.
 *
 * Parameters:
 * id
 *
 * Returns:
 * id
 * refid
 * email
 * user_class_refid
 * user_class_name
 */
\db::setQuery('sysdata', 'MYSQL', 'get.user:id', '
	SELECT
		user.id AS id,
		user.refid AS refid,
		user.email AS email,
		user.password AS password,
		user_class.refid AS user_class_refid
	FROM user
		INNER JOIN user_class ON (user.user_class_id = user_class.id)
	WHERE user.id = :id
	LIMIT 1;
');

/**
 * get.user.count
 *
 * Gets the total number of registered users.
 *
 * Returns:
 * registered_users
 */
\db::setQuery('sysdata', 'MYSQL', 'get.user.count', '
	SELECT COUNT(*) AS registered_users FROM user;
');

/**
 * set.user:refid,email,password,user_class_refid
 *
 * Creates an user.
 *
 * Parameters:
 * refid
 * email
 * password
 * user_class_id
 *
 * Returns:
 * null
 */
\db::setQuery('sysdata', 'MYSQL', 'set.user:refid,email,password,user_class_id', '
	INSERT INTO user (refid, email, password, user_class_id)
	VALUES
		(:refid, :email, :password, :user_class_id);
');

/**
 * get.user_class:refid
 *
 * Parameters:
 * refid
 *
 * Returns:
 * id
 * refid
 * name
 * description
 */
\db::setQuery('sysdata', 'MYSQL', 'get.user_class:refid', '

	SELECT
		user_class.id AS id,
		user_class.refid AS refid,
		item_description.name AS name,
		item_description.description AS description

	FROM user_class
		LEFT OUTER JOIN item_description
			ON (user_class.item_description_id = item_description.id)

	WHERE refid = :refid

');

/**
 * get.user_class.all
 *
 * Gets all existing user classes.
 *
 * Parameters:
 * none
 *
 * Returns:
 * id
 * refid
 * name
 * description
 */
\db::setQuery('sysdata', 'MYSQL', 'get.user_class.all', '
	SELECT
		user_class.id AS id,
		user_class.refid AS refid,
		item_description.name AS name,
		item_description.description AS description

	FROM user_class
		LEFT OUTER JOIN item_description ON (user_class.item_description_id = item_description.id);
');

/**
 * set.user_class:refid,item_description_id
 *
 * Parameters:
 * refid
 * item_description_id
 *
 * Returns
 * null
 */
\db::setQuery('sysdata', 'MYSQL', 'set.user_class:refid,item_description_id', '
	INSERT INTO user_class(refid, item_description_id)
	VALUES (:refid, :item_description_id);
');
/**
 * set.session:refid,user_id,expires
 *
 * Creates a session for the given user.
 *
 * Parameters:
 * refid
 * user_id
 * expires (0 | 1)
 *
 * Returns
 * null
 */
\db::setQuery('sysdata', 'MYSQL', 'set.session:refid,user_id,expires', '
	INSERT INTO session (refid, user_id, expires) VALUES (:refid, :user_id, :expires);
');

/**
 * get.session:refid
 *
 * Returns a requested session.
 *
 * Parameters:
 * refid
 *
 * Returns
 * id
 * refid
 * user_id
 * created (UNIX_TIMESTAMP)
 * updated (UNIX_TIMESTAMP)
 * expires
 * is_expired
 */
\db::setQuery('sysdata', 'MYSQL', 'get.session:refid', '
	SELECT
		session.id AS id,
		session.refid AS refid,
		session.user_id AS user_id,
		UNIX_TIMESTAMP(session.created) AS created,
		UNIX_TIMESTAMP(session.updated) AS updated,
		session.expires AS expires,
		session.is_expired AS is_expired
	FROM session
	WHERE refid = :refid;
	LIMIT 1;
');

/**
 * update.session.refresh:refid
 *
 * Updates the 'updated' timestamp of the session.
 *
 * Parameters:
 * refid
 *
 * Returns:
 * null
 */
\db::setQuery('sysdata', 'MYSQL', 'update.session.refresh:refid', '
	UPDATE session
	SET updated = NOW()
	WHERE refid = :refid;
');

/**
 * update.session.expire:user_id
 *
 * Expires all sessions active for the user.
 *
 * Parameters:
 * user_id
 *
 * Returns:
 * null
 */
\db::setQuery('sysdata', 'MYSQL', 'update.session.expire:user_id', '
UPDATE session
SET is_expired = TRUE
WHERE user_id = :user_id AND is_expired = FALSE;
');

/**
 * get.register_key:refid
 *
 * Parameters:
 * refid
 *
 * Returns:
 * refid
 * created (UNIX_TIMESTAMP)
 * is_expired
 * user_refid
 * user_class_refid
 */
\db::setQuery('sysdata', 'MYSQL', 'get.register_key:refid', '
	SELECT
		register_key.refid AS refid,
		UNIX_TIMESTAMP(register_key.created) AS created,
		register_key.is_expired AS is_expired,
		user.refid AS user_refid,
		user_class.refid AS user_class_refid
	FROM register_key
		INNER JOIN user_class ON (register_key.user_class_id = user_class.id)
		LEFT OUTER JOIN user ON (register_key.user_id = user.id)
	WHERE register_key.refid = :refid
	LIMIT 1;
');

/**
 * update.register_key.expire:refid,user_id
 *
 * Expires a register key and assigns it to an user.
 *
 * Parameters:
 * refid
 * user_id
 *
 * Returns
 * null
 */
\db::setQuery('sysdata', 'MYSQL', 'update.register_key.expire:refid,user_id', '
	UPDATE register_key
	SET is_expired = TRUE, user_id = :user_id
	WHERE refid = :refid AND is_expired = FALSE;
');

/**
 * set.register_key:refid,user_class_id
 *
 * Creates a new register key set to create an user of a given class.
 *
 * Parameters
 * user_class_id
 *
 * Returns
 * null
 */
\db::setQuery('sysdata', 'MYSQL', 'set.register_key:refid,user_class_id', <<<_END
INSERT INTO register_key(refid, user_class_id) VALUES (:refid, :user_class_id);
_END
);

/**
 * get.permission:refid
 *
 * Parameters:
 * refid
 *
 * Returns:
 * id
 * refid
 * name
 * description
 */
\db::setQuery('sysdata', 'MYSQL', 'get.permission:refid', '

	SELECT
		permission.id AS id,
		permission.refid AS refid,
		item_description.name AS name,
		item_description.description AS description

	FROM permission
		LEFT OUTER JOIN item_description
			ON (permission.item_description_id = item_description.id)

	WHERE refid = :refid;

');

/**
 * get.permission.all
 *
 * Returns
 * id
 * refid
 * name
 * description
 */
\db::setQuery('sysdata', 'MYSQL', 'get.permission.all', '
	SELECT
		permission.id AS id,
		permission.refid AS refid,
		item_description.name AS name,
		item_description.description AS description

	FROM permission
		LEFT OUTER JOIN item_description
			ON (permission.item_description_id = item_description.id);

');

/**
 * set.permission:refid,item_description_id
 *
 * Parameters:
 * refid
 * item_description_id
 *
 * Returns
 * null
 */
\db::setQuery('sysdata', 'MYSQL', 'set.permission:refid,item_description_id', '
	INSERT INTO permission(refid, item_description_id)
	VALUES (:refid, :item_description_id);
');

/**
 * get.user_class_permission:user_class_id
 *
 * Parameters:
 * user_class_id
 *
 * Returns:
 * user_class_id
 * permission_id
 * permission_refid
 * setting
 */
\db::setQuery('sysdata', 'MYSQL', 'get.user_class_permission:user_class_id', '
	SELECT
		user_class_permission.user_class_id AS user_class_id,
		user_class_permission.permission_id AS permission_id,
		permission.refid AS permission_refid,
		user_class_permission.setting AS setting

	FROM user_class_permission
		INNER JOIN permission
			ON (permission.id = user_class_permission.permission_id)

	WHERE
		user_class_permission.user_class_id = :user_class_id;
');

/**
 * set.user_class_permission:user_class_id,permission_id,setting
 *
 * Parameters:
 * user_class_id
 * permission_id
 * setting
 */
\db::setQuery('sysdata', 'MYSQL', 'set.user_class_permission:user_class_id,permission_id,setting', '
	INSERT INTO user_class_permission(user_class_id, permission_id, setting)
	VALUES (:user_class_id, :permission_id, :setting);
');

/**
 * update.user_class_permission:user_class_id,permission_id,setting
 *
 * Parameters:
 * user_class_id
 * permission_id
 * setting
 */
\db::setQuery('sysdata', 'MYSQL', 'update.user_class_permission:user_class_id,permission_id,setting', '
	UPDATE user_class_permission
	SET setting = :setting
	WHERE user_class_id = :user_class_id AND permission_id = :permission_id
');

/**
 * get.preference:refid
 *
 * Parameters:
 * refid
 *
 * Returns:
 * id
 * refid
 * name
 * description
 */
\db::setQuery('sysdata', 'MYSQL', 'get.preference:refid', '

	SELECT
		preference.id AS id,
		preference.refid AS refid,
		item_description.name AS name,
		item_description.description AS description

	FROM preference
		LEFT OUTER JOIN item_description
			ON (preference.item_description_id = item_description.id)

	WHERE refid = :refid;

');

/**
 * get.preference.all
 *
 * Returns
 * id
 * refid
 * name
 * description
 */
\db::setQuery('sysdata', 'MYSQL', 'get.preference.all', '
	SELECT
		preference.id AS id,
		preference.refid AS refid,
		item_description.name AS name,
		item_description.description AS description

	FROM preference
		LEFT OUTER JOIN item_description
			ON (preference.item_description_id = item_description.id);

');

/**
 * set.preference:refid,item_description_id
 *
 * Parameters:
 * refid
 * item_description_id
 *
 * Returns
 * null
 */
\db::setQuery('sysdata', 'MYSQL', 'set.preference:refid,item_description_id', '
	INSERT INTO preference(refid, item_description_id)
	VALUES (:refid, :item_description_id);
');

/**
 * get.user_preference:user_id
 *
 * Parameters:
 * user_id
 *
 * Returns:
 * user_id
 * preference_id
 * preference_refid
 * setting
 */
\db::setQuery('sysdata', 'MYSQL', 'get.user_preference:user_id', '
	SELECT
		user_preference.user_id AS user_id,
		user_preference.preference_id AS preference_id,
		preference.refid AS preference_refid,
		user_preference.setting AS setting

	FROM user_preference
		INNER JOIN preference
			ON (preference.id = user_preference.preference_id)

	WHERE
		user_preference.user_id = :user_id;
');

/**
 * set.user_preference:user_id,preference_id,setting
 *
 * Parameters:
 * user_id
 * preference_id
 * setting
 */
\db::setQuery('sysdata', 'MYSQL', 'set.user_preference:user_id,preference_id,setting', '
	INSERT INTO user_preference(user_id, preference_id, setting)
	VALUES (:user_id, :preference_id, :setting);
');

/**
 * update.user_preference:user_id,preference_id,setting
 *
 * Parameters:
 * user_id
 * preference_id
 * setting
 */
\db::setQuery('sysdata', 'MYSQL', 'update.user_preference:user_id,preference_id,setting', '
	UPDATE user_preference
	SET setting = :setting
	WHERE user_id = :user_id AND preference_id = :preference_id
');

/**
 * get.server_session:refid
 *
 * Parameters:
 * 	refid
 */
\db::setQuery('sysdata', 'MYSQL', 'get.server_session:refid', '
	SELECT * FROM server_session WHERE refid = :refid
');

/**
 * set.server_session:refid,system_id
 *
 * Creates a new server session.
 *
 * Parameters:
 * 	refid
 * 	system_id
 */
\db::setQuery('sysdata', 'MYSQL', 'set.server_session:refid,system_id', '
	INSERT INTO server_session(refid, system_id) VALUES(:refid, :system_id)
');

/**
 * update.server_session:refid
 *
 * Parameters:
 * 	refid
 */
\db::setQuery('sysdata', 'MYSQL', 'update.server_session:refid', '
	UPDATE server_session
	SET updated = NOW()
	WHERE refid = :refid
');

/**
 * update.server_session.expire:refid
 *
 * Parameters:
 * 	refid
 */
\db::setQuery('sysdata', 'MYSQL', 'update.server_session.expire:refid', '
	UPDATE server_session
	SET is_expired = true
	WHERE refid = :refid
');
?>
