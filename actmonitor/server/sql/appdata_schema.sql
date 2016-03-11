create table system_event(
	system_refid varchar(64) not null,
	updated timestamp default current_timestamp on update current_timestamp,
	content text not null,
	
	bool hasComponentUpdated not null,
	bool hasMeasure not null,
	
	primary key(system_refid)
);