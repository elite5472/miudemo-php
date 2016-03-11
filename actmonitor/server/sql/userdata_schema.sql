create table user_class(
	id int not null auto_increment,
	refid varchar(64) not null,

	item_description_id int null,

	primary key (id),
	unique(refid),
	foreign key (item_description_id) references item_description(id)
);

create table permission(
	id int not null auto_increment,
	refid varchar(128) not null,

	item_description_id int null,

	primary key (id),
	unique(refid),
	foreign key (item_description_id) references item_description(id)
);

create table user_class_permission(
	user_class_id int not null,
	permission_id int not null,

	setting varchar(128) not null,

	primary key(user_class_id, permission_id),
	foreign key (user_class_id) references user_class(id),
	foreign key (permission_id) references permission(id)
);

create table user(
	id int not null auto_increment,
	refid varchar(64) not null,

	user_class_id int not null,

	email varchar(128) not null,
	password varchar(128) not null,

	is_frozen boolean default false,

	primary key (id),
	unique (refid),
	unique (email),
	foreign key (user_class_id) references user_class(id)
);

create table preference(
	id int not null auto_increment,
	refid varchar(128) not null,

	item_description_id int null,

	primary key (id),
	unique(refid),
	foreign key (item_description_id) references item_description(id)
);

create table user_preference(
	user_id int not null,
	preference_id int not null,

	setting varchar(128) not null,

	primary key(user_id, preference_id),
	foreign key (user_id) references user (id),
	foreign key (preference_id) references preference(id)
);

create table session(
	id int not null auto_increment,
	refid varchar(36) not null,

	user_id int not null,

	created timestamp default current_timestamp,
	updated timestamp null,
	expires boolean default true,
	is_expired boolean default false,

	primary key (id),
	unique (refid),
	foreign key (user_id) references user(id)
);

DROP TRIGGER IF EXISTS `update_session_timestamp_trigger`;
DELIMITER //
CREATE TRIGGER `update_session_timestamp_trigger` BEFORE UPDATE ON `session`
 FOR EACH ROW SET NEW.`updated` = NOW()
//
DELIMITER ;

create table server_session(
	refid varchar(36) not null,

	system_id int not null,

	created timestamp default current_timestamp,
	updated timestamp null,
	is_expired boolean default false,

	primary key (refid),
	foreign key (system_id) references system(id)
);

create table user_system(
	user_id int not null,
	system_id int not null,

	primary key(user_id, system_id),
	foreign key(user_id) references user(id),
	foreign key(system_id) references system(id)
);

create table user_system_permission(
	user_id int not null,
	system_id int not null,

	permission_id int not null,
	setting varchar(128) not null,

	primary key (user_id, system_id),
	foreign key (user_id, system_id) references user_system(user_id, system_id),
	foreign key (permission_id) references permission(id)
);

create table register_key(
	refid varchar(36) not null,

	user_class_id int not null,
	user_id int null,

	created timestamp default current_timestamp,
	is_expired boolean default false,

	primary key(refid),
	unique(user_id),
	foreign key(user_class_id) references user_class(id),
	foreign key(user_id) references user(id)
);
