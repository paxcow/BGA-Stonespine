define([], function () {
  debug("module utils.js loaded");
  return {
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

    getPositionInSprite: function (card_index, sprite_rows, sprite_columns) {
        //card_index -= 1; //card index start at 1, formula below works with index starting at 0
  
        const row = Math.floor(card_index / sprite_columns);
        const col = card_index % sprite_columns;
        yPosition = (row / (sprite_rows - 1)) * 100;
        xPosition = (col / (sprite_columns - 1)) * 100;
  
        return `${xPosition}% ${yPosition}%`;
      },

      normalizeBackgroundSize: function (type) {
        width = card_types[type] == "portrait" ? CARD_WIDTH : CARD_HEIGHT;
        height = card_types[type] == "portrait" ? CARD_HEIGHT : CARD_WIDTH;
  
        calcWidth = width * card_types[type]["cols"];
        calcHeight = height * card_types[type]["rows"];
  
        return `${calcWidth} ${calcHeight}`;
      },
  };
});
