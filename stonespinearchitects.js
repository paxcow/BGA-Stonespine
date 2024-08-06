/*
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

const DOOR_WIDTH = 40;
const DOOR_HEIGHT = 34;

const SPRITE_CHAMBER_ROWS = 11;
const SPRITE_CHAMBER_COLS = 10;

const SPRITE_CHALLENGE_ROWS = 4;
const SPRITE_CHALLENGE_COLS = 10;

const SPRITE_BLUEPRINT_ROWS = 1;
const SPRITE_BLUEPRINT_COLS = 9;

const SPRITE_MARKET_ROWS = 3;
const SPRITE_MARKET_COLS = 9;

const OVAL_TOKEN_WIDTH = 34;
const OVAL_TOKEN_HEIGHT = 50;

const CIRCLE_TOKEN_WIDTH = 34;
const CIRCLE_TOKEN_HEIGHT = 34;

const SQUARE_TOKEN_WIDTH = 40;
const SQUARE_TOKEN_HEIGHT = 40;

var isDebug = window.location.host == "studio.boardgamearena.com" || window.location.hash.indexOf("debug") > -1;
var debug = isDebug ? console.info.bind(window.console) : function () {};
define([
  //prettier-ignore
  "dojo",
  "dojo/_base/declare",
  g_gamethemeurl + "modules/JS/utils.js",
  g_gamethemeurl + "modules/JS/UIAndClickable.trait.js",
  "ebg/core/gamegui",
  g_gamethemeurl + "modules/JS/dungeon.manager.js",
  g_gamethemeurl + "modules/JS/cards.manager.js",
  g_gamethemeurl + "modules/JS/market.manager.js",
  g_gamethemeurl + "modules/JS/token.manager.js",
  g_gamethemeurl + "modules/JS/marker.manager.js",
  g_gamethemeurl + "modules/bga-zoom/bga-zoom.js",
  g_gamethemeurl + "modules/bga-animations/bga-animations.js",
], function (
  //prettier-ignore
  dojo,
  declare,
  utils,
  UIAndClickable,
  bgaZoom,
  bgaAnimations
) {
  return declare(
    "bgagame.stonespinearchitects",
    [
      //prettier-ignore
      ebg.core.gamegui,
      UIAndClickable.ClickableTrait,
      UIAndClickable.UITrait,
    ],
    {
      constructor: function () {
        console.log("stonespinearchitects constructor");
        // create the zoom manager
        this.zoomManager = new ZoomManager({
          element: document.getElementById("main_wrapper"),
          localStorageZoomKey: "stonespine-zoom",
          zoomControls: {
            color: "white",
          },
        });

        this.animationsManager = new AnimationManager(this);
        this.cardsManager = new CardsManager(this);
        this.dungeonsManager = new DungeonManager(this);
        this.goldMarkers = new Marker("gold");
        this.priorityMarkers = new Marker("priority");
        this.tokenManager = new TokenManager(this);
      },

      setup: function (gamedatas) {
        console.log("Starting game setup");
        console.dir(gamedatas);

        // Setting up player boards
        for (var player_id in gamedatas.players) {
          var player = gamedatas.players[player_id];
        }

        //Initialize cards
        this.cardsManager.initHands(this.gamedatas.hand);
        this.cardsManager.initTable(this.gamedatas.table);

        //Initialize dungeons
        this.dungeonsManager.setup();
        this.dungeonsManager.init();

        //Initialize markets  /// TODO: merge in normal Card manager
        this.market = new MarketManager();
        market_card_ids = this.gamedatas.table.market.map((obj) => obj.id);
        this.market.addCards(market_card_ids);

        //Initialize markers
        this.goldMarkers.initMarkers(this.gamedatas["players"]);
        this.priorityMarkers.initMarkers(this.gamedatas["players"]);

        //Setup scoreboard and central table area

        //initialize goal card
        this.initGoalCard(this.gamedatas.table.goal);

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
        //place tokens on market cards

        this.tokenManager.addTokens(this.gamedatas.table.token.market);
        this.tokenManager.addTokens(this.gamedatas.table.token.player);
        for (player in this.gamedatas.table.token.dungeon) {
          this.tokenManager.addTokens(this.gamedatas.table.token.dungeon[player]);
        }
        this.tokenManager.initTokens("market", this.market);
        this.tokenManager.initTokens("player", null, document.querySelector("#my_token_staging"), true);

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
        debug("onEnteringState " + stateName, args);
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
        args = args ?? { args: null };

        console.log("onUpdateActionButtons: " + stateName);
        //call appropriate method
        var stateMethod = "onUpdateActionButtons_" + stateName;
        if (this[stateMethod] != undefined) {
          console.log("Calling method: " + stateMethod);
          this[stateMethod](args.args);
        }
      },

      onEnteringState_playCard: function (args) {
        debug("args: ", args);
        //check if we are in a client state becuase of an action in the action stack
        if (this.gamedatas.client_state) {
          this.setClientState("client_discardCard", {
            descriptionmyturn: _("${you} must choose a card to discard"),
          });
          this.gamedatas.client_state = false;
          return;
        }

        if (!this.isSpectator) {
          //add clickable to cards in hand
          this.cardsManager.chamberHand[this.player_id].setSelectionMode("single");
          this.cardsManager.chamberHand[this.player_id].onSelectionChange = (selection, lastChange) => {
            this.dungeonsManager.returnAllCardsToHand();
            const card = selection.lenght ? selection[0] : lastChange;
            const cardElement = this.cardsManager.chamberManager.getCardElement(card);
            this.cardsManager.selectChamber(cardElement);
            this.dungeonsManager.placementMode(cardElement);
            document.getElementById("placeChamber_button").classList.add("disabled");
          };

          //highlights open dungeon slots
          let openSlots = args._private.slots;
          this.dungeonsManager.highlightOpenSlots(openSlots, document.getElementById("my_dungeon"));
        }
      },

      onEnteringState_playerTokenOrPass: function (args) {
        debug("args: ", args);

        sections = args.affordable; //get the sections to be highlighted
        if (!Array.isArray(sections)) return;
        sections.forEach((card) => {
          id = card.id;
          highlight_top = card.top;
          highlight_bottom = card.bottom;
          if (this.market.isPresent(card.id)) {
            this.market.highlightSection(id, "top", highlight_top);
            this.market.highlightSection(id, "bottom", highlight_bottom);
          }
        });
        //setTimeout(() => document.querySelector("#market_wrapper").scrollIntoView({ behavior: "smooth", block: "center", inline: "center" }), 250);
      },
      onEnteringState_playerPlaceToken: function (args) {
        debug("args: ", args);

        if (this.player_id != args.player_id) return;

        setTimeout(() => document.querySelector("#my_token_staging").scrollIntoView({ behavior: "smooth", block: "center", inline: "center" }), 250);

        //list available slots
        const slotsToHighlight = args.slots;
        for (cardId in slotsToHighlight) {
          openSlots = slotsToHighlight[cardId];
          this.cardsManager.makeSlotActionable(cardId, openSlots, true);
        }
        debugger;
        //add clickable to tokens in the active player staging area
        let tokens = document.querySelector("#my_token_staging").children;

        let clickHandler = function (event) {
          event.stopPropagation();
          this.tokenManager.selectToken(event.currentTarget);
          this.cardsManager.activateSlots();
        };
        clickHandler = clickHandler.bind(this);
        if (tokens.length > 0) {
          for (const token of tokens) {
            this.addEvent(token, "click", clickHandler);
          }
        }

        //activate buttons
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
          this.cardsManager.chamberHand[this.player_id].setSelectionMode("single");
          this.cardsManager.chamberHand[this.player_id].onSelectionChange = (selection, lastChange) => {
            let button = document.querySelector("#discardChamber_button");

            if (selection[0]) {
              cardSelected = selection[0];
              this.lastCardIdSelected = cardSelected.id;
              cardElement = this.cardsManager.chamberHand[this.player_id].getCardElement(cardSelected);
              if (!cardElement) return;
              cardElement.parentNode.classList.add("card-selected");
              if (button) button.classList.remove("disabled");
            } else {
              cardDeselected = lastChange;
              this.lastCardIdSelected = null;
              cardElement = this.cardsManager.chamberHand[this.player_id].getCardElement(cardDeselected);
              if (!cardElement) return;
              cardElement.parentNode.classList.remove("card-selected");
              if (button) button.classList.add("disabled");
            }
          };

          //Add action button
          this.addActionButton("discardChamber_button", _("Confirm"), "onDiscardChamberClicked");
          if (this.lastCardIdSelected === null) {
            document.getElementById("discardChamber_button").classList.add("disabled");
          }
          this.addActionButton("undoPlayChamber_button", _("Undo play Chamber"), "onUndoPlaceChamberClicked");
        } else {
          this.addActionButton("unpass_button", _("Undo pass"), "onUnpassClicked");
        }
      },

      onUpdateActionButtons_playerPlaceToken: function (args) {
        if (!this.isSpectator) {
          this.addActionButton("placeToken_button", _("Place Token"), "onPlaceTokenClicked");

          this.addActionButton("undoBuyTokens_button", _("I've changed my mind"), "onUndoBuyTokensClicked", null, false, "red");
        }
      },

      onUndoBuyTokensClicked: function (evt) {
        this.bgaPerformAction("undo", { state: "goBack" }, { checkAction: false });
      },
      onPlaceTokenClicked: function () {},
      onUnpassClicked: function (evt) {
        this.cardsManager.chamberHand[this.player_id].unselectAll(true);

        nbr_players = Object.keys(this.gamedatas.players).length;
        cards_remaining = this.cardsManager.chamberHand[this.player_id].getCards().length;

        let steps_back = 1;

        if (nbr_players == 2 && cards_remaining == 0) steps_back++; //in a 2 players game, if there are no cards remaining, Unpass retraces two steps: discard last card, unplay the second to last card.

        this.bgaPerformAction("undo", { unpass: true, steps: steps_back }, { checkAction: false });

        if (nbr_players == 2) {
          if (steps_back == 1) {
            this.setClientState("client_discardCard", {
              descriptionmyturn: _("${you} must choose a card to discard"),
            });
          } else {
            this.restoreServerGameState();
          }
        }
      },

      onPlaceChamberClicked: function (evt) {
        const cardSelected = this.cardsManager.getSelectedChamber();

        if (!cardSelected) {
          this.showMessage(_("You must select a Chamber card first, and a position in the Dungeon"), "only_to_log");
          return;
        }
        const slot = cardSelected.parentElement.dataset.slotId;
        const [type, id] = cardSelected.id.split("-");
        const cardObj = {
          card: id,
          row: slot.charAt(0),
          col: slot.charAt(1),
        };
        this.bgaPerformAction("placeChamberCard", cardObj);
      },

      onUndoPlaceChamberClicked: function (evt) {
        this.cardsManager.chamberHand[this.player_id].unselectAll();
        this.bgaPerformAction("undo", {}, { checkAction: false });
        this.restoreServerGameState();
      },

      onDiscardChamberClicked: function (evt) {
        if (this.lastCardIdSelected === null) {
          this.showMessage(_("You must select a Chamber card to discard"), "only_to_log");
          return;
        }
        this.bgaPerformAction("discardChamberCard", { card: this.lastCardIdSelected });
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

        notification_to_register.push({ name: "card_placed" });
        notification_to_register.push({ name: "undo_card_placed" });
        notification_to_register.push({ name: "card_discarded" });
        notification_to_register.push({ name: "undo_card_discarded" });
        notification_to_register.push({ name: "reveal_cards_placed", condition: (notif) => notif.args.player_id == this.player_id });
        notification_to_register.push({ name: "reveal_cards_discarded", condition: (notif) => notif.args.player_id == this.player_id });
        notification_to_register.push({ name: "cards_received", delay: 500 });
        notification_to_register.push({ name: "card_picked" });
        notification_to_register.push({ name: "animate_gold_received" });
        notification_to_register.push({ name: "gold_received" });
        notification_to_register.push({ name: "tokens_purchased", condition: (notif) => notif.args.player_id == this.player_id, delay: 1000 });
        notification_to_register.push({ name: "tokens_returned" });

        notification_to_register.forEach((notif) => {
          let methodName = "notif_" + notif.name;
          let methodNamePrivate = "notif_" + notif.name + "_private";

          dojo.subscribe(notif.name, this, methodName);

          if (notif.delay === undefined) notif.delay = 100;
          this.notifqueue.setSynchronous(notif.name, notif.delay);

          if (notif.condition !== undefined) {
            debugger;
            if (!(methodNamePrivate in this) && methodName in this) {
              this[methodNamePrivate] = this[methodName];
            }
            dojo.subscribe(notif.name + "_private", this, methodNamePrivate);
            this.notifqueue.setSynchronous(notif.name + "_private", notif.delay);
            this.notifqueue.setIgnoreNotificationCheck(notif.name, notif.condition);
          }
        });
      },

      // TODO: from this point and below, you can write your game notifications handling methods

      //Notification handlers

      notif_card_placed: function (notif) {
        debug(`notification: ${notif.type}`, notif);

        //get parameters from notif

        let position = notif.args.position;
        let player_id = notif.args.player_id;
        let card = notif.args.card;
        let cardElement = this.cardsManager.chamberManager.getCardElement(card);
        let anim = { fromStock: origin_stock };
        let settings = {};
        this.dungeonsManager.dungeon[player_id].addCard(card, anim, settings);


        //remove any clickable from card placed
        this.removeAllEvents(cardElement);

        //remove clickable from every open slot
        this.dungeonsManager.placementMode(false);

        //remove slot from open slots
        document.querySelector(".row" + position.charAt(0) + ".col" + position.charAt(1)).classList.remove("open-slot");

        //set selection mode none for cards in Hand
        this.cardsManager.chamberHand[this.player_id].setSelectionMode("none");

        //change to client state in 2 players for next phase (discard card)
        if (Object.keys(this.gamedatas.players).length == 2) {
          this.setClientState("client_discardCard", {
            descriptionmyturn: _("${you} must choose a card to discard"),
          });
        }
      },

      notif_undo_card_placed: function (notif) {
        debug(`notification: ${notif.type}`, notif);
        this.lastCardIdSelected = null;

        let player_id = notif.args.player_id;
        let card = notif.args.card;
        let origin_stock = this.dungeonsManager.dungeon[player_id];
        let anim = { fromStock: origin_stock };
        let settings = {};
        this.cardsManager.chamberHand[player_id].addCard(card, anim, settings);

        if (Object.keys(this.gamedatas.players).length == 2) {
          this.restoreServerGameState();
        }
      },

      notif_card_discarded: function (notif) {
        debug(`notification: ${notif.type}`, notif);
        this.lastCardIdSelected = null;

        let card = notif.args.card;
        let player_id = notif.args.player_id;

        //remove class from Hand container
        let handDiv = this.cardsManager.chamberHand[player_id].getCardElement(card);
        handDiv.parentElement.classList.remove("card-selected");

        //discard card
        this.cardsManager.discardChamber.addCard(card);

        //remove clickable and selection from the player hand
        this.cardsManager.chamberHand[this.player_id].setSelectionMode("none");

        //restore server state
        //      this.restoreServerGameState();
      },

      notif_undo_card_discarded: function (notif) {
        debug(`notification: ${notif.type}`, notif);
        this.lastCardIdSelected = null;
        let card = notif.args.card;

        //return card to hand
        this.cardsManager.discardChamber.addCard(card, undefined, { remove: false });
        this.cardsManager.chamberHand[this.player_id].addCard(card);

        //transition back to client state
        this.setClientState("client_discardCard", {
          descriptionmyturn: _("${you} must choose a card to discard"),
        });
      },

      notif_reveal_cards_placed: function (notif) {
        debugger;
        debug(`notification: ${notif.type}`, notif);
        let card = notif.args.card;
        let position = notif.args.position;
        let player_id = notif.args.player_id;
        let anim = {};
        let settings = { slot: position };

        //add card to Dungeon (remove card from LineStock)
        this.dungeonsManager.dungeon[player_id].addCard(card, anim, settings);
      },
      notif_reveal_cards_discarded: function (notif) {
        debug(`notification: ${notif.type}`, notif);
        let card = notif.args.card;
        let player_id = notif.args.player_id;
        this.cardsManager.discardChamber.addCard(card);
      },

      notif_cards_received: function (notif) {
        debug(`notification: ${notif.type}`, notif);

        let this_player_id = notif.args.player_id;
        let to_player_id = notif.args.destination;
        let from_player_id = notif.args.source;
        let cards_received = notif.args.cards;
        let cards_passed = this.cardsManager.chamberHand[this_player_id].getCards();

        //send hand to the To player
        // anonymize them first
        this.cardsManager.chamberHand[this_player_id].cards.forEach((card) => {
          card.type_arg = null;
        });
        this.cardsManager.chamberHand[to_player_id].addCards(cards_passed, { fromStock: this.cardsManager.chamberHand[this_player_id] }, { visible: false });

        //get hand from the From player
        this.cardsManager.chamberHand[this_player_id].addCards(cards_received, { fromStock: this.cardsManager.chamberHand[from_player_id] }, { visible: true });
      },

      notif_card_picked: function (notif) {
        debug(`notification: ${notif.type}`, notif);
        let player_id = notif.args.player_id;
        let card = notif.args.card;
        this.cardsManager.chamberHand[player_id].addCard(card, { fromStock: this.cardsManager.deckChamber });
      },

      notif_animate_gold_received: function (notif) {
        debug(`notification: ${notif.type}`, notif);
      },

      notif_gold_received: function (notif) {
        debug(`notification: ${notif.type}`, notif);
        player_id = notif.args.player_id;
        gold = notif.args.gold;
        this.goldMarkers.moveMarker(player_id, gold);
      },

      notif_tokens_purchased: async function (notif) {
        debug(`notification: ${notif.type}`, notif);
        tokens_to_move = notif.args.tokens;
        gold = notif.args.gold;
        player_id = notif.args.player_id;

        //find staging area:
        player_qualifier = player_id == this.player_id ? "my" : player_id;
        tokenStagingDiv = document.querySelector(`#${player_qualifier}_token_staging`);

        //move tokens

        for (const token_id in tokens_to_move) {
          const tokenElement = this.tokenManager.getDiv(token_id);
          await this.tokenManager.placeToById(token_id, tokenStagingDiv, true);
          console.log(`token ${token_id} moved`);
        }
        //rearrange
        tokensMoved = tokenStagingDiv.children;
        for (tokenMoved of tokensMoved) {
          tokenMoved.classList.add("token-staged");
        }
        this.tokenManager.distributeAllTokensInContainer(tokenStagingDiv);
        document.querySelector("#my_token_staging").scrollIntoView({ behavior: "smooth", block: "center", inline: "center" });

        //take money away

        this.goldMarkers.moveMarker(player_id, -gold);

        //disable clickable and purchasable from other tokens
        this.market.cards.forEach((card) => {
          this.market.highlightSection(card, "top", false);
          this.market.highlightSection(card, "bottom", false);
        });
      },
      notif_tokens_returned: function (notif) {
        debug(`notification: ${notif.type}`, notif);
        const tokens_to_move = notif.args.tokens;
        const gold = notif.args.gold;
        const player_id = notif.args.player_id;

        //return tokens:
        for (let tokenIndex in tokens_to_move) {
          const token = tokens_to_move[tokenIndex];
          const tokenElement = this.tokenManager.getDiv(token.token_id);
          const toElement = this.market.getSlotDiv(token.token_location, token.token_location_slot);
          this.removeAllEvents(tokenElement);
          this.tokenManager.placeTo(tokenElement, toElement);
        }

        //return gold:

        this.goldMarkers.moveMarker(player_id, gold);
      },

      // Utilities

      initGoalCard: function (cardId) {
        let card = document.createElement("div");
        card.id = `goal_${cardId}`;
        card.classList.add("goal-card");
        card.style.backgroundPosition = utils.getPositionInSprite(cardId, 8, 1);
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
    }
  );
});
