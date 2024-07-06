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

define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", g_gamethemeurl + "modules/BGA-cards/bga-cards.js", g_gamethemeurl + "stonespinearchitects.js", g_gamethemeurl + "modules/JS/utils.js"], function (dojo, declare, utils) {
  return declare("MarketManager", null, {
    constructor: function () {
      this.cards = [];
    },
    addCards: function (cards) {
      if (!Array.isArray(cards)) cards = [cards]; //if argument is a single card_id, transform it into an array with one element only to keep the rest of the function consistent
      cards.forEach((card) => {
        if (this.cards.indexOf(card) < 0) {
          this.cards.push(card);
        }
      });
    },
    removeCards: function (cards) {
      if (!Array.isArray(cards)) cards = [cards]; //if argument is a single card_id, transform it into an array with one element only to keep the rest of the function consistent
      cards.forEach((card) => {
        if (this.cards.indexOf(card) < 0) {
          debug("Cannot remove card " + card + ". Card does not exist in Market");
        } else {
          let index = this.cards.indexOf(card);
          cards.splice(index, 1);
        }
      });
    },
    getDiv: function (card_id) {
      let div = document.querySelector("#" + card_id) ?? null;
      return div;
    },
    getSlotDiv: function (card_id, location) {
      let str_location = location.toString();

      let digits = str_location.length;
      let main_div = this.getDiv(card_id);

      let quadrant = null;
      let position = null;

      if (digits == 1) {
        quadrant = parseInt(location);
      } else if (digits == 2) {
        quadrant = parseInt(str_location.charAt(0));
        position = Math.max(parseInt(str_location.charAt(1)), 1); //if 1 token, location is 10 or 20 (0 = centered single token) but the div is still token_1
      } else {
        debug("Invalid token location. " + location);
        return;
      }

      let str_quadrant_id = "market_" + (quadrant == 1 ? "top" : quadrant == 2 ? "bottom" : null);
      let str_position_class = "token_" + position;

      let div_quadrant = main_div.querySelector("#" + str_quadrant_id);
      let div_slot = div_quadrant.querySelector("." + str_position_class);

      return div_slot;
    },

    highlightHalf: function (card_id, half, turn_on = null) {
      card_element = this.getDiv(card_id);
      half_element = card_element.querySelector("market_"+half);

      is_highlighted = half_element.classList.contains("purchasable");

      if (turn_on === null) turn_on = !is_highlighted; // if state is not defined, toggle between highlighted and not highlighted

      if (turn_on) {
        half_element.classList.add("purchasable");
      } else {
        half_element.classList.remove("puchasable");
      }

      if (turn_on) {
        // if highlighted, add clickable
        this.onclick = function (evt) {
          let selected = {};
          selected.card_id = card_id;

          let div_selected = evt.target;
          selected.half = div_selected.dataset.half;

          this.sendAjaxCall("purchaseTokens", selected);
        };
      } else {
        this.onclick = null;
      }
    },
  });
});
