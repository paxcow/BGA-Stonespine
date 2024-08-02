{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
-- Stonespine Architects implementation : © Andrea "Paxcow" Vitagliano <andrea.vitagliano@gmail.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    stonespinearchitects_stonespinearchitects.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->

<div id="main_wrapper" class="wrapper column">
  <a id="anchor_my_player_area"></a>
  <div id="my_player_area" class="wrapper player_area">
    <div id="my_hand_wrapper" class="wrapper column whiteblock">
      <h2>{MY_HAND}</h2>
      <div id="my_chamber_hand" class="wrapper player_hand_chambers"></div>
      <div id="my_challenge_hand" class="wrapper player_hand_chambers"></div>
    </div>
    <div id="my_dungeon_wrapper" class="dungeon_wrapper wrapper column">
      <div id="my_dungeon_frame_top" class="dungeon_frame top" face="cave" data-player="{THIS_PLAYER_ID}" data-color="{THIS_PLAYER_COLOR}">
        <div class="dungeon_column" data-column="1"></div>
        <div class="dungeon_column" data-column="2"></div>
        <div class="dungeon_column" data-column="3"></div>
        <div class="dungeon_column" data-column="4"></div>
      </div>
      <div id="my_dungeon" class="dungeon_grid_container">
        <div id="my_passage_overlay" class="overlay"></div>
      </div>
      <div id="my_dungeon_frame_bottom" class="dungeon_frame bottom" face="cave" data-player="{PLAYER_ID}" data-color="{PLAYER_COLOR}"></div>
    </div>
    <div id="my_right_player_area" class="wrapper column">
      <div id="my_blueprint_hand" class="wrapper"></div>
      <div id="my_token_staging"></div>
    </div>
  </div>

  <div id="score_board" class="wrapper">
    <div id="market_wrapper" class="wrapper">
      <!-- need a wrapper inside a wrapper to manage flexwrap-->
      <div id="market_cards_wrapper" class="wrapper">
        <div id="market_deck" class="wrapper"></div>
      </div>
    </div>

    <div id="central_wrapper" class="wrapper column">
      <div id="upper_central_wrapper" class="deck wrapper nowrap">
        <div id="token_wrapper" class="wrapper nowrap">
          <div id="oval_token_pile" class="token_pile"></div>
          <div id="square_token_pile" class="token_pile"></div>
          <div id="circle_token_pile" class="token_pile"></div>
        </div>
        <div id="chamber_wrapper" class="wrapper"></div>
      </div>
      <div id="board_wrapper" class="wrapper">
        <div id="board">
          <div class="marker_pos gold_column_0 gold_row_1" id="gold_0"></div>
          <div class="marker_pos gold_column_1 gold_row_1" id="gold_1"></div>
          <div class="marker_pos gold_column_2 gold_row_1" id="gold_2"></div>
          <div class="marker_pos gold_column_3 gold_row_1" id="gold_3"></div>
          <div class="marker_pos gold_column_4 gold_row_1" id="gold_4"></div>
          <div class="marker_pos gold_column_5 gold_row_1" id="gold_5"></div>
          <div class="marker_pos gold_column_6 gold_row_1" id="gold_6"></div>
          <div class="marker_pos gold_column_7 gold_row_1" id="gold_7"></div>
          <div class="marker_pos gold_column_8 gold_row_1" id="gold_8"></div>
          <div class="marker_pos gold_column_9 gold_row_1" id="gold_9"></div>
          <div class="marker_pos" id="gold_10"></div>
          <div class="marker_pos gold_column_1 gold_row_2" id="gold_19"></div>
          <div class="marker_pos gold_column_2 gold_row_2" id="gold_18"></div>
          <div class="marker_pos gold_column_3 gold_row_2" id="gold_17"></div>
          <div class="marker_pos gold_column_4 gold_row_2" id="gold_16"></div>
          <div class="marker_pos gold_column_5 gold_row_2" id="gold_15"></div>
          <div class="marker_pos gold_column_6 gold_row_2" id="gold_14"></div>
          <div class="marker_pos gold_column_7 gold_row_2" id="gold_13"></div>
          <div class="marker_pos gold_column_8 gold_row_2" id="gold_12"></div>
          <div class="marker_pos gold_column_9 gold_row_2" id="gold_11"></div>
          <div class="marker_pos" id="gold_20"></div>
          <div class="marker_pos gold_column_1 gold_row_3" id="gold_21"></div>
          <div class="marker_pos gold_column_2 gold_row_3" id="gold_22"></div>
          <div class="marker_pos gold_column_3 gold_row_3" id="gold_23"></div>
          <div class="marker_pos gold_column_4 gold_row_3" id="gold_24"></div>
          <div class="marker_pos gold_column_5 gold_row_3" id="gold_25"></div>
          <div class="marker_pos gold_column_6 gold_row_3" id="gold_26"></div>
          <div class="marker_pos gold_column_7 gold_row_3" id="gold_27"></div>
          <div class="marker_pos gold_column_8 gold_row_3" id="gold_28"></div>
          <div class="marker_pos gold_column_9 gold_row_3" id="gold_29"></div>
          <div class="marker_pos gold_column_10 gold_row_3" id="gold_30"></div>
          <div class="marker_pos current_priority_marker priority_1" id="priority_curr_1"></div>
          <div class="marker_pos current_priority_marker priority_2" id="priority_curr_2"></div>
          <div class="marker_pos current_priority_marker priority_3" id="priority_curr_3"></div>
          <div class="marker_pos current_priority_marker priority_4" id="priority_curr_4"></div>
          <div class="marker_pos current_priority_marker priority_5" id="priority_curr_5"></div>
          <div class="marker_pos next_priority_marker priority_1" id="priority_next_1"></div>
          <div class="marker_pos next_priority_marker priority_2" id="priority_next_2"></div>
          <div class="marker_pos next_priority_marker priority_3" id="priority_next_3"></div>
          <div class="marker_pos next_priority_marker priority_4" id="priority_next_4"></div>
          <div class="marker_pos next_priority_marker priority_5" id="priority_next_5"></div>
          <div id="goal_card_placeholder"></div>
        </div>
      </div>
    </div>
    <div id="challenge_wrapper" class="wrapper">
      <div id="challenge_cards_wrapper" class="wrapper">
        <div id="challenge_deck" class="wrapper"></div>
      </div>
    </div>
  </div>

  <!-- BEGIN OTHER_PLAYERS -->

  <a id="anchor_player_{PLAYER_ID}"></a>
  <div id="player_{PLAYER_ID}_area" class="wrapper nowrap player_area">
    <div id="player_{PLAYER_ID}_hand_wrapper" class="wrapper column whiteblock">
      <h2>{PLAYER_NAME}</h2>
      <div id="{PLAYER_ID}_chamber_hand" class="wrapper player_hand_chambers"></div>
      <div id="{PLAYER_ID}_challenge_hand" class="wrapper player_hand_challenges"></div>
    </div>
    <div id="dungeon_{PLAYER_ID}_wrapper" class="dungeon_wrapper wrapper column">
      <div id="dungeon_{PLAYER_ID}_frame_top" class="dungeon_frame top" face="cave" data-player="{PLAYER_ID}" data-color="{PLAYER_COLOR}">
        <div class="dungeon_column" data-column="1"></div>
        <div class="dungeon_column" data-column="2"></div>
        <div class="dungeon_column" data-column="3"></div>
        <div class="dungeon_column" data-column="4"></div>
      </div>
      <div id="{PLAYER_ID}_dungeon" class="dungeon_grid_container">
        <div id="{PLAYER_ID}_passage_overlay" class="overlay"></div>
      </div>
      <div id="{PLAYER_ID}_dungeon_frame_bottom" class="dungeon_frame bottom" face="cave" data-player="{PLAYER_ID}" data-color="{PLAYER_COLOR}"></div>
    </div>
    <div id="{PLAYER_ID}_blueprint_hand" class="wrapper column"></div>
  </div>

  <div class="anchor-up">
    <a href="#"><div class="up_arrow shadow_smallicon"></div></a>
  </div>

  <!-- END OTHER_PLAYERS -->
</div>

<script type="text/javascript">
  // Javascript HTML templates

  /*
// Example:
var jstpl_some_game_item='<div class="my_game_item" id="my_game_item_${MY_ITEM_ID}"></div>';

*/
</script>

{OVERALL_GAME_FOOTER}
