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

define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", g_gamethemeurl + "modules/BGA-cards/bga-cards.js"], function (dojo, declare) {
  return declare("DungeonManager", null, {
    constructor: function (game) {
      this.game = game;
      this.manager = game.cards.chamberManager;
    },

    setup: function () {
      this.dungeon = [];
      let players = this.game.gamedatas.players;
      let this_player = this.game.player_id;
      for (const player in players) {
        //create the SlotStock for each player to host the chamber cards in a dungeon

        let slotIds = [];
        for (let r = 1; r <= 4; r++) {
          for (let c = 1; c <= 4; c++) {
            slotIds.push(`${r}${c}`);
          }
        }

        let player_tag = player == this_player ? "my" : player;

        this.dungeon[player] = new SlotStock(this.manager, document.getElementById(`${player_tag}_dungeon`), {
          gap: 0,
          slotsIds: slotIds,
          slotClasses: ["grid_element"],
        });
        this.dungeon[player].setSelectionMode("none");
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
          targetDiv.classList.remove("open_slot");
        }
      });

      //highlight new slots

      let active_row = slots.row;

      slots.col.forEach((column) => {
        const selector = `div[data-slot-id].row${active_row}.col${column}`;
        let open_slots = tableau.querySelectorAll(selector);
        open_slots.forEach((element) => {
          element.classList.add("open_slot");
        });
      });
    },

    init: function () {
      for (const player_id in this.game.gamedatas.players) {
        let dungeon = this.game.gamedatas.dungeon[player_id];
        for (let r in dungeon) {
          for (let c in dungeon[r]) {
            if (r > 0) {
              let card = { id: dungeon[r][c].id, type: "chamber" };
              let slot = r+c;
              this.dungeon[player_id].addCard(card,{},{slot: slot});
            }
          }
        }
      }
    },

    placementMode: function (card = false) {
      let slots = document.querySelectorAll(".open_slot");

      //clean the grid elements if they had childs from a previous selection
      slots.forEach((targetDiv) => {        
        Array.from(targetDiv.childNodes).forEach((child) => {
          if (child.classList && child.classList.contains("placement_mode")){
            targetDiv.removeChild(child);
          } 
        });
      });
    

      if (card) {
        //a card has been selected
        let source = this.manager.getCardElement(card);

        slots.forEach((targetDiv) => {
          let temp = source.cloneNode(true);
          temp.id = `temp_${source.id}_${targetDiv.id}`;
          temp.classList.add("placement_mode");
          temp.onclick = (event) => {
            event.stopPropagation();

            //reset targets
            this.game.lastCardIdSelected = null;
            this.game.lastPosXSelected = null;
            this.game.lastPosYSelected = null;

            //clean previously selected card in dungeon
            prev_selected = document.querySelectorAll(".placement_selected");
            if (prev_selected) {
              prev_selected.forEach((element) => {
                element.classList.remove("placement_selected");
              });

              //if this card was already selected, stop after deselecting it
              nodesArray = Array.from(prev_selected);
              isSameClicked = nodesArray.includes(temp);
              if (isSameClicked) {
                document.getElementById("placeChamber_button").classList.add("disabled");

                return;
              }
            }
            //highlight new card selected in dungeon
            event.currentTarget.classList.add("placement_selected");
            this.game.lastCardIdSelected = source.id.match(/chamber-(\d+)/)[1];
            colMatch = targetDiv.className.match(/col(\d+)/);
            rowMatch = targetDiv.className.match(/row(\d+)/);
            this.game.lastPosXSelected = colMatch[1] ? parseInt(colMatch[1]) : null;
            this.game.lastPosYSelected = rowMatch[1] ? parseInt(rowMatch[1]) : null;
            document.getElementById("placeChamber_button").classList.remove("disabled");
          };
          targetDiv.appendChild(temp);
        });
      }
    },
  });
});
