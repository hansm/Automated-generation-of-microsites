/**
 * Widget resizing class. Keeps the widgets appropriate for screen size.
 *
 * @author Hans
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
		, "dojo/window", "dojo/on"
		, "dojo/query", "dojo/dom-geometry"
		, "dojo/NodeList-traverse"]
	, function(declare, dom, domConstruct, domStyle, win, on, query, domGeom, nodeListTraverse) {
	return declare(null, {

		/**
		 * Widget data
		 */
		data: [],

		/**
		 * Widget placeholder elements
		 */
		placeholders: [],

		/**
		 * Constructor method
		 *
		 * @param string divMashupId ID of element where hub should be attached
		 */
		constructor: function(widgetData, placeholders) {
			this.data = widgetData;
			this.placeholders = placeholders;
		},

		/**
		 * Run widget resize. Process all placeholders one by one
		 */
		run: function() {
			for (var i = 0; i < this.placeholders.length; i++) {
				this.processPlaceholder(this.placeholders[i]);
			}
		},

        /**
         * Get all widgets in placeholder
         */
		getWidgetsInPlaceholder: function(placeholder, onlyEnabled) {
			var widgets = [];
			for (var i = 0; i < this.data.length; i++) {
				if ((!onlyEnabled || this.data[i].enabled) && this.data[i].placeholder == placeholder) {
					widgets.push(this.data[i]);
				}
			}

			return widgets;
		},

		/**
		 * Run a callback on each element of an array/object
		 */
		forEach: function(array, callback) {
			for (var i in array) {
				callback(array[i]);
			}
		},

		/**
		 * Process all placeholders
		 */
		processPlaceholder: function(placeholder) {
			//return ;
			var placeholderId = placeholder.getAttribute("itemid");
			var widgets = this.getWidgetsInPlaceholder(placeholderId, true);

			if (widgets.length == 0) {
				return;
			}

			// Find placeholder actual dimensions
			this.forEach(widgets, function(w) {
				domStyle.set(w.div, {
					display: "none"
				});
			});
			var dimensions = domGeom.getContentBox(widgets[0].div.parentNode);
			var placeholderDimensions = {
				width: dimensions.w,
				height: dimensions.h
			};
			this.forEach(widgets, function(w) {
				domStyle.set(w.div, {
					display: "block"
				});
			});

			// Distribute widgets between lines
			var perLine = 1;
			var widgetDimensions = [];
			if (widgets.length == 2 || widgets.length == 4) {
				perLine = 2;
			} else {
				perLine = 3;
			}
			var j = 0, minWidth = 0, maxWidth = 0;
			var line = [];
			var lines = [];
			var pushed;
			for (var i = 0; i < widgets.length; i++) {
				j++;
				pushed = false;

				minWidth += widgets[i].minWidth;
				maxWidth += widgets[i].maxWidth;

				if (minWidth <= placeholderDimensions.width || line.length == 0) {
					line.push(widgets[i]);
					pushed = true;
				}

				if (j >= perLine && maxWidth >= placeholderDimensions.width
						|| minWidth >= placeholderDimensions.width) {
					lines.push(line);
					j = 0;
					minWidth = 0;
					maxWidth = 0;
					line = [];
					if (!pushed) {
						i--;
					}
				}
			}
			if (line.length > 0) {
				lines.push(line);
			}

			// Distribute width
			var widgetWidth, availableWidth, multipleWidgets, lineNotDone, widget, widgetsRemaining;
			for (var i = 0; i < lines.length; i++) {
				availableWidth = placeholderDimensions.width;
				multipleWidgets = lines[i].length > 1;

				do {
					widgetsRemaining = this.countNotNull(lines[i]);
					if (widgetsRemaining == 0) {
						break;
					}
					widgetWidth = parseInt(availableWidth / widgetsRemaining); // Distribute equally
					lineNotDone = false;

					for (var j = 0; j < lines[i].length; j++) {
						widget = lines[i][j];
						if (!widget) continue; // Already correct size

						// TODO: use min-height and max-heigth value
						widget.height = placeholderDimensions.height / lines.length;

						// Widget has special requirements
						if (widgetWidth < widget.minWidth || widgetWidth > widget.maxWidth) {
							widget.width = widgetWidth < widget.minWidth ? widget.minWidth : widget.maxWidth;
							availableWidth -= widget.width;
							this.setWidgetDimensions(widget, multipleWidgets);
							lines[i][j] = null;
							lineNotDone = true;
							break;
						}

						widget.width = widgetWidth;
						this.setWidgetDimensions(widget, multipleWidgets);
					}
				} while (lineNotDone);
			}
		},

		/**
		 * Set widget dimensions to desired value
		 */
		setWidgetDimensions: function(widget, multipleWidgets) {
			if (multipleWidgets) {
				widget.div.style.cssFloat = "left";
			}
			widget.openAjax.OpenAjax.adjustDimensions({
				width: widget.width,
				height: widget.height
			});
		},

		/**
		 * Count not NULL values in an array
		 */
		countNotNull: function(a) {
			var c = 0;
			for (var i = 0; i < a.length; i++) {
				if (a[i]) {
					c++;
				}
			}
			return c;
		}

	});
});