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

define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter", g_gamethemeurl + "modules/BGA-cards/bga-cards.js"], function (dojo, declare, bgaCards) {
  return declare("CardsManager", null, {
    constructor: function (game) {
      this.card_types = {
        chamber: { rows: SPRITE_CHAMBER_ROWS, cols: SPRITE_CHAMBER_COLS, orientation: "portrait" },
        blueprint: { rows: SPRITE_BLUEPRINT_ROWS, cols: SPRITE_BLUEPRINT_COLS, orientation: "portrait" },
        challenge: { rows: SPRITE_CHAMBER_ROWS, cols: SPRITE_CHALLENGE_COLS, orientation: "landscape" },
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
            return Boolean(card.type) && !card.fake;
          },
          cardWidth: card_settings.orientation == "portrait" ? CARD_WIDTH : CARD_HEIGHT,
          cardHeight: card_settings.orientation == "portrait" ? CARD_HEIGHT : CARD_WIDTH,
        });
      }
    },

    addMarketSubDivs: function (card, div) {
      let token_top_nbr = 0;
      let token_bottom_nbr = 0;

      //count positions in top half card
      if (card.token_top_3 != null) {
        token_top_nbr = 3;
      } else if (card.token_top_2 != null) {
        token_top_nbr = 2;
      } else {
        token_top_nbr = 1;
      }

      //count positions in bottom half card
      if (card.token_bottom_2 != null) {
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
      tempDivTop.dataset.tokens = token_top_nbr;
      let tempDivBottom = document.createElement("div");
      tempDivBottom.classList.add("market_bottom");
      tempDivBottom.dataset.tokens = token_bottom_nbr;

      let tempDivTop_tokens = [];
      let tempDivBottom_tokens = [];

      for (let i = 1; i <= token_top_nbr; i++) {
        tempDivTop_tokens[i] = document.createElement("div");
        if (card[`token_top_${i - 1}`] == "square" && card[`token_top_${i}`] != "square") tempDivTop.dataset.hassquare = "yes";
        tempDivTop_tokens[i].id = card.card_id + "_top_" + i;
        tempDivTop_tokens[i].classList.add("token", `token_${i}`);
        tempDivTop_tokens[i].dataset.shape = card[`token_top_${i}`];
        tempDivTop.appendChild(tempDivTop_tokens[i]);
      }

      for (let i = 1; i <= token_bottom_nbr; i++) {
        tempDivBottom_tokens[i] = document.createElement("div");
        tempDivBottom_tokens[i].id = card.card_id + "_bottom_" + i;
        tempDivBottom_tokens[i].classList.add("token", `token_${i}`);
        tempDivBottom_tokens[i].dataset.shape = card[`token_bottom_${i}`];
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
        div.style.backgroundPosition = face == "front" ? (card.type ? this.getPositionInSprite(card.id, this.card_types[card.type].rows, this.card_types[card.type].cols) : "0% 100%") : "0% 100%";
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
            this[stockName][player] = new HandStock(this[manager], targetDiv, { cardOverlap: "50px", cardShift: "10px", cardInclination: "5deg" });
          } else {
            this[stockName][player] = new LineStock(this[manager], targetDiv, {});
          }
          //populate hands
          let tempCardsArray = hands[player][card_type];

          if (card_type != "chamber" || player == this_player) {
            if (tempCardsArray.length > 0) {
              tempCardsArray.forEach((card) => {
                this[stockName][player].addCard({ id: card, type: card_type });
              });
            }
          } else {
            let nbr_cards = tempCardsArray;
            for (let i = 0; i < nbr_cards; i++) {
              this[stockName][player].addCard({ id: `${player}_fake_${i}`, type: "chamber" }, null, { visible: false, updateInformations: false });
            }
          }
        }
      }
    },

    initTable: function (table) {
      for (let card_type in table) {
        if (card_type != "goal") {
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
                this[stockName].addCard({ id: card, type: card_type });
              });
            }
          } else {
            for (let card_index in tempCardsArray) {
              let card = tempCardsArray[card_index];
              card.id = card.card_id;
              card.type = card.card_type;
              this[stockName].addCard(card);
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
      }
    },

    getPositionInSprite: function (card_index, sprite_rows, sprite_columns) {
      yPosition = (Math.floor(card_index / sprite_columns) * 100) / (sprite_rows - 1);
      xPosition = ((card_index % sprite_columns) * 100) / (sprite_columns - 1);

      return `${xPosition}% ${yPosition}%`;
    },
    normalizeBackgroundSize: function (type) {
      width = card_types[type] == "portrait" ? CARD_WIDTH : CARD_HEIGHT;
      height = card_types[type] == "portrait" ? CARD_HEIGHT : CARD_WIDTH;

      calcWidth = width * card_types[type]["cols"];
      calcHeight = height * card_types[type]["rows"];

      return `${calcWidth} ${calcHeight}`;
    },
  });
});
