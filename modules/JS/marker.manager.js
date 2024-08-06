define(["dojo", "dojo/_base/declare", g_gamethemeurl + "modules/JS/utils.js", "ebg/core/gamegui", "ebg/counter"], function (dojo, declare, utils) {
  return declare("Marker", null, {
    constructor: function (type) {
      this.type = type;
      this.markers = {};
    },

    setPlayers: function(players){
      this.players = players;

    },
    initMarkers: function (players,style = "3D") {
      this.setPlayers(players);
      for (let player_id in this.players) {
        player = this.players[player_id];
        const colorX = {
          101820: 1,
          "3A5DAE": 2,
          "00843D": 3,
          DA291C: 4,
          F0B323: 5,
        };
        let color = colorX[player.color];
        let position = player[this.type];
        let type = this.type;

        let marker = document.createElement("div");
        marker.id = `${type}_${player_id}`;
        marker.classList.add("marker");
        marker.style.setProperty("--marker_color", color);
        marker.dataset["style"] = style;

        this.markers[player_id] = { element: marker, position: position };

        let targetDiv_id = this.type == "gold" ? `${type}_${position}` : `${type}_curr_${position}`;

        let targetDiv = document.getElementById(targetDiv_id);
        targetDiv.appendChild(marker);
      }
    },
    getElement: function (player_id) {
      return this.markers[player_id].element;
    },
    getPositionElement: function (player_id) {
      marker_div = this.getElement(player_id);
      return marker_div.parentNode;
    },
    getPosition: function (player_id) {
      return this.markers[player_id].position;
    },
    setPosition: function (player_id, new_pos) {
      switch (this.type) {
        case "gold":
          min_pos = 0;
          max_pos = 30;
          break;
        case "priority":
          min_pos = 1;
          max_pos = 5;
          break;
        default:
          break;
      }
      new_pos = Math.max(new_pos, min_pos);
      new_pos = Math.min(new_pos, max_pos);
      this.markers[player_id].position = new_pos;
      return new_pos;
    },
    deltaGold: function (player_id, delta) {
      let position = parseInt(this.getPosition(player_id));

      let new_pos = position + parseInt(delta);
      switch (this.type) {
        case "gold":
          min_pos = 0;
          max_pos = 30;
          break;
        case "priority":
          min_pos = 1;
          max_pos = 5;
          break;
        default:
          break;
      }

      new_pos = Math.max(new_pos, min_pos);
      new_pos = Math.min(new_pos, max_pos);
      this.markers[player_id].position = new_pos;
      return new_pos;
    },
    placeTo: function (player_id, element) {
      element.appendChild(this.getElement(player_id));
    },
    moveTo: function (player_id, to_element, from_element, settings = {}) {
      return new Promise((resolve, reject) => {
        //check settings
        if (!("duration" in settings)) settings.duration = "500";

        // Get the element of the marker
        marker = this.getElement(player_id);

        // Get the initial and final positions of the from_element and to_element
        const fromRect = from_element.getBoundingClientRect();
        const toRect = to_element.getBoundingClientRect();

        // Calculate the distances to move
        const deltaX = toRect.left - fromRect.left;
        const deltaY = toRect.top - fromRect.top;

        // Set the initial position and append the tokenElement to the document body
        marker.style.position = "absolute";
        marker.style.left = `${fromRect.left}px`;
        marker.style.top = `${fromRect.top}px`;
        from_element.parentNode.appendChild(this.tokenElement);

        // Create the animation using CSS transitions
        marker.style.transition = `transform ${settings.duration}ms ease-in-out`;
        marker.getBoundingClientRect();
        marker.style.transform = `translate(${deltaX}px, ${deltaY}px)`;

        // Append the tokenElement to the new parent element without disrupting the document flow
        to_element.appendChild(this.tokenElement);
        marker.style.left = "";
        marker.style.top = "";
        // Resolve the promise after the animation is finished
        resolve();
      });
    },
    moveMarker: function (player_id, delta) {
      new_pos= this.deltaGold(player_id,delta);
      target_pos = document.querySelector(`#${this.type}_${new_pos}`);
      this.placeTo(player_id, target_pos);
    },
  });
});
