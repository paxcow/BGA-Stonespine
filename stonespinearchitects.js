/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * Stonespine Architects implementation : © Andrea "Paxcow" Vitagliano <andrea.vitagliano@gmail.com>
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * stonespinearchitects.js
 *
 * StonespineArchitects user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

const CARD_WIDTH = 126;
const CARD_HEIGHT = 176;

const SPRITE_CHAMBER_ROWS = 11;
const SPRITE_CHAMBER_COLS = 10;

const SPRITE_CHALLENGE_ROWS = 4;
const SPRITE_CHALLENGE_COLS = 10;

const SPRITE_BLUEPRINT_ROWS = 1;
const SPRITE_BLUEPRINT_COLS = 9;

const SPRITE_MARKET_ROWS = 3;
const SPRITE_MARKET_COLS = 9;

var isDebug = window.location.host == "studio.boardgamearena.com" || window.location.hash.indexOf("debug") > -1;
var debug = isDebug ? console.info.bind(window.console) : function () {};

define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter", g_gamethemeurl + "modules/BGA-cards/bga-cards.js", g_gamethemeurl + "modules/JS/dungeon.manager.js", g_gamethemeurl + "modules/JS/cards.manager.js", g_gamethemeurl + "modules/JS/market.manager.js"], function (dojo, declare) {
  return declare("bgagame.stonespinearchitects", ebg.core.gamegui, {
    constructor: function () {
      console.log("stonespinearchitects constructor");

      // Here, you can init the global variables of your user interface
      // Example:
      // this.myGlobalValue = 0;

      ///GLOBALS FOR POSITIONING OF CHAMBER CARDS IN THE DUNGEON
      this.lastCardIdSelected = null;
      this.lastPosXSelected = null;
      this.lastPosYSelected = null;
    },

    /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */

    setup: function (gamedatas) {
      console.log("Starting game setup");
      console.dir(gamedatas);
      // Setting up player boards
      for (var player_id in gamedatas.players) {
        var player = gamedatas.players[player_id];

        // TODO: Setting up players boards if needed
      }
      //Creating the card managers
      this.cards = new CardsManager(this);
      this.cards.initHands(this.gamedatas.hand);
      this.cards.initTable(this.gamedatas.table);

      this.dungeons = new DungeonManager(this);
      this.dungeons.setup();
      this.dungeons.init();

      //create markers and position them

      this.initGoalCard(this.gamedatas.table.goal);
      this.initGoldMarkers(this.gamedatas["players"]);
      this.initPriorityMarkers(this.gamedatas["players"]);

      //Setup scoreboeard and central table area

      //Create token piles

      const shapes = { oval: 5, square: 6, circle: 5 };
      for (const shape in shapes) {
        let targetDiv = document.getElementById(`${shape}_token_pile`);
        createToken(shape, targetDiv, shapes[shape]);
      }

      function createToken(shape, element, nbr) {
        let i = 0;
        for (i; i < nbr; i++) {
          const w = element.clientWidth;
          const h = element.clientHeight;

          const token = document.createElement("div");
          token.classList.add(`token_back`, `${shape}`);
          token.style.setProperty("position", "absolute");

          token.style.visibility = "hidden"; //position element while hidden, to avoid flickering
          element.appendChild(token);

          const tokenWidth = token.offsetWidth;
          const tokenHeight = token.offsetHeight;

          const centerLeft = (w - tokenWidth) / 2;
          const centerTop = (h - tokenHeight) / 2;

          const maxOffset = Math.min(centerLeft, centerTop);

          const randOffsetTop = (Math.random() - 0.5) * maxOffset;
          const randOffsetLeft = (Math.random() - 0.5) * maxOffset;

          token.style.setProperty("top", `${centerTop + randOffsetTop}px`);
          token.style.setProperty("left", `${centerLeft + randOffsetLeft}px`);

          token.style.visibility = "visible";
        }
        return;
      }

      // TODO: Set up your game interface here, according to "gamedatas"

      // Setup game notifications to handle (see "setupNotifications" method below)
      this.setupNotifications();

      console.log("Ending game setup");
    },

    ///////////////////////////////////////////////////
    //// Game & client states

    // onEnteringState: this method is called each time we are entering into a new game state.
    //                  You can use this method to perform some user interface changes at this moment.
    //
    onEnteringState: function (stateName, args) {
      console.log("Entering state: " + stateName);

      //call appropriate method
      var stateMethod = "onEnteringState_" + stateName;
      if (this[stateMethod] != undefined) {
        console.log("Calling method: " + stateMethod);
        this[stateMethod](args.args);
      }
    },

    // onLeavingState: this method is called each time we are leaving a game state.
    //                 You can use this method to perform some user interface changes at this moment.
    //

    onLeavingState: function (stateName) {
      console.log("Leaving state: " + stateName);

      //call appropriate method
      var stateMethod = "onLeavingState_" + stateName;
      if (this[stateMethod] != undefined) {
        console.log("Calling method: " + stateMethod);
        this[stateMethod]();
      }
    },

    // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
    //                        action status bar (ie: the HTML links in the status bar).
    //
    onUpdateActionButtons: function (stateName, args) {
      console.log("onUpdateActionButtons: " + stateName);
      //call appropriate method
      var stateMethod = "onUpdateActionButtons_" + stateName;
      if (this[stateMethod] != undefined) {
        console.log("Calling method: " + stateMethod);
        this[stateMethod](args.args);
      }
    },

    onEnteringState_playCard: function (args) {
      //check if we the previous pending action triggered a client state
      if (this.gamedatas.client_state) {
        this.setClientState("client_discardCard", {
          descriptionmyturn: _("${you} must choose a card to discard"),
        });
        this.gamedatas.client_state = false;
        return;
      }

      if (this.isCurrentPlayerActive()) {
        //add clickable to cards in hand
        this.cards.chamberHand[this.player_id].setSelectionMode("single");
        this.cards.chamberHand[this.player_id].onSelectionChange = (selection, lastChange) => {
          this.lastCardIdSelected = null;
          this.lastPosXSelected = null;
          this.lastPosYSelected = null;

          document.getElementById("placeChamber_button").classList.add("disabled");

          this.dungeons.placementMode(selection[0]);
        };
        /*  }*/

        //highlights open dungeon slots
        let open_slots = args._private.slots;
        this.dungeons.highlightOpenSlots(open_slots, document.getElementById("my_dungeon"));
      }
    },

    onUpdateActionButtons_playCard: function (args) {
      if (this.isCurrentPlayerActive()) {
        //Add action button
        this.addActionButton("placeChamber_button", _("Confirm"), "onPlaceChamberClicked");
        if (this.lastCardIdSelected === null || this.lastPosXSelected === null || this.lastPosYSelected === null) {
          document.getElementById("placeChamber_button").classList.add("disabled");
        }
      } else if (!this.isSpectator) {
        this.addActionButton("unpass_button", _("Undo pass"), "onUnpassClicked");
      }
    },

    onUpdateActionButtons_client_discardCard: function (args) {
      if (this.isCurrentPlayerActive()) {
        //set selection mode on player hand
        //add clickable to cards in hand
        this.cards.chamberHand[this.player_id].setSelectionMode("single");
        this.cards.chamberHand[this.player_id].onSelectionChange = (selection, lastChange) => {
          if (selection.length > 0) {
            this.lastCardIdSelected = selection[0].id;
            document.querySelector("#discardChamber_button").classList.remove("disabled");
          } else {
            this.lastCardIdSelected = null;
            document.querySelector("#discardChamber_button").classList.add("disabled");
          }
        };

        //Add action button
        this.addActionButton("discardChamber_button", _("Confirm"), "onDiscardChamberClicked");
        if (this.lastCardIdSelected === null) {
          document.getElementById("discardChamber_button").classList.add("disabled");
        }
        debugger;
        this.addActionButton("undoPlayChamber_button", _("Undo play Chamber"), "onUndoPlaceChamberClicked");
      } else {
        this.addActionButton("unpass_button", _("Undo pass"), "onUnpassClicked");
      }
    },

    onUnpassClicked: function (evt) {
      this.cards.chamberHand[this.player_id].unselectAll(true);
      this.sendajaxcall("undo", { unpass: true });
      if (Object.keys(this.gamedatas.players).length == 2) {
        this.setClientState("client_discardCard", {
          descriptionmyturn: _("${you} must choose a card to discard"),
        });
      } else {
        this.restoreServerGameState();
      }
    },

    onPlaceChamberClicked: function (evt) {
      if (this.lastCardIdSelected === null || this.lastPosXSelected === null || this.lastPosYSelected === null) {
        this.showMessage(_("You must select a Chamber card first, and a position in the Dungeon"), "only_to_log");
        return;
      }
      this.dungeons.placementMode(false);
      this.sendajaxcall("placeChamberCard", { card: this.lastCardIdSelected, posX: this.lastPosXSelected, posY: this.lastPosYSelected });
    },

    onUndoPlaceChamberClicked: function (evt) {
      this.cards.chamberHand[this.player_id].unselectAll();
      this.sendajaxcall("undo", {});
      this.restoreServerGameState();
    },

    onDiscardChamberClicked: function (evt) {
      if (this.lastCardIdSelected === null) {
        this.showMessage(_("You must select a Chamber card first, and a position in the Dungeon"), "only_to_log");
        return;
      }
      this.sendajaxcall("discardChamberCard", { card: this.lastCardIdSelected });
    },

    ///////////////////////////////////////////////////
    //// Utility methods

    /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */

    ///////////////////////////////////////////////////
    //// Player's action

    /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */

    /* Example:
        
        onMyMethodToCall1: function( evt )
        {
            console.log( 'onMyMethodToCall1' );
            
            // Preventing default browser reaction
            dojo.stopEvent( evt );

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'myAction' ) )
            {   return; }

            this.ajaxcall( "/stonespinearchitects/stonespinearchitects/myAction.html", { 
                                                                    lock: true, 
                                                                    myArgument1: arg1, 
                                                                    myArgument2: arg2,
                                                                    ...
                                                                 }, 
                         this, function( result ) {
                            
                            // What to do after the server call if it succeeded
                            // (most of the time: nothing)
                            
                         }, function( is_error) {

                            // What to do after the server call in anyway (success or failure)
                            // (most of the time: nothing)

                         } );        
        },        
        
        */

    ///////////////////////////////////////////////////
    //// Reaction to cometD notifications

    /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your stonespinearchitects.game.php file.
        
        */
    setupNotifications: function () {
      // TODO: here, associate your game notifications with local methods
      // Example 1: standard notification handling
      // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
      // Example 2: standard notification handling + tell the user interface to wait
      //            during 3 seconds after calling the method in order to let the players
      //            see what is happening in the game.
      // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
      // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
      // dojo.subscribe('yourCardPlaced',this,"notif_cardPlaced");
      // dojo.subscribe('yourCardDiscarded',this,"notif_cardDiscarded")

      let notification_to_register = [];
      notification_to_register.push("card_placed");
      notification_to_register.push("undo_card_placed");
      notification_to_register.push("card_discarded");
      notification_to_register.push("undo_card_discarded");

      notification_to_register.forEach((notif) => {
        dojo.subscribe(notif, this, "notif_" + notif);
        this.notifqueue.setSynchronous(notif, 1);
      });
    },

    // TODO: from this point and below, you can write your game notifications handling methods

    //Notification handlers

    notif_card_placed: function (notif) {
      this.lastCardIdSelected = null;
      //get parameters from notif

      let card_id = notif.args.card_id;
      let position = notif.args.position;
      let player_id = notif.args.player_id;
      let card = { id: card_id, type: "chamber" };
      let anim = {};
      let settings = { slot: position };

      //add card to Dungeon (remove card from LineStock)
      this.dungeons.dungeon[player_id].addCard(card, anim, settings);

      //remove slot from open slots
      document.querySelector(".row" + position.charAt(0) + ".col" + position.charAt(1)).classList.remove("open_slot");

      //set selection mode none for cards in Hand
      this.cards.chamberHand[this.player_id].setSelectionMode("none");

      if (Object.keys(this.gamedatas.players).length == 2) {
        this.setClientState("client_discardCard", {
          descriptionmyturn: _("${you} must choose a card to discard"),
        });
      }
    },

    notif_undo_card_placed: function (notif) {
      this.lastCardIdSelected = null;
      let card_id = notif.args.card_id;

      let player_id = notif.args.player_id;
      let card = { id: card_id, type: "chamber" };
      let origin_stock = this.dungeons.dungeon[player_id];
      let anim = { fromStock: origin_stock };
      let settings = {};
      this.cards.chamberHand[player_id].addCard(card, anim, settings);

      if (Object.keys(this.gamedatas.players).length == 2) {
        this.restoreServerGameState();
      }
    },

    notif_card_discarded: function (notif) {
      this.lastCardIdSelected = null;
      let card_id = notif.args.card_id;
      let player_id = notif.args.player_id;
      let card = { id: card_id, type: "chamber" };
      
      //discard card
      this.cards.discardChamber.addCard(card);

      //remove clickable and selection from the player hand
      this.cards.chamberHand[this.player_id].setSelectionMode("none");

      //restore server state
      this.restoreServerGameState();
    },

    notif_undo_card_discarded: function (notif) {
      this.lastCardIdSelected = null;
      let card_id = notif.args.card_id;
      let player_id = notif.args.player_id;
      let card = { id: card_id, type: "chamber" };
      
      //return card to hand
      this.cards.discardChamber.addCard(card,undefined,{remove:false});
      this.cards.chamberHand[this.player_id].addCard(card);

      //transition back to client state
        this.setClientState("client_discardCard", {
          descriptionmyturn: _("${you} must choose a card to discard"),
        });
      
    },

    // Utilities

    sendajaxcall: function (action, args, handler) {
      if (!args) {
        args = {};
      }
      args.lock = true;
      if (action == "undo" || this.checkAction(action)) {
        this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", args, (result) => {}, handler);
      }
    },

    getScaledDimension: function (dimension) {
      this.getPositionInSprite();
      return dimension * this.getZoomFactor();
    },

    getZoomFactor: function () {
      return this.zoomFactor();
    },

    /**
     *
     * @param {Number} card_index - the index of the card to locate in the sprite, starting from 1
     * @param {Number} sprite_rows - nbr of rows in the sprite
     * @param {Number} sprite_columns - nbr of columns in the sprite
     * @returns - returns a string to be used in a background-position CSS attribute, representing the X and Y offset in %
     */

    getPositionInSprite: function (card_index, sprite_rows, sprite_columns) {
      yPosition = (Math.floor((card_index - 1) / sprite_columns) * 100) / (sprite_rows - 1);
      xPosition = (((card_index - 1) % sprite_columns) * 100) / (sprite_columns - 1);

      return `${xPosition}% ${yPosition}%`;
    },

    initGoldMarkers: function (players, style = "3D") {
      for (let player in players) {
        const colorX = {
          "3A5CAC": 2,
          959300: 5,
          "10181F": 1,
          E2231A: 4,
          F6B221: 3,
        };
        let color = colorX[players[player]["color"]];
        let id = player;
        let gold = players[player]["gold"];

        let marker = document.createElement("div");
        marker.id = `gold_${id}`;
        marker.classList.add("marker");
        marker.style.setProperty("--marker_color", color);
        marker.dataset["style"] = style;
        let targetDiv = document.getElementById(`gold_${gold}`);
        targetDiv.appendChild(marker);
      }
    },

    initPriorityMarkers: function (players, style = "3D") {
      for (let player in players) {
        const colorX = {
          "3A5CAC": 2,
          959300: 5,
          "10181F": 1,
          E2231A: 4,
          F6B221: 3,
        };
        let color = colorX[players[player]["color"]];
        let id = player;
        let priority = players[player]["priority"];

        let marker = document.createElement("div");
        marker.id = `priority_${id}`;
        marker.classList.add("marker");
        marker.style.setProperty("--marker_color", color);
        marker.dataset["style"] = style;
        let targetDiv = document.getElementById(`priority_curr_${priority}`);
        targetDiv.appendChild(marker);
      }
    },

    initGoalCard: function (cardId) {
      let card = document.createElement("div");
      card.id = `goal_${cardId}`;
      card.classList.add("goal-card");
      card.style.backgroundPosition = this.getPositionInSprite(cardId, 8, 1);
      let targetDiv = document.getElementById("goal_card_placeholder");

      targetDiv.appendChild(card);
    },

    initGridStock: function (cardmanager) {
      players = this.gamedatas.players;
      for (let player_id in players) {
        let row = 1;
        let col = 1;

        let clase;
      }
    },
  });
});
