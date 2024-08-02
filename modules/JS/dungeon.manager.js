/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * Stonespine Architects implementation : © Andrea "Paxcow" Vitagliano <andrea.vitagliano@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 *
 * Dungeon.manager.js - Manager of dungeon tableau
 *
 */

var isDebug = window.location.host == "studio.boardgamearena.com" || window.location.hash.indexOf("debug") > -1;
var debug = isDebug ? console.info.bind(window.console) : function () {};

define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", g_gamethemeurl + "modules/bga-cards/bga-cards.js"], function (dojo, declare) {
  return declare("DungeonManager", null, {
    constructor: function (game) {
      this.game = game;
      this.manager = game.cardsManager.chamberManager;
    },

    setup: function () {
      this.dungeon = [];
      let players = this.game.gamedatas.players;
      let this_player = this.game.player_id;
      for (const player in players) {
        //create the SlotStock for each player to host the chamber cards in a dungeon
        let player_tag = player == this_player ? "my" : player;

        let slotIds = [];
        for (let r = 1; r <= 4; r++) {
          for (let c = 1; c <= 4; c++) {
            slotIds.push(`${r}${c}`);
          }
        }

        this.dungeon[player] = new SlotStock(this.manager, document.getElementById(`${player_tag}_dungeon`), {
          gap: 0,
          slotsIds: slotIds,
          slotClasses: ["grid_element"],
        });
        this.dungeon[player].setSelectionMode("none");

        //create passage overlay slots

        const overlay_id = "#" + player_tag + "_passage_overlay";
        const overlay = document.querySelector(overlay_id);

        const slotWidth = OVAL_TOKEN_WIDTH;
        const slotHeight = OVAL_TOKEN_WIDTH;
        const squareWidth = CARD_WIDTH;
        const squareHeight = CARD_WIDTH;
        const totalWidth = squareWidth * 4;
        const totalHeight = squareHeight * 4;

        const xPositions = {
          left: 0,
          mid: (squareWidth - slotWidth) / 2,
          right: squareWidth - slotWidth,
        };
        const yPositions = {
          top: 0,
          mid: (squareHeight - slotHeight) / 2,
          bottom: squareHeight - slotHeight,
        };

        const directions = ["top", "bottom", "left", "right"];
        for (let r = 0; r <= 3; r++) {
          for (let c = 0; c <= 3; c++) {
            for (let direction of directions) {
              let tempElement = document.createElement("div");
              tempElement.classList.add("passage-overlay-slot");
              const xPos = xPositions[direction == "top" || direction == "bottom" ? "mid" : direction];
              const yPos = yPositions[direction == "left" || direction == "right" ? "mid" : direction];
              const baseXOffset = c * squareWidth;
              const baseYOffset = r * squareHeight;
              const xOffsetPercent = ((baseXOffset + xPos) / totalWidth) * 100;
              const yOffsetPercent = ((baseYOffset + yPos) / totalHeight) * 100;

              tempElement.style.top = yOffsetPercent + "%";
              tempElement.style.left = xOffsetPercent + "%";
              tempElement.dataset.gridSlot = `${r+1}${c+1}`;
              tempElement.dataset.passage = direction;
              tempElement.dataset.passageId = `${r+1}${c+1}_${direction}`;

              overlay.appendChild(tempElement);
            }
          }
        }
      }
      //for every slot created by the SlotStock, add class for row and column
      document.querySelectorAll("[data-slot-id]").forEach((element) => {
        let row = element.getAttribute("data-slot-id").charAt(0);
        let col = element.getAttribute("data-slot-id").charAt(1);
        element.classList.add(`row${row}`, `col${col}`);
      });

      //override standard flex layout for Dungeon slotstock
      document.querySelectorAll(".slot-stock").forEach((element) => {
        element.style.display = "grid";
      });
    },

    highlightOpenSlots: function (slots, tableau) {
      //clean tableau
      tableau.querySelectorAll("div").forEach((targetDiv) => {
        if (targetDiv.dataset.hasOwnProperty("slotid")) {
          targetDiv.classList.remove("open-slot");
        }
      });

      //highlight new slots

      let active_row = slots.row;

      slots.col.forEach((column) => {
        const selector = `div[data-slot-id].row${active_row}.col${column}`;
        let openSlots = tableau.querySelectorAll(selector);
        openSlots.forEach((element) => {
          element.classList.add("open-slot");
        });
      });
    },

    init: function () {
      for (const player_id in this.game.gamedatas.players) {
        let player = this.game.gamedatas.players[player_id];
        let player_color = player["color"];
        let dungeon = this.game.gamedatas.dungeon[player_id];
        for (let r in dungeon) {
          for (let c in dungeon[r]) {
            if (r > 0) {
              let card = { id: dungeon[r][c].id, type: dungeon[r][c].type, type_arg: dungeon[r][c].type_arg };
              let slot = r + c;
              this.dungeon[player_id].addCard(card, {}, { slot: slot });
            } else {
              let door = document.createElement("div");
              door.classList.add("door");
              door.dataset.color = player_color;
              door.classList.add("3D");

              let selector = `.dungeon_frame.top[data-player = "${player_id}"] div[data-column = "${c}"]`;

              let targetDiv = document.querySelector(selector);
              if (!targetDiv) return;
              targetDiv.appendChild(door);
            }
          }
        }
      }
    },

    placementMode: function (cardSelected) {
      let slots = document.querySelectorAll(".open-slot");

      if (cardSelected) {
        //add clickable to each open slot.
        clickHandler = function (event) {
          // event.stopPropagation();
          //target slot in dungeon
          const toElement = event.currentTarget;

          //get the card element
          const cardElement = gameui.cardsManager.getSelectedChamber(".bga-cards_selected-card");
          const fromElement = cardElement?.parentNode;
          //remove existing clickable
          gameui.removeAllEvents(cardElement);
          //manually move the card (not using bga-cards)
          if (!cardElement || !toElement) return;
          toElement.appendChild(cardElement);

          //add clickable to card (since bga-cards select doesn't work outside the stock element)
          clickToUnselect = function (event) {
            event.stopPropagation();
            //return card to main stock
            gameui.dungeonsManager.returnCardToHand(event.currentTarget, true);
          };
          clickToUnselect = clickToUnselect.bind(this);
          gameui.addEvent(cardElement, "click", clickToUnselect);

          //activate button
          document.getElementById("placeChamber_button").classList.remove("disabled");

          /*           //add double click to the card
          doubleClickCardToConfirm = function (event) {
            event.stopPropagation();
            gameui.onPlaceChamberClicked(event);
          };
          gameui.addEvent(cardElement, "dblclick", doubleClickCardToConfirm); */
        };

        slots.forEach((slot) => {
          gameui.addEvent(slot, "click", clickHandler);
        });
      } else {
        slots.forEach((slot) => {
          gameui.removeAllEvents(slot);
        });
      }
    },
    returnCardToHand: function (cardElement, deselect = false) {
      //return card to hand
      const toElement = document.querySelector("#my_chamber_hand");
      toElement?.appendChild(cardElement);

      if (deselect) {
        //deselect card
        const card = gameui.cardsManager.chamberHand[gameui.player_id].getCard(cardElement.id);
        gameui.cardsManager.chamberHand[gameui.player_id].unselectCard(card);
      }
      gameui.removeAllEvents(cardElement);
    },
    returnAllCardsToHand: function () {
      cards = document.querySelector("#my_dungeon_wrapper").querySelectorAll(".chamber-card.actionable");
      for (card of cards) {
        this.returnCardToHand(card);
      }
    },
  });
});
