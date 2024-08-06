define([], /**
 * Description placeholder
 *
 * @returns {{ getPositionInSprite: (card_index: number, sprite_rows: number, sprite_columns: number) => string; normalizeBackgroundSize: (type: any) => string; getPositionInTokenSprite: (token: { ...; }, face?: string) => string; ... 4 more ...; isReadOnly: () => any; }}
 */

function () {
  return {
    /**
     *
     * @param {number} card_index
     * @param {number} sprite_rows
     * @param {number} sprite_columns
     * @returns Returns the offset of the image with index card_index in a sprite made of spire_rows rows and sprite_columns columns. Result is formatted as "xOffset% yOffset%" to be used in the CSS background-position attribute.
     */
    getPositionInSprite: function (card_index, sprite_rows, sprite_columns) {
      //card_index -= 1; //card index start at 1, formula below works with index starting at 0

      const row = Math.floor(card_index / sprite_columns);
      const col = card_index % sprite_columns;
      // Handle case for single row or column to avoid divide by zero
      let yPosition, xPosition;
      if (sprite_rows === 1) {
        yPosition = 0;
      } else {
        yPosition = (row / (sprite_rows - 1)) * 100;
      }

      if (sprite_columns === 1) {
        xPosition = 0;
      } else {
        xPosition = (col / (sprite_columns - 1)) * 100;
      }

      return `${xPosition}% ${yPosition}%`;
    },

    normalizeBackgroundSize: function (type) {
      width = card_types[type] == "portrait" ? CARD_WIDTH : CARD_HEIGHT;
      height = card_types[type] == "portrait" ? CARD_HEIGHT : CARD_WIDTH;

      calcWidth = width * card_types[type]["cols"];
      calcHeight = height * card_types[type]["rows"];

      return `${calcWidth} ${calcHeight}`;
    },

    /**
     *
     * @param {Object} token An object representing a token
     * @param {string} token.token_id the id of the token
     * @param {string} token.token_shape the shape of the token: circle, square or oval
     * @param {string} token.token_type the unique type of token (e.g. gnoll)
     * @param {string} [face = "front"]  front or back
     * @returns {string }dedicated version of getPositionInSprite, uses the token properties to select the proper image in a single row sprite
     */
    getPositionInTokenSprite: function (token, face = "front") {
      const circleWidth = 34 * 5; // Total width for circle images
      const squareWidth = 40 * 6; // Total width for square images
      const ovalWidth = 34 * 5; // Total width for oval images
      const totalWidth = circleWidth + squareWidth + ovalWidth; // Total width of the sprite
      const imageWidth = totalWidth;

      let xOffset = 0;

      if (token.token_shape === "circle") {
        containerWidth = 34;
        if (face === "front") {
          xOffset = 34 * (token.token_type - 1); // token.token_type ranges from 1 to 4
        } else if (face === "back") {
          xOffset = 34 * 4; // back image is the 5th image
        }
      } else if (token.token_shape === "square") {
        containerWidth = 40;
        xOffset = circleWidth; // Offset for first 5 circle images
        if (face === "front") {
          xOffset += 40 * (token.token_type - 1); // token.token_type ranges from 1 to 5
        } else if (face === "back") {
          xOffset += 40 * 5; // back image is the 6th image
        }
      } else if (token.token_shape === "oval") {
        containerWidth = 34;
        xOffset = circleWidth + squareWidth; // Offset for first 5 circle and 6 square images
        if (face === "front") {
          xOffset += 34 * (token.token_type - 1); // token.token_type ranges from 1 to 4
        } else if (face === "back") {
          xOffset += 34 * 4; // back image is the 5th image
        }
      }

      // Convert Offset to % to be used with background-position
      const xPercent = (xOffset / (imageWidth - containerWidth)) * 100;
      const yPercent = 0;
      return `${xPercent}% ${yPercent}%`;
    },
    /**
     *
     * @param {Object[]} elements An array of objects (or a single object) representing the width and height of the element(s) we want to randomly position
     * @param {number} elements[].width
     * @param {number} elements[].height
     * @param {Object} container An object representing the container element the elements will be positioned in
     * @param {number} container.width
     * @param {number} container.height
     * @param {boolean} [format = true] If true, format the result by appending "px" for direct use in CSS attributes. Default: true;
     * @returns {Object[]} An array of objects detailing the top and left position of each element's position inside the container, in the same order as the the elements array.
     */
    getRandomPosition: function (elements, container, gap, format = true, usable_area = 0.9) {
      if (!Array.isArray(elements)) elements = [elements];
      if (elements.length == 0) return [];

      container.height = container.height || container.width;
      container.width = container.width || container.height;

      //sanity check:
      let total_width = elements.reduce((sum, elem) => sum + elem.width, 0);
      let total_height = elements.reduce((sum, elem) => sum + elem.height, 0);

      if (total_width > usable_area * container.width + gap || total_height > usable_area * container.height + gap) {
        console.log("The elements won't fit in the container");
        return [];
      }

      let position = [];
      let max_attempts = 1000;
      let attempt = 0;
      let nbr_elements = elements.length;

      let element = elements[0];
      while (position.length < nbr_elements && attempt < max_attempts) {
        attempt++;
        const randX = ((1 - usable_area) * container.width) / 2 + Math.random() * (usable_area * container.width - element.width);
        const randY = ((1 - usable_area) * container.height) / 2 + Math.random() * (usable_area * container.height - element.height);

        let newPosition = this.getRandomPosition(elements.slice(1), container, gap, false);

        const isOverlapping = newPosition.some((pos) => {
          hDeltaCenter = Math.abs(pos.x - randX);
          vDeltaCenter = Math.abs(pos.y - randY);
          hMaxDistance = (pos.width + element.width) / 2 + gap;
          vMaxDistance = (pos.height + element.height) / 2 + gap;

          return hDeltaCenter < hMaxDistance && vDeltaCenter < vMaxDistance;
        });

        if (!isOverlapping || attempt == max_attempts) {
          let new_pos = { x: randX, y: randY, width: element.width, height: element.height };
          newPosition.push(new_pos);
          position = newPosition;
        }
      }
      if (format) {
        position = position.map((elem) => {
          delete elem.width;
          delete elem.height;
          Object.keys(elem).forEach((key) => (elem[key] += "px"));
          return elem;
        });
      }
      return position;
    },
    /**
     *
     *
     * @param {Element} element
     * @return {Boolean} true if the argument is a DOM element
     */
    isElement: function (element) {
      return element instanceof Element;
    },
    rearrangeAllChildrenInContainer: function (container, gap = 0, usableArea = 1) {
      children = Array.form(container.children);
      rearrangeElementsInContainer(children, container, gap, usableArea);
    },
    /**
     *
     *
     * @param {element[]} elements
     * @param {element} container
     * @param {number} [gap=0]
     * @param {number} [usableArea=1]
     * @param {element[]} [elementsAlreadyPresent=[]]
     * 
     * Randomly positions elements in container leaving some gap among them (and respecting also elements already presents, that won't be moved)
     */
    rearrangeElementsInContainer: function (elements, container, gap = 0, usableArea = 1, elementsAlreadyPresent = []) {
      if (!Array.isArray(elements)) elements = Array.from(elements);
      const containerRect = container.getBoundingClientRect();
      const maxAttempts = 100;
      const usableWidth = containerRect.width * usableArea;
      const usableHeight = containerRect.height * usableArea;
      const offsetX = (containerRect.width - usableWidth) / 2;
      const offsetY = (containerRect.height - usableHeight) / 2;

      function getRandomPosition(element) {
        const x = Math.random() * (usableWidth - element.offsetWidth) + offsetX;
        const y = Math.random() * (usableHeight - element.offsetHeight) + offsetY;
        return { x, y };
      }

      function elementsOverlap(el1, el2) {
        const rect1 = el1.getBoundingClientRect();
        const rect2 = el2.getBoundingClientRect();
        const overlapH = !(rect1.right + gap < rect2.left || rect1.left - gap > rect2.right);
        const overlapV = !(rect1.bottom + gap < rect2.top || rect1.top - gap > rect2.bottom);

        return overlapH && overlapV;
      }

      function noOverlap(element, placedElements) {
        return placedElements.every((el) => !elementsOverlap(el, element));
      }

      async function placeElement(element, placedElements, attempt = 0) {
        if (attempt >= maxAttempts) {
          return false;
        }
        console.log(element.id + " attempt: " + attempt);

        const position = getRandomPosition(element);
        element.style.position = "absolute";
        element.style.left = `${position.x}px`;
        element.style.top = `${position.y}px`;

        if (noOverlap(element, placedElements)) {
          placedElements.push(element);
          return true;
        } else {
          return placeElement(element, placedElements, attempt + 1);
        }
      }

      function initPositions(elements) {
        for (element of elements) {
          element.style.position = "absolute";
          element.style.top = "-" + element.offsetHeight + "px";
        }
      }

      async function placeAllElements(elements, placedElements = []) {
        for (let i = 0; i < maxAttempts; i++) {
          initPositions(elements);

          let success = true;

          for (const element of elements) {
            const placed = await placeElement(element, placedElements);
            if (!placed) {
              success = false;
              break;
            }
          }

          if (success) {
            return;
          }
        }

        console.warn("Max attempts reached, elements may overlap.");
      }

      placeAllElements(elements, elementsAlreadyPresent);
    },
  
    /**
     *
     *
     * @return {boolean} true for spectators, instant replay (during game), archive mode (after game end)

     */
    isReadOnly: function () {
      return this.isSpectator || typeof g_replayFrom != "undefined" || g_archive_mode;
    },
  };
});
