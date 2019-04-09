USE roleplay_db;

-- Default system data. MANDATORY
INSERT INTO Character_Class (class)  
VALUES 
("Warrior"), 
("Rogue"), 
("Mage");

INSERT INTO Character_Race (race)  
VALUES
("Human"), 
("Dwarf"), 
("Elf");

INSERT INTO Guild_Type (type)  
VALUES 
("PvE"), 
("PvP"), 
("RP");

INSERT INTO Guild_Rank (rank)  
VALUES 
("Initiate"), 
("Member"), 
("Senior"), 
("Officer"), 
("Guild Master");



-- Player data - requires at least 3 players existing in the database
INSERT INTO Player_Character (player_id, name, char_class, char_race, level)  
VALUES 
(1, "Rob Johnson", 1, 1, 21), 
(2, "Long-Ears", 3, 3, 5), 
(2, "Ironpants", 1, 2, 1),
(3, "Sneaky", 2, 3, 24);

INSERT INTO Guild (name, guild_type, description)  
VALUES 
("Explorer's Guild", 1, "A casual guild for fun-loving people."),
("Sneaky Bandits", 3, "We steal stuff.");

INSERT INTO Guild_Membership (guild_id, char_id, char_rank)  
VALUES
(1, 3, 5),
(1, 1, 4),
(2, 4, 5);