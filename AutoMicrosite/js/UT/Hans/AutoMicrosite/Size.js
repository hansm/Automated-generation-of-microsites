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
			console.log(placeholderDimensions);

			this.forEach(widgets, function(w) {
				domStyle.set(w.div, {
					display: "block"
				});
			});

			for (var i = 0; i < widgets.length; i++) {
				this.setWidgetDimensions(widgets[i], placeholderDimensions);
			}
		},

		setWidgetDimensions: function(widget, dimensions) {
			var placeholderWidgets = this.getWidgetsInPlaceholder(widget.placeholder, true);

			var widgetDimensions = {
				width: dimensions.width,
				height: dimensions.height
			};

			if (placeholderWidgets.length > 1) {
				widget.div.style.cssFloat = "left";
				widgetDimensions.width = widgetDimensions.width / placeholderWidgets.length;
			}

			// TODO: consider actual and allowed dimensions when fitting

			// TODO: shouldn't it be 'requestSizeChange'?
			widget.openAjax.OpenAjax.adjustDimensions(widgetDimensions);
		}

	});
});