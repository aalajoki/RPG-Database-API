DROP DATABASE IF EXISTS roleplay_db;
CREATE DATABASE roleplay_db;

use roleplay_db;


CREATE TABLE Character_Class
(
    id INT AUTO_INCREMENT,
    class VARCHAR(20) NOT NULL UNIQUE,
    PRIMARY KEY (id)
);

CREATE TABLE Character_Race 
(
    id INT AUTO_INCREMENT,
    race VARCHAR(20) NOT NULL UNIQUE,
    PRIMARY KEY (id)
);

CREATE TABLE Guild_Type 
(
    id INT AUTO_INCREMENT,
    type VARCHAR(3) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE Guild_Rank 
(
    id INT NOT NULL AUTO_INCREMENT,
    rank VARCHAR(20) NOT NULL UNIQUE,
    PRIMARY KEY (id)
);


CREATE TABLE Player
(
    id INT AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE Player_Character
(
    id INT AUTO_INCREMENT,
    player_id INT NOT NULL,
    FOREIGN KEY (player_id) REFERENCES Player(id),
    name VARCHAR(30) NOT NULL UNIQUE,
    char_race INT NOT NULL,
    FOREIGN KEY (char_race) REFERENCES Character_Race(id),
    char_class INT NOT NULL,
    FOREIGN KEY (char_class) REFERENCES Character_Class(id),
    level INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
);

CREATE TABLE Guild
(
    id INT AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL UNIQUE,
    guild_type INT NOT NULL,
    FOREIGN KEY (guild_type) REFERENCES Guild_Type(id),
    description VARCHAR(100),
    PRIMARY KEY (id)
);

CREATE TABLE Guild_Membership
(
    guild_id INT AUTO_INCREMENT,
    FOREIGN KEY (guild_id) REFERENCES Guild(id),
    char_id INT NOT NULL UNIQUE,
    FOREIGN KEY (char_id) REFERENCES Player_Character(id),
    char_rank INT NOT NULL DEFAULT 1,
    FOREIGN KEY (char_rank) REFERENCES Guild_Rank(id),
    joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (guild_id, char_id)
);