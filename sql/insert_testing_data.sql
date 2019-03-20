use roleplay_db;

-- Default system data
insert into Character_Class (name)  
values ("Warrior");
insert into Character_Class (name)  
values ("Rogue");
insert into Character_Class (name)  
values ("Mage");

insert into Character_Race (name)  
values ("Human");
insert into Character_Race (name)  
values ("Dwarf");
insert into Character_Race (name)  
values ("Elf");

insert into Guild_Type (name)  
values ("PvE");
insert into Guild_Type (name)  
values ("PvP");
insert into Guild_Type (name)  
values ("RP");

insert into Guild_Rank (name)  
values ("Initiate");
insert into Guild_Rank (name)  
values ("Member");
insert into Guild_Rank (name)  
values ("Senior");
insert into Guild_Rank (name)  
values ("Officer");
insert into Guild_Rank (name)  
values ("Guild Master");


-- Player data
insert into Player_Character (name, class, race, level)  
values ("Rob Johnson", 1, 1, 21);
insert into Player_Character (name, class, race, level)  
values ("Long-Ears", 3, 3, 5);
insert into Player_Character (name, class, race)  
values ("Ironpants", 1, 2);
insert into Player_Character (name, class, race, level)  
values ("Sneaky", 2, 3, 24);

insert into Guild (name, type, description)  
values ("Explorer's Guild", 1, "A casual guild for fun-loving people.");
insert into Guild (name, type, description)  
values ("Sneaky Bandits", 3, "We steal stuff.");


insert into Guild_Membership (guild_id, character_id, rank)  
values (1, 3, 5);
insert into Guild_Membership (guild_id, character_id, rank)  
values (1, 1, 4);
insert into Guild_Membership (guild_id, character_id, rank)  
values (2, 4, 5);