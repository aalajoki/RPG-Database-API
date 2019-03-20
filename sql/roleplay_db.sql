drop database if exists roleplay_db;
create database roleplay_db;

use roleplay_db;


create table Character_Class
(
    id  int primary key auto_increment,
    name VARCHAR(20) not null unique
);

create table Character_Race 
(
    id int primary key auto_increment,
    name varchar(20) not null unique
);

create table Guild_Type 
(
    id int primary key auto_increment,
    name varchar(3) not null
);

create table Guild_Rank 
(
    id int primary key auto_increment,
    name varchar(20) not null unique
);



create table Player_Character
(
    id int primary key auto_increment,
    name varchar(30) not null unique,
    class int not null,
    foreign key (class) references Character_Class(id),
    race int not null,
    foreign key (race) references Character_Race(id),
    level int not null default 1
);

create table Guild
(
    id int primary key auto_increment,
    name varchar(20) not null unique,
    type int not null,
    foreign key (type) references Guild_Type(id),
    description varchar(100)
);

create table Guild_Membership
(
    guild_id int not null,
    foreign key (guild_id) references Guild(id),
    character_id int not null unique,
    foreign key (character_id) references Player_Character(id),
    rank int not null default 1,
    foreign key (rank) references Guild_Rank(id),
    joined timestamp default current_timestamp
);