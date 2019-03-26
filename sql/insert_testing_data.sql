use roleplay_db;

-- Default system data
insert into Character_Class (class)  
values 
("Warrior"), 
("Rogue"), 
("Mage");

insert into Character_Race (race)  
values 
("Human"), 
("Dwarf"), 
("Elf");

insert into Guild_Type (type)  
values 
("PvE"), 
("PvP"), 
("RP");

insert into Guild_Rank (rank)  
values 
("Initiate"), 
("Member"), 
("Senior"), 
("Officer"), 
("Guild Master");



-- Player data
insert into Player_Character (name, char_class, char_race, level)  
values 
("Rob Johnson", 1, 1, 21), 
("Long-Ears", 3, 3, 5), 
("Ironpants", 1, 2, 1),
("Sneaky", 2, 3, 24);

insert into Guild (name, guild_type, description)  
values 
("Explorer's Guild", 1, "A casual guild for fun-loving people."),
("Sneaky Bandits", 3, "We steal stuff.");

insert into Guild_Membership (guild_id, char_id, char_rank)  
values 
(1, 3, 5),
(1, 1, 4),
(2, 4, 5);