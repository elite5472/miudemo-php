<?php

/*
 * set.item_description:name,description
 * 
 * Parameters:
 * 	name
 * 	description
 *
 */
\db::setQuery('sysdata', 'MYSQL', 'set.item_description:name,description', '
	INSERT INTO item_description (name, description)
	VALUES (:name, :description)
');

/*
 * get.item_description.last
 * 
 * Returns the last item_description inserted.
 */
\db::setQuery('sysdata', 'MYSQL', 'get.item_description.last', '
	SELECT * FROM item_description ORDER BY id DESC LIMIT 1;	
');
?>