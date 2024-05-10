<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * Stonespine Architects implementation : © Andrea "Paxcow" Vitagliano <andrea.vitagliano@gmail.com>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * StonespineArchitects game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */


/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/

$this->chamber_data = array(
// SQL structure: cardid, type, door_top, door_bottom, door_left, door_right, element_1, element_2, element_3, element_4,name,gold,solo
  array('cave',false,true,true,true,'blades','starneg',null,'slime',"name" => 'Smokehouse',3,'blue'),
  array('cave',false,true,true,true,'bear',null,'spike',null,"name" => 'Training Room',2,'blue'),
  array('cave',true,true,true,false,'pit',null,null,null,"name" => 'Secret Path',3,'blue'),
  array('cave',true,true,true,false,'kobold',null,'star1',null,"name" => "Furrow",2,'green'),
  array('cave',true,true,true,false,'gnoll',null,'gnoll',null,"name" => 'Gnoll Hideout',2,'green'),
  array('cave',true,true,true,false,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('cave',false,true,true,false,'blades',null,'bear','spike',"name" => 'Trap Storage',0,'blue'),
  array('cave',false,true,true,false,'blades',null,'star1','slime',"name" => 'Corner Room',3,'red'),
  array('cave',false,true,true,false,'starneg','ooze',null,'slime',"name" => 'Abandoned Mine',5,'red'),
  array('cave',false,true,false,true,'treasure',null,'fire',null,"name" => 'Scorch Alley',1,'red'),

  array('cave',false,true,false,true,null,'kobold','spike','starneg',"name" => 'Torment Room',5,'green'),
  array('cave',false,true,false,true,null,'star1',null,'slime',"name" => 'Crooked Path',3,'green'),
  array('cave',false,true,false,true,'slime','slime',null,null,"name" => 'Slime Trail',3,'red'),
  array('cave',false,false,true,true,'ooze','starneg','ooze','slime',"name" => 'Cellar',4,'red'),
  array('cave',false,false,true,true,'goblin',null,'gnoll',null,"name" => 'Derelict Site',3,'green'),
  array('cave',false,false,true,true,null,null,'goblin',null,"name" => 'Ambush Room',3,'blue'),
  array('cave',false,false,true,true,null,'goblin',null,'goblin',"name" => 'Tunnel',2,'blue'),
  array('cave',false,false,true,true,'treasure',null,'ooze','pit',"name" => 'Supply Room',2,'red'),
  array('cave',true,true,false,true,'bear',null,'gnoll','spike',"name" => 'Armory',1,'blue'),
  array('cave',true,true,false,true,null,'slime',null,'ooze',"name" => 'Burial Ground',3,'red'),

  array('cave',true,false,true,true,null,'kobold',null,'kobold',"name" => 'Kobold Quarters',2,'green'),
  array('cave',true,true,false,true,'ooze',null,'star1',null,"name" => 'Footpath',3,'green'),
  array('cave',true,true,false,true,null,'kobold',null,'kobold',"name" => 'Exercise Ward',2,'red'),
  array('cave',false,true,true,true,null,null,null,'treasure',"name" => 'Archive',1,'red'),
  array('cave',false,true,true,true,'treasure','treasure',null,null,"name" => 'Tomb',0,'red'),
  array('cave',true,true,false,false,null,'ooze',null,'slime',"name" => 'Lair',4,'red'),
  array('cave',true,true,false,false,null,'gnoll',null,'goblin',"name" => 'Watering Hole',3,'blue'),
  array('cave',true,true,false,false,'gnoll',null,'gnoll',null,"name" => 'Passage',3,'blue'),
  array('cave',true,true,false,false,'star1',null,'fire',null,"name" => 'Furnace',3,'green'),
  array('cave',true,true,true,true,'fire','ooze','treasure',null,"name" => 'Hearth',0,'red'),

  array('cave',true,false,false,false,null,'star1','treasure','star1',"name" => 'Sanctuary',0,'green'),
  array('cave',true,false,false,false,'slime','pit','ooze',null,"name" => 'Catacombs',5,'blue'),
  array('cave',true,false,false,false,null,null,'kobold',null,"name" => 'Alcove',5,'blue'),
  array('cave',true,false,false,false,'treasure','treasure',null,"name" => 'treasure','Treasury',0,'red'),
  array('cave',true,false,false,false,'star1','star1','star1',null,"name" => 'Ruins',1,'green'),
  array('cave',true,false,true,false,null,null,'star1','bear',"name" => 'Burrow',0,'red'),
  array('cave',true,false,true,false,'gnoll',null,'gnoll',null,"name" => 'Encampent',3,'green'),
  array('cave',true,false,true,false,null,null,null,'ooze',"name" => 'Ossiary',5,'red'),
  array('cave',true,false,true,false,'treasure',null,'slime',null,"name" => 'Coffers',1,'red'),
  array('cave',true,false,false,true,null,'ooze','pit',null,"name" => 'Concourse',3,'blue'),

  array('cave',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('cave',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('cave',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('cave',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('cave',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('cave',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('cave',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('cave',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('cave',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('cave',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),

  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),

  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),

  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),

  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),

  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  array('rock',true,true,true,0,'gnoll',null,'kobold',null,"name" => 'Cavern',1,'green'),
  
  );

$this->token_data = array(
  //shape,type,number
  "square" => array(
    1 => array('bear',3),
    2 => array('blade',3),
    3 => array('fire',3),
    4 => array('pit',3),  
    5 => array('spike',3),
  ),
  "circle" => array(
    1 => array('ooze',6),
    2 => array('slime',6),
    3 => array('star1',7),  
    4 => array('treasure',6),
  ),
  "oval" => array(
    1 => array('gnoll',4),  
    2 => array('goblin',4),
    3 => array('kobold',4),
    4 => array('secret',4),
  )
);


$this->market_data = array(
  //cost_top, top_shape_1, top_shape_2, top_shape_3, cost_bottom, bottom_shape
  1 => array (9,'square','square',null,4,'oval',null),
  2 => array (7,'circle','circle',null,3,'circle',null),
  3 => array (10,'oval','oval',null,4,'circle',null),
  4 => array (10,'circle','circle','circle',5,'square',null),
  5 => array (8,'square','circle',null,4,'oval',null),
  6 => array (7,'square','circle',null,3,'oval',null),
  7 => array (6,'circle','circle',null,4,'oval',null),
  8 => array (7,'circle','oval',null,4,'oval',null),
  9 => array (9,'square','oval',null,5,'circle','circle'),
  10 => array (8,'square','circle',null,4,'oval',null),
  11 => array (7,'circle','circle',null,4,'square',null),
  12 => array (9,'oval','oval',null,3,'circle',null),
  13 => array (6,'circle','circle',null,5,'square',null),
  14 => array (5,'square',null,null,4,'oval',null),
  15 => array (7,'square','circle',null,5,'square',null),
  16 => array (9,'square','oval',null,4,'oval',null),
  17 => array (7,'circle','oval',null,4,'square',null),
  18 => array (9,'oval','oval',null,7,'square','circle')
);

$this->market_layouts = array(
  //types of different layouts for market cards, for CSS purpose
  1=>"squaresquare",
  2=>"circlecircle",
  3=>"ovaloval",
  4=>"circlecirclecircle",
  5=>"squarecircle",
  6=>"circleoval",
  7=>"squareoval",
  8=>"square",
  9=>"oval",
  10=>"circle"
);

$this->blueprint_data = array(
  //col_start, row1 [col1 to col4]...row4 [col1 to col4],col_end
  1 => array("start" => 2,
    "row1" => array("col1" => null,"col2" => null,"col3" => 'treasure',"col4" => 'cave'),
    "row2" => array("col1" => "goblin","col2" => "cave","col3" => null,"col4" => null),
    "row3" => array("col1" => null,"col2" => null,"col3" => 'cave',"col4" => 'ooze'),
    "row4" => array("col1" => "cave","col2" => "slime","col3" => null,"col4" => null),
    "end" => 4
  ),
  2 => array("start" => 3,
    "row1" => array("col1" => "star","col2" => "cave","col3" => null,"col4" => null),
    "row2" => array("col1" => "cave","col2" => null,"col3" => null,"col4" => "trap"),
    "row3" => array("col1" => null,"col2" => null,"col3" => 'goblin',"col4" => 'cave'),
    "row4" => array("col1" => null,"col2" => "treasure","col3" => "cave","col4" => null),
    "end" => 1
  ),
  3 => array("start" => 2,
    "row1" => array("col1" => null,"col2" => null,"col3" => "stone","col4" => "star"),
    "row2" => array("col1" => "trap","col2" => null,"col3" => null,"col4" => "stone"),
    "row3" => array("col1" => "stone","col2" => "gnoll","col3" => null,"col4" => null),
    "row4" => array("col1" => null,"col2" => "stone","col3" => "kobold","col4" => null),
    "end" => 4
  ),
  4 => array("start" => 3,
    "row1" => array("col1" => null,"col2" => "kobold","col3" => null,"col4" => "stone"),
    "row2" => array("col1" => "goblin","col2" => null,"col3" => "stone","col4" => null),
    "row3" => array("col1" => null,"col2" => "stone","col3" => "star","col4" => null),
    "row4" => array("col1" => "stone","col2" => null,"col3" => null,"col4" => "gnoll"),
    "end" => 2
  ),
  5 => array("start" => 2,
    "row1" => array("col1" => "stone","col2" => null,"col3" => "ooze","col4" => null),
    "row2" => array("col1" => null,"col2" => null,"col3" => "stone","col4" => "treasure"),
    "row3" => array("col1" => "slime","col2" => "stone","col3" => null,"col4" => null),
    "row4" => array("col1" => null,"col2" => "gnoll","col3" => null,"col4" => "stone"),
    "end" => 2
  ),
  6 => array("start" => 2,
    "row1" => array("col1" => "treasure","col2" => null,"col3" => "cave","col4" => null),
    "row2" => array("col1" => "cave","col2" => null,"col3" => "slime","col4" => null),
    "row3" => array("col1" => null,"col2" => "ooze","col3" => null,"col4" => "cave"),
    "row4" => array("col1" => null,"col2" => "cave","col3" => null,"col4" => "star"),
    "end" => 3
  ),
  7 => array("start" => 3,
    "row1" => array("col1" => null,"col2" => "stone","col3" => null,"col4" => "trap"),
    "row2" => array("col1" => null,"col2" => null,"col3" => "slime","col4" => "stone"),
    "row3" => array("col1" => "stone","col2" => "kobold","col3" => null,"col4" => null),
    "row4" => array("col1" => "ooze","col2" => null,"col3" => "stone","col4" => null),
    "end" => 2
  ),
  8 => array("start" => 2,
    "row1" => array("col1" => "cave","col2" => null,"col3" => "kobold","col4" => null),
    "row2" => array("col1" => "trap","col2" => "cave","col3" => null,"col4" => null),
    "row3" => array("col1" => null,"col2" => null,"col3" => "cave","col4" => "goblin"),
    "row4" => array("col1" => null,"col2" => "gnoll","col3" => null,"col4" => "cave"),
    "end" => 1
  ),

);