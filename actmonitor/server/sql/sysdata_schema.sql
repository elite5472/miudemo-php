create table item_description(
	id int not null auto_increment,
	
	name varchar(255) null,
	description text(4096) null,
	
	primary key (id)
);

create table measure_type(
	id tinyint not null auto_increment,
	
	name varchar(32) not null,
	unit varchar(4) not null,
	
	primary key(id)
);

create table system(
	id int not null auto_increment,	
	refid varchar(64) not null,
	
	item_description_id int null,
	
	primary key (id),
	unique (refid),
	foreign key (item_description_id) references item_description(id)
);

create table system_update(
	id int not null auto_increment,
	
	system_id int not null,
	updated timestamp default current_timestamp on update current_timestamp,
	
	primary key (id),
	foreign key (system_id) references system(id)
);

create table system_component(
	id int not null auto_increment,
	refid varchar(64) not null,
	
	system_id int not null,
	item_description_id int null,
	
	primary key(id),
	unique (refid, system_id),
	foreign key (system_id) references system(id),
	foreign key (item_description_id) references item_description(id)
);

create table system_component_update(
	system_component_id int not null,
	system_update_id int not null,
	
	current_status varchar(32) not null,
	reason varchar(32) not null,
	info text(1536) null,
	
	primary key (system_component_id, system_update_id),
	foreign key (system_component_id) references system_component(id),
	foreign key (system_update_id) references system_update(id)
);

create table system_measure(
	id int not null auto_increment,
	refid varchar(4) not null,
	
	system_update_id int not null,
	
	measure decimal(2, 2) not null,

	primary key (id),
	unique(refid, system_update_id),
	foreign key (system_update_id) references system_update(id)
);

create table system_resource_measure(
	id int not null auto_increment,
	refid varchar(16) not null,
	
	system_update_id int not null,
	measure_type_id tinyint not null,
	
	total bigint not null,
	used bigint not null,
	
	check (used <= total),
	
	primary key (id),
	unique(refid, system_update_id),
	foreign key (system_update_id) references system_update(id),
	foreign key (measure_type_id) references measure_type(id)
	
);