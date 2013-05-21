/**
 * Widget loading process
 *
 * @author Hans
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
		, "dojo/window", "dojo/on", "dojo/query"
		, "dojo/NodeList-traverse", "dojo/dom-attr"]
	, function(declare, dom, domConstruct, domStyle, win, on, query, nodeListTraverse
		, domAttr) {
	return declare(null, {

        /**
         * Prefix for widget
         */
		WIDGET_ELEMENT_ID_PREFIX: "widgetElement",

		/**
		 * Widget data
		 */
		data: [],

		/**
		 * Widget placeholder elements
		 */
		placeholders: [],

		/**
		 * Callback functions to run when all visual widgets have finished loading
		 */
		visualDoneCallback: null,

		/**
		 * Callback function to run when all data widgets have finished loading
		 */
		allDoneCallback: null,

		/**
		 * OpenAjax loader
		 */
		loader: null,

		visualWidgets: [],
		visualWidgetsLoaded: [],

		dataWidgets: [],
		dataWidgetsLoaded: [],

		constructor: function(openAjaxLoader, widgetData, placeholders) {
			this.loader = openAjaxLoader;
			this.data = widgetData;
			this.placeholders = placeholders;

			// Reorder widgets in priority and workflow order
			this.data.sort(function(a, b) {
				var order = b.priority - a.priority;
				if (order == 0) {
					if (a.workflowOrder && !b.workflowOrder) {
						return -1;
					} else if (!a.workflowOrder && b.workflowOrder) {
						return 1;
					} else if (!a.workflowOrder && !b.workflowOrder) {
						return 0;
					} else {
						return a.workflowOrder - b.workflowOrder;
					}
				}
				return order;
			});
		},

		/**
		 * Start loading widgets
		 */
		load: function(visualDone, allDone) {
			this.visualDoneCallback = visualDone;
			this.allDoneCallback = allDone;

			this.emptyPlaceholders();

			// Distribute widgets to data and visual
			for (var i in this.data) {
				if (this.data[i].isDataWidget) {
					this.dataWidgets.push(this.data[i]);
				} else {
					this.visualWidgets.push(this.data[i]);
				}
			}

            this.loadFirstOrderWidgets();
		},

		/**
		 * Load widgets that have to be loaded first
		 */
		loadFirstOrderWidgets: function() {
			var firstOrderWidgets = [];
			for (var i in this.data) {
				if (this.data[i].loadFirst) {
					firstOrderWidgets.push(this.data[i]);
				}
			}

			if (firstOrderWidgets.length == 0) {
				this.firstOrderDone();
				return;
			}

			var firstOrderLoaded = [];

			// Callback for loaded widgets
			var callback = function(widget, widgetObject) {
				firstOrderLoaded.push(widget.id);
				if (widget.isDataWidget) {
					this.dataWidgetsLoaded.push(widget.id);
				} else {
					this.visualWidgetsLoaded.push(widget.id);
				}

				if (firstOrderLoaded.length >= firstOrderWidgets.length) {
					this.firstOrderDone();
				}
			};

			// Load
			for (i in firstOrderWidgets) {
				this.loadWidget(firstOrderWidgets[i], callback.bind(this, firstOrderWidgets[i]));
			}
		},

        /**
         * Load visual widgets
         */
        loadVisualWidgets: function() {
            if (this.visualWidgets.length == 0) {
				this.visualDone();
				return;
			}

            for (i in this.visualWidgets) {
				this.loadVisualWidget(this.visualWidgets[i]);
			}
        },

		/**
		 * Load data widgets, after visual widgets are done
		 */
		loadDataWidgets: function() {
			if (this.dataWidgets.length == 0) {
				this.done();
				return;
			}
			for (i in this.dataWidgets) {
				this.loadDataWidget(this.dataWidgets[i]);
			}
		},

		/**
		 * Load visual widget
		 */
		loadVisualWidget: function(widget) {
			var callback = (function(widgetId, widgetObject) {
				this.visualWidgetsLoaded.push(widgetId);

				// All visual widgets done
				if (this.visualWidgetsLoaded.length == this.visualWidgets.length) {
					this.visualDone();
				}
			}).bind(this, widget.id);
			this.loadWidget(widget, callback);
		},

		/**
		 * Load data (non-visual) widget
		 */
		loadDataWidget: function(widget) {
			var callback = (function(widgetId, widgetObject) {
				this.dataWidgetsLoaded.push(widgetId);

				// All data widgets done
				if (this.dataWidgetsLoaded.length == this.dataWidgets.length) {
					this.done();
				}
			}).bind(this, widget.id);
			this.loadWidget(widget, callback);
		},

		/**
		 * Load OpenAjax Metadata 1.0 widget
		 */
		loadWidget: function(widget, callback) {
			widget.enabled = true;

			// Create element for widget
			widget.div = domConstruct.create("div", {
				id: this.WIDGET_ELEMENT_ID_PREFIX + "_" + widget.id
			}, this.getPlaceholderElement(widget.placeholder));

			// Hide data widgets
			if (widget.isDataWidget) {
				domStyle.set(widget.div, "display", "none");
			} else {
				domStyle.set(widget.div, "overflow", "hidden");
			}

			var thisLoader = this.loader;

			// Load widget
			this.loader.create({
				spec:		widget.metadataFile,
				target:		widget.div,
				properties: (widget.properties ? widget.properties : {}),
				onComplete: function(widgetObject) {
					widget.loaded = true;
					widget.openAjax = widgetObject;

					widgetObject.widgetId2 = widget.id;
					widgetObject.autoMicrositeData = widget;
					callback(widgetObject);

					// Publish mappings
					if (widget.mappings) {
						thisLoader.hub.publish("ee.stacc.transformer.mapping.add.raw",
							widget.mappings);
					}
				},
				onError: function(error) {
					console.log("Failed loading widget: " + error);
				}
			});
		},

		/**
		 * First order widgets loaded
		 */
		firstOrderDone: function() {
			this.loadVisualWidgets();
		},

		/**
		 * All widgets finished loading
		 */
		visualDone: function() {
			console.log("WidgetLoad.visualDone");

			if (typeof this.visualDone == "function") {
				this.visualDoneCallback(this.visualWidgets
					, this.dataWidgets);
			}

			this.loadDataWidgets();
		},

		/**
		 * All widgets finished loading
		 */
		done: function() {
			console.log("WidgetLoad.done");
			if (typeof this.allDoneCallback == "function") {
				this.allDoneCallback();
			}
		},

		/**
		 * Get placeholder DOM element
		 */
		getPlaceholderElement: function(placeholder) {
			if (placeholder) {
				for (var i in this.placeholders) {
					if (domAttr.get(this.placeholders[i], "itemid") == placeholder) {
						return this.placeholders[i];
					}
				}
			}

			return document.body; // no placeholder, append to the end of body
		},

		/**
		 * Clear placeholders of microdata/default content
		 */
		emptyPlaceholders: function() {
			for (var i in this.placeholders) {
				this.placeholders[i].innerHTML = "";
			}
		}

	});
});