use dorm;

drop table if exists users;
drop table if exists cars;

create table users (
	id int not null auto_increment primary key,
	name varchar(255) not null,
	email varchar(255) not null
);

create table cars (
	id int not null auto_increment primary key,
	user_id int not null,
	name varchar(255) not null
);
