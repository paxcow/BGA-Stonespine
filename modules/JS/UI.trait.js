var isDebug = window.location.host == "studio.boardgamearena.com" || window.location.hash.indexOf("debug") > -1;
var debug = isDebug ? console.info.bind(window.console) : function () {};

define(["dojo", "dojo/_base/declare", "ebg/core/gamegui"], function (dojo, declare) {
  return declare("UITrait", null, {
    multiSelect: false,
    constructor: function(){
    },
    addSelected: function(element, forceSelected = null, selectedClass = "selected", containersToFlag = [], containerClass = "selected" ){
        let isAlreadySelected = element.classList.contains(selectedClass);

        let select = forceSelected ?? !isAlreadySelected;
      // select token by adding proper class
      if (select == true) {
        if (!this.multiSelect) this.clearAllSelected(selectedClass, containersToFlag);
        element.classList.add(selectedClass);
      } else {
        element.classList.remove(selectedClass);
      }

      containersToFlag.forEach( container => container.classList.add(containerClass));
    },
    clearAllSelected: function (selectedClass, containersToFlag, callable = null) {
        prevSelected = document.querySelectorAll(selectedClass);
          for (element of prevSelected) {
            element.classList.remove(selectedClass);
            if (callable) callable(element);
          }

          containersToFlag.forEach( container => container.classList.remove(containerClass));
        },
  

  });
});