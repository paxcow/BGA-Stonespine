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

define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", g_gamethemeurl + "modules/bga-cards/bga-cards.js", g_gamethemeurl + "stonespinearchitects.js", g_gamethemeurl + "modules/JS/utils.js"], function (dojo, declare, gamegui, utils) {
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
    isPresent: function (cardOrId){
      if (typeof cardOrId == 'object'){
        return this.cards.includes(card);
      } else if (typeof parseInt(cardOrId) == 'number'){
        return this.cards.some(id => id == cardOrId);
      }
      return false;
    },
    getDiv: function (card_id) {
      let div = document.querySelector(`#market-${card_id}-front`) ?? null;

      return div;
    },
    getSlotDiv: function (card_id, location) {
      let str_location = location.toString();

      let digits = str_location.length;
      let main_div = this.getDiv(card_id);

      let section = null;
      let position = null;

      if (digits == 1) {
        section = parseInt(location);
      } else if (digits == 2) {
        section = parseInt(str_location.charAt(0));
        position = Math.max(parseInt(str_location.charAt(1)), 1); //if 1 token, location is 10 or 20 (0 = centered single token) but the div is still token_1
      } else {
        debug("Invalid token location. " + location);
        return;
      }

      let str_section_id = "market_" + (section == 1 ? "top" : section == 2 ? "bottom" : null);
      let str_position_class = "token_" + position;

      let div_section = main_div.querySelector("." + str_section_id);
      let div_slot = div_section.querySelector("." + str_position_class);

      return div_slot;
    },

    highlightSection: function (card_id, section, turn_on = null, purchasableClass = "purchasable") {
      card_element = this.getDiv(card_id);
      if (!card_element) {
        console.log("Invalid card to highlight");
        return;
      }
      section_element = card_element.querySelector(".market_"+section);
      if (!section_element) {
        console.log("Invalid section to highlight");
        return;
      }
      is_highlighted = section_element.classList.contains(purchasableClass);

      if (turn_on === null) turn_on = !is_highlighted; // if state is not defined, toggle between highlighted and not highlighted

      if (turn_on) {
        section_element.classList.add(purchasableClass);
      } else {
        section_element.classList.remove(purchasableClass);
      }

      eventHandler = this.purchaseToken.bind(this,card_id,section_element);
    

      if (turn_on) {
        // if highlighted, add clickable
        section_element.addEventListener('click', eventHandler);

        // Disable pointer-event for parent div or it will block the click event
        section_element.style.pointerEvents = "auto";

      } else {
        section_element.removeEventListener("click", eventHandler);

        // Re-enable pointer event
        section_element.style.pointerEvents = "none";
      }
    },

    purchaseToken: function(id, section_element, evt){
      evt.stopPropagation();
      
      let selected = {};
      selected.id = id;

      selected.section = section_element.dataset.section;

      gameui.bgaPerformAction("buyTokens", selected);

    }
  });
});
