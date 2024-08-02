var isDebug = window.location.host == "studio.boardgamearena.com" || window.location.hash.indexOf("debug") > -1;
var debug = isDebug ? console.info.bind(window.console) : function () {};

const eventHandlers = new Map();

define(["dojo/_base/declare", "dojo/_base/lang"], function (declare, lang) {
  var ClickableTrait = declare(null, {
    addEvent: function (element, eventType, callable) {
      if (!eventHandlers.has(element)) {
        eventHandlers.set(element, new Map());
      }

      const eventMap = eventHandlers.get(element);

      if (!eventMap.has(eventType)) {
        eventMap.set(eventType, new Set());
      }

      const handler = (event) => callable(event);
      element.addEventListener(eventType, handler);
      eventMap.get(eventType).add(handler);
    },

    removeEvent: function (element, eventType, callable) {
      if (eventHandlers.has(element)) {
        const eventMap = eventHandlers.get(element);

        if (eventMap.has(eventType)) {
          const handlers = eventMap.get(eventType);

          for (let handler of handlers) {
            if (handler.toString() === callable.toString()) {
              element.removeEventListener(eventType, handler);
              handlers.delete(handler);
              break;
            }
          }

          if (handlers.size === 0) {
            eventMap.delete(eventType);
          }

          if (eventMap.size === 0) {
            eventHandlers.delete(element);
          }
        }
      }
    },

    removeAllEvents: function (element) {
      if (eventHandlers.has(element)) {
        const eventMap = eventHandlers.get(element);

        eventMap.forEach((handlers, eventType) => {
          handlers.forEach((handler) => {
            element.removeEventListener(eventType, handler);
          });
        });

        eventHandlers.delete(element);
      }
    },

    removeAllElementsEvents: function () {
      eventHandlers.forEach((eventMap, element) => {
        eventMap.forEach((handlers, eventType) => {
          handlers.forEach((handler) => {
            element.removeEventListener(eventType, handler);
          });
        });
      });

      eventHandlers.clear();
    },
  });

  var UITrait = declare(null, {
    multiSelect: false,
    constructor: function () {},
    select: function (element, options = {}) {
      options = Object.assign(
        {
          forceSelected: null,
          selectedClass: "selected",
          containersToFlag: [],
          containerData: "selected",
          callable : null,
        },
        options
      );

      let isAlreadySelected = element.classList.contains(options.selectedClass);

      //cleanup selection and highlights
      this.clearAllSelected(options.selectedClass, options);

      let select = options.forceSelected ?? !isAlreadySelected;
      // select token by adding proper class
      if (select == true) {
        element.classList.add(options.selectedClass);
        options.containersToFlag.forEach((container) => container.dataset.selected = options.containerData);
      } else {
        
        element.classList.remove(options.selectedClass);
      }
      if (options.callable) options.callable(element);
      return select ? element : false;
    },
    selectContainerOnly: function (element, options = {}){
      options = Object.assign(
        {
          forceSelected: null,
          selectedClass: "selected",
          containersToFlag: [],
          containerData: "selected",
        },
        options
      );
      let isAlreadySelected = element.classList.contains(options.selectedClass);
      let select = options.forceSelected ?? !isAlreadySelected;
      if (select == true) {
        options.containersToFlag.forEach((container) => container.dataset.selected = options.containerData);
      } else {
        options.containersToFlag.forEach((container) => container.dataset.selected = "");
      }
    },
    clearAllSelected: function (selectedClass, options = {}) {
      options = Object.assign(
        {
          forceSelected: null,
          selectedClass: "selected",
          containersToFlag: [],
          containerData: "selected",
          callable:null
        },
        options
      );
      prevSelected = document.querySelectorAll("."+selectedClass);
      for (element of prevSelected) {
        element.classList.remove(selectedClass);
        if (options.callable) options.callable(element);
      }
      options.containersToFlag.forEach(container => {
         container.removeAttribute("data-selected");
      });
    },
  });

  return {
    UITrait: UITrait,
    ClickableTrait: ClickableTrait,
  };
});
