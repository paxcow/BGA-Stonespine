/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * Stonespine Architects implementation : © Andrea "Paxcow" Vitagliano <andrea.vitagliano@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 *
 * cards.manager.js -all cards manager for Stonespine Architects
 *
 */

define([
  //prettier-ignore
  "dojo",
  "dojo/_base/declare",
  g_gamethemeurl + "modules/bga-cards/bga-cards.js",
  g_gamethemeurl + "modules/JS/utils.js",
  g_gamethemeurl + "modules/JS/UIAndClickable.trait.js",
], function (
  //prettier-ignore
  dojo,
  declare,
  bgaCards,
  utils,
  UIAndClickable
) {
  return declare("CardsManager", [UIAndClickable.ClickableTrait, UIAndClickable.UITrait], {
    constructor: function (game) {
      this.card_types = {
        chamber: { rows: SPRITE_CHAMBER_ROWS, cols: SPRITE_CHAMBER_COLS, orientation: "portrait" },
        blueprint: { rows: SPRITE_BLUEPRINT_ROWS, cols: SPRITE_BLUEPRINT_COLS, orientation: "portrait" },
        challenge: { rows: SPRITE_CHALLENGE_ROWS, cols: SPRITE_CHALLENGE_COLS, orientation: "landscape" },
        market: { rows: SPRITE_MARKET_ROWS, cols: SPRITE_MARKET_COLS, orientation: "portrait" },
      };

      this.game = game;

      for (let card_type in this.card_types) {
        let card_settings = this.card_types[card_type];
        let manager = card_type + "Manager";
        this[manager] = new CardManager(this, {
          getId: (card) => {
            if (card.type) {
              return `${card.type}-${card.id}`;
            } else {
              return card.id;
            }
          },
          setupDiv: (card, div) => {
            this.setupDivByCardType(card, div);
          },
          setupFrontDiv: (card, div) => {
            this.setupDivByCardType(card, div, "front");
          },
          setupBackDiv: (card, div) => {
            this.setupDivByCardType(card, div, "back");
          },
          isCardVisible: (card) => {
            return Boolean(card.type_arg) && !card.fake;
          },
          reverseId: (cardId) => {
            // Check if the id contains a hyphen
            if (cardId.includes("-")) {
              // Split the id into type and id parts
              const [type, id] = cardId.split("-");
              return { type: type, id: id };
            } else {
              // If there's no hyphen, return an object with only the id
              return { id: cardId };
            }
          },
          selectableCardClass: "actionable",
          cardWidth: card_settings.orientation == "portrait" ? CARD_WIDTH : CARD_HEIGHT,
          cardHeight: card_settings.orientation == "portrait" ? CARD_HEIGHT : CARD_WIDTH,
        });
      }
    },
    addChamberSubDivs: function (card, div) {
      let direction = ["top", "bottom", "left", "right"];

      for (let i = 1; i < 5; i++) {
        let quadrantDiv = document.createElement("div");
        quadrantDiv.classList.add("element-slot", "slot");
        quadrantDiv.id = "chamber-" + card.id + "_" + "element" + i;
        quadrantDiv.dataset.card = card.id;
        quadrantDiv.dataset.quadrant = i;
        div.appendChild(quadrantDiv);

        let passageDiv = document.createElement("div");
        passageDiv.id = "chamber-" + card.id + "_passage-" + direction[i - 1];
        passageDiv.dataset.card = card.id;
        passageDiv.dataset.passage = direction[i - 1];
        passageDiv.dataset.direction = i < 2 ? "vertical" : "horizontal";
        passageDiv.classList.add("passage-slot", "slot");
        div.appendChild(passageDiv);
      }
      let income = document.createElement("div");
      income.classList.add("income");
      income.id = "chamber-" + card.id + "_income";

      let solo_rune = document.createElement("div");
      solo_rune.classList.add("solo");
      solo_rune.id = "chamber-" + card.id + "_solo";

      let chamber_type = document.createElement("div");
      chamber_type.classList.add("chamber_type");
      chamber_type.id = "chamber-" + card.id + "_chamber_type";

      div.appendChild(income);
      div.appendChild(solo_rune);
      div.appendChild(chamber_type);
    },
    addMarketSubDivs: function (card, div) {
      if (card.fake) return;
      let token_top_nbr = 0;
      let token_bottom_nbr = 0;

      //count positions in top half card
      if (card.top[3] != null) {
        token_top_nbr = 3;
      } else if (card.top[2] != null) {
        token_top_nbr = 2;
      } else {
        token_top_nbr = 1;
      }

      //count positions in bottom half card
      if (card.bottom[2] != null) {
        token_bottom_nbr = 2;
      } else {
        token_bottom_nbr = 1;
      }

      let tempDivTop_cost = document.createElement("div");
      tempDivTop_cost.classList.add("market_top_cost");
      let tempDivBottom_cost = document.createElement("div");
      tempDivBottom_cost.classList.add("market_bottom_cost");

      let tempDivTop = document.createElement("div");
      tempDivTop.classList.add("market_top");
      tempDivTop.dataset.section = "top";
      tempDivTop.dataset.tokens = token_top_nbr;
      let tempDivBottom = document.createElement("div");
      tempDivBottom.classList.add("market_bottom");
      tempDivBottom.dataset.section = "bottom";
      tempDivBottom.dataset.tokens = token_bottom_nbr;

      let tempDivTop_tokens = [];
      let tempDivBottom_tokens = [];

      for (let i = 1; i <= token_top_nbr; i++) {
        tempDivTop_tokens[i] = document.createElement("div");
        if (card.top["${i - 1}"] == "square" && card.top["${i}"] != "square") tempDivTop.dataset.hassquare = "yes";
        tempDivTop_tokens[i].id = card.id + "_top_" + i;
        tempDivTop_tokens[i].classList.add("token", "token_slot", `token_${i}`);
        tempDivTop_tokens[i].dataset.shape = card.top[`${i}`];
        tempDivTop.appendChild(tempDivTop_tokens[i]);
      }

      for (let i = 1; i <= token_bottom_nbr; i++) {
        tempDivBottom_tokens[i] = document.createElement("div");
        tempDivBottom_tokens[i].id = card.id + "_bottom_" + i;
        tempDivBottom_tokens[i].classList.add("token", "token_slot", `token_${i}`);
        tempDivBottom_tokens[i].dataset.shape = card.bottom[`${i}`];
        tempDivBottom.appendChild(tempDivBottom_tokens[i]);
      }

      div.appendChild(tempDivTop);
      div.appendChild(tempDivBottom);
      div.appendChild(tempDivTop_cost);
      div.appendChild(tempDivBottom_cost);
    },

    setupDivByCardType: function (card, div, face = "card") {
      div.classList.add(card.type ? `${card.type}-${face}` : face);
      if (face != "card") {
        div.style.backgroundPosition = face == "front" ? (card.type ? utils.getPositionInSprite(card.type_arg, this.card_types[card.type].rows, this.card_types[card.type].cols) : "0% 100%") : "0% 100%";
      }
      //for chamber cards, build the inner structure (token position)
      if (card.type == "chamber" && face == "card" && !div.querySelector(".income")) {
        this.addChamberSubDivs(card, div);
      }

      //for market cards, build the inner structure (token position)
      if (card.type == "market" && face == "front" && div.children.length == 0) {
        this.addMarketSubDivs(card, div);
      }
    },

    initHands: function (hands) {
      let this_player = this.game.player_id;
      for (let player in hands) {
        for (let card_type in hands[player]) {
          // create hand stocks
          let manager = card_type + "Manager";
          let stockName = `${card_type}Hand`;

          let player_tag = player == this_player ? "my" : player;

          let targetDiv = document.getElementById(`${player_tag}_${card_type}_hand`);

          if (!this[stockName]) this[stockName] = {};
          if (card_type == "chamber" && player != this_player) {
            this[stockName][player] = new HandStock(this[manager], targetDiv, { cardOverlap: "100px", cardShift: "10px", cardInclination: "5deg", wrap: "nowrap" });
          } else {
            this[stockName][player] = new LineStock(this[manager], targetDiv, {});
          }

          //populate hands
          let tempCardsArray = hands[player][card_type];

          if (tempCardsArray.length > 0) {
            tempCardsArray.forEach((card) => {
              let card_data = {};
              card_data.id = card.id;
              card_data.type = card.type;
              card_data.type_arg = card.type_arg ?? null;
              card_data.visible = player == this_player || card.type != "chamber" ? true : false;
              this[stockName][player].addCard(card_data);
            });
          }
        }
      }
    },

    initTable: function (table) {
      for (let card_type in table) {
        if (card_type != "goal" && card_type != "token") {
          // create stocks
          let manager = card_type + "Manager";
          let stockName = `${card_type}River`;
          let targetDiv = document.getElementById(`${card_type}_cards_wrapper`);
          if (!this[stockName]) this[stockName] = {};
          this[stockName] = new LineStock(this[manager], targetDiv, {});

          //populate stocks
          let tempCardsArray = table[card_type];
          if (card_type != "market") {
            if (tempCardsArray.length > 0) {
              tempCardsArray.forEach((card) => {
                this[stockName].addCard(card);
              });
            }
          } else {
            if (tempCardsArray.length > 0) {
              tempCardsArray.forEach((card) => {
                this[stockName].addCard(card);
              });
            }
          }
        }
        //create decks (and void stock for discard)
        this.deckChamber = new Deck(this.chamberManager, document.getElementById("chamber_wrapper"), {
          cardNumber: 49,
          fakeCardGenerator: (deckId) => {
            return { id: deckId + "-fake-top-card", type: "chamber", fake: true };
          },
        });
        this.discardChamber = new VoidStock(this.chamberManager, document.getElementById("chamber_wrapper"));

        this.deckMarket = new Deck(this.marketManager, document.getElementById("market_deck"), {
          cardNumber: 18,
          fakeCardGenerator: (deckId) => {
            return { id: deckId + "-fake-top-card", type: "market", fake: true };
          },
        });
        this.discardMarket = new VoidStock(this.marketManager, document.getElementById("market_deck"));

        this.deckChallenge = new Deck(this.challengeManager, document.getElementById("challenge_deck"), {
          cardNumber: 30,
          fakeCardGenerator: (deckId) => {
            return { id: deckId + "-fake-top-card", type: "challenge", fake: true };
          },
        });
        this.discardChallenge = new VoidStock(this.challengeManager, document.getElementById("challenge_deck"));
      }
    },
    ///////////////////////////////////////////
    //// Chamber Cards specific functions: ////
    ///////////////////////////////////////////

    getSelectedChamber: function (selectedClass = ".bga-cards_selected-card") {
      return document.querySelector(selectedClass);
    },

    selectChamberCard: function (element) {
      options = {
        selectedClass: "card-selected",
        containersToFlag: [document.querySelector("#my_hand_wrapper")],
        containerData: "card-selected",
      };

      options.callable = function (element) {
        if (!element.classList.contains("card-selected")) {
          const card = gameui.cardsManager.chamberHand[gameui.player_id].getCard(element.id);
          gameui.cardsManager.chamberHand[gameui.player_id].addCard(card, {}, { visible: true });
        }
      };

      return gameui.select(element, options);
    },

    makeSlotActionable: function (cardId, lists, highlight = null) {
      let openQuadrants = lists.quadrant;
      let openPassages = lists.passage;

      let manager = this.chamberManager;
      let card = { id: cardId, type: "chamber" };
      let cardElement = manager.getCardElement(card);
      let centralSlots = cardElement.querySelectorAll(".element-slot");
      centralSlots = Array.from(centralSlots);
      let quadrantsHighlighted = centralSlots.filter((elem) => {
        const quadrantNbr = parseInt(elem.dataset.quadrant);
        return openQuadrants.includes(quadrantNbr);
      });

      if (quadrantsHighlighted.length > 0) {
        quadrantsHighlighted.forEach((slot) => {
          if (highlight == true) {
            slot.classList.add("actionable");
          } else if (highlight == false) {
            slot.classList.remove("actionable");
          } else {
            slot.classList.toggle("actionable");
          }
        });
      }

      let passages = cardElement.querySelectorAll(".passage-slot");
      passages = Array.from(passages);
      let passagesHighlighted = passages.filter((elem) => {
        const passageDirection = elem.dataset.passage;
        return openPassages.includes(passageDirection);
      });

      if (passagesHighlighted.length > 0) {
        passagesHighlighted.forEach((slot) => {
          if (highlight == true) {
            slot.classList.add("actionable");
          } else if (highlight == false) {
            slot.classList.remove("actionable");
          } else {
            slot.classList.toggle("actionable");
          }
        });
      }
    },

    activateSlots: function () {
      dungeon = document.querySelector("#my_dungeon_wrapper");
      let slotsNodeList = dungeon.querySelectorAll(`.actionable.${dungeon.dataset.selected}-slot`);

      //remove all clickable from every slot.
      prevActiveSlots = document.querySelectorAll(".chamber-card .slot.actionable");
      for (prevActiveSlot of prevActiveSlots) {
        this.removeAllEvents(prevActiveSlot);
      }

      // add clickable to the slots
      if (!slotsNodeList) return;

      let clickHandler = function (event) {
        event.stopPropagation();
        let slotElement = event.currentTarget;
        let tokenElement = document.querySelector(".token-staged.token-selected");
        if (tokenElement) {
          if (dungeon.dataset.selected == "passage") {
            gameui.tokenManager.placePassageOnOverlay(tokenElement, slotElement,gameui.player_id);
          } else {
            gameui.tokenManager.placeTo(tokenElement, slotElement);
          }
          tokenElement.classList.add("placed");
        }
      };

      for (slot of slotsNodeList) {
        gameui.addEvent(slot, "click", clickHandler);
      }
    },
  });
});
