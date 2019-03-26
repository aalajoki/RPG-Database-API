drop database if exists roleplay_db;
create database roleplay_db;

use roleplay_db;


create table Character_Class
(
    id  int primary key auto_increment,
    class VARCHAR(20) not null unique
);

create table Character_Race 
(
    id int primary key auto_increment,
    race varchar(20) not null unique
);

create table Guild_Type 
(
    id int primary key auto_increment,
    type varchar(3) not null
);

create table Guild_Rank 
(
    id int primary key auto_increment,
    rank varchar(20) not null unique
);



create table Player_Character
(
    id int primary key auto_increment,
    name varchar(30) not null unique,
    char_class int not null,
    foreign key (char_class) references Character_Class(id),
    char_race int not null,
    foreign key (char_race) references Character_Race(id),
    level int not null default 1
);

create table Guild
(
    id int primary key auto_increment,
    name varchar(20) not null unique,
    guild_type int not null,
    foreign key (guild_type) references Guild_Type(id),
    description varchar(100)
);

create table Guild_Membership
(
    guild_id int not null,
    foreign key (guild_id) references Guild(id),
    char_id int not null unique,
    foreign key (char_id) references Player_Character(id),
    char_rank int not null default 1,
    foreign key (char_rank) references Guild_Rank(id),
    joined timestamp default current_timestamp
);