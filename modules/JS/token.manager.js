define(["dojo", "dojo/_base/declare", g_gamethemeurl + "modules/JS/utils.js", "ebg/core/gamegui", "ebg/counter"], function (dojo, declare, utils) {
  return declare("TokenManager", null, {
    constructor: function () {
      this.tokens = [];
    },

    addTokens: function (tokens) {
      if (!Array.isArray(tokens)) tokens = [tokens];
      tokens.forEach((element) => {
        this.tokens.push(element);
      });
    },

    initTokens: function (type, cardsManager = null, toElement = null, spreadOut = false) {
      let tokenCreated = [];
      this.tokens.forEach((token) => {
        if (token.token_location_type != type) return;

        let targetCardId = token.token_location;
        let tokenElement = document.createElement("div");
        tokenElement.id = "token_" + token.token_id;
        tokenElement.classList.add("token");
        tokenElement.dataset.shape = token.token_shape;
        tokenElement.dataset.type = token.token_face == "secret" ? "passage" : "element";
        tokenElement.dataset.tokenId = token.token_id;
        tokenElement.style.backgroundPosition = utils.getPositionInTokenSprite(token);
        if (type == "player") tokenElement.classList.add("token-staged");
        tokenCreated.push(tokenElement);
        if (cardsManager) toElement = cardsManager.getSlotDiv(targetCardId, token.token_location_slot);

        if (toElement) this.placeTo(tokenElement, toElement);
      });

      if (spreadOut && tokenCreated.length > 0) this.distributeTokensInParent(tokenCreated);
    },
    placeTo: function (tokenElement, toElement) {
      tokenElement.style.position = "relative";
      tokenElement.style.top = "";
      tokenElement.style.left = "";
      toElement.appendChild(tokenElement);
    },
    placeToById: function (tokenId, toElement) {
      let tokenElement = this.getDiv(tokenId);
      if (!tokenElement) {
        debug(`The token ${tokenId} does not exist in the document`);
      }
      this.placeTo(tokenElement, toElement);
    },

    createDiv: function () {
      let div = document.createElement("div");
      div.classList.add("token");
      div.dataset.tokenId = this.token_id;
      div.dataset.shape = this.token_shape;
      div.style.backgroundPosition = utils.getPositionInTokenSprite(this);
      return div;
    },

    getDiv: function (token_id) {
      return document.querySelector(`[data-token-id = "${token_id}"]`);
    },

    distributeTokensInElement: function (tokens, container) {
      allTokens = Array.from(container.children);
      tokensArray = Array.from(tokens);
      fixedTokens = allTokens.filter((child) => !tokensArray.includes(child));

      gap = 3;
      usableArea = 0.9;

      utils.rearrangeElementsInContainer(tokens, container, gap, usableArea, fixedTokens);
    },

    distributeAllTokensInContainer: function (container) {
      allTokens = container.children;
      this.distributeTokensInElement(allTokens, container);
    },

    distributeTokensInParent: function (tokens) {
      container = tokens[0].parentElement;
      this.distributeTokensInElement(tokens, container);
    },

    getStagedTokens: function () {
      const tokensStaged = document.querySelectorAll(".token-staged");
      return tokensStaged;
    },

    getSelectTokenOptions: function (element = null) {
      options = {};

      options.selectedClass = "token-selected";
      options.containersToFlag = [];
      options.containersToFlag.push(element.parentElement);
      options.containersToFlag.push(document.querySelector("#my_dungeon_wrapper"));
      options.containerData = element.dataset.type;
      options.callable = function (element) {
        if (!element.classList.contains("token-selected")) {
          toElement = document.querySelector("#my_token_staging");
          this.placeTo(element, toElement);
          this.distributeTokensInParent([element]);
        }
      };
      options.callable = options.callable.bind(this);
      return options;
    },

    placePassageOnOverlay: function (passageElement, slotElement, player_id) {
      debugger;
      //get slot clicked (top, bottom, left, right)
      const direction = slotElement.dataset.passage;

      //get card slot in the grid
      const card = slotElement.closest(".grid_element");
      const slotId = card.dataset.slotId;

      let overlaySlot = `${slotId}_${direction}`;
 
      player_tag = player_id == gameui.player_id ? "my":player_id;
      const overlay = document.querySelector(`#${player_tag}_passage_overlay`);
      const targetElement = overlay.querySelector(`[data-passage-id = "${overlaySlot}"]`);

      this.placeTo(passageElement, targetElement);

      return overlaySlot;


    },
  });
});
