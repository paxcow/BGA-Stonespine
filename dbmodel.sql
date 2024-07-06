
-- ------
-- BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
-- Stonespine Architects implementation : © Andrea "Paxcow" Vitagliano <andrea.vitagliano@gmail.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.

-- Example 1: create a standard "card" table to be used with the "Deck" tools (see example game "hearts"):

-- CREATE TABLE IF NOT EXISTS `card` (
--   `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `card_type` varchar(16) NOT NULL,
--   `card_type_arg` int(11) NOT NULL,
--   `card_location` varchar(16) NOT NULL,
--   `card_location_arg` int(11) NOT NULL,
--   PRIMARY KEY (`card_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- Example 2: add a custom field to the standard "player" table

ALTER TABLE `player`
ADD `gold` INT UNSIGNED NOT NULL DEFAULT 0,
ADD `prev_player` INT UNSIGNED NOT NULL default 0,
ADD `next_player` INT UNSIGNED NOT NULL default 0,
ADD `priority` INT UNSIGNED NOT NULL default 0,
ADD `new_priority` INT UNSIGNED NOT NULL default 0;

CREATE table IF NOT EXISTS `actions` (
  `action_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `action_json` varchar(65535) NOT NULL,
  PRIMARY KEY (`action_id`)

)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `chamber` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL, /*will be used to define the type of chamber*/
  `card_type_arg` int(11) NOT NULL, 
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  `door_top` boolean, 
  `door_bottom` boolean,
  `door_left` boolean,
  `door_right` boolean,
  `element_1` varchar(16), /*standard quadrant notation, 1 is top right, 2 top left, 3 bottom left, 4 bottom right*/
  `element_2` varchar(16),
  `element_3` varchar(16),
  `element_4` varchar(16),
  `chamber_name` varchar(16),
  `gold_value` int(1) NOT NULL DEFAULT 0,
  `solo_rune` varchar(16), /*for solo mode*/
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `dungeon` (
  `dungeon_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) unsigned NOT NULL,
  `row` int(1) unsigned NOT NULL,
  `column` int(1) UNSIGNED NOT NULL,
  `chamber_id` int(10) unsigned not null, /*for start and end door, chamber_id is 9999*/ 
  FOREIGN KEY (`player_id`) REFERENCES `player`(`player_id`),
  PRIMARY KEY (`dungeon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `blueprint` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  `scoring` json NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `challenge` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `goal` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `market` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  `top_cost` int(1) NOT NULL,
  `bottom_cost` int(1) NOT NULL,
  `token_top_1` ENUM ("circle","square","oval"),
  `token_top_2` ENUM ("circle","square","oval"),
  `token_top_3` ENUM ("circle","square","oval"),
  `token_bottom_1` ENUM ("circle","square","oval"), 
  `token_bottom_2` ENUM ("circle","square","oval"), 
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `token` (
  `token_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token_shape` varchar(16) NOT NULL,
  `token_face` varchar(16) NOT NULL,
  `token_type` int(11) NOT NULL,
  `token_location` varchar(16) NOT NULL, /*market card_id or chamber card_id or player_id*/
  `token_location_type` varchar(16) NOT NULL, /* "player", "market", "chamber", "reserve"*/
  `token_location_slot` int(11), /*slot on the market card, or quadrant of the chamber, null if in player's area.*/
/*
Quadrant arg
1 -- Quadrant 1
2 -- Quadrant 2
3 -- Quadrant 3
4 -- Quadrant 4
Slot on market card
10 -- top center
11 -- top left 
12 -- top right
13 -- top center (3 slots arrangement)
20 -- bottom center
21 -- bottom left
22 -- bottom right
*/
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
