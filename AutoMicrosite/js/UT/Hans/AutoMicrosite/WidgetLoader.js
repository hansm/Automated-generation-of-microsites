/**
 * Widget loading class
 *
 * @author Hans
 */
// TODO: load visual first and only then add data widgets. so far show "Loading..." message (probably overlay would be a good idea)
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
		, "dojo/window", "dojo/on", "dojo/query"
		, "UT/Hans/AutoMicrosite/Log"]
	, function(declare, dom, domConstruct, domStyle, win, on, query, log) {
	return declare(null, {

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
		 * Event to run when all visual widgets have finished loading
		 */
		visualDoneCallback: null,
		allDoneCallback: null,

		/**
		 * OpenAjax loader
		 */
		loader: null,

		visualWidgets: [],
		visualWidgetsLoaded: [],

		dataWidgets: [],
		dataWidgetsLoaded: [],

		/**
		 * Loaded widgets management objects
		 */
		visualWidgetsLoadedObjects: [],
		dataWidgetsLoadedObjects: [],

		constructor: function(openAjaxLoader, widgetData, placeholders, visualDone, allDone) {
			console.log("WidgetLoader.constructor");
			this.loader = openAjaxLoader;
			this.data = widgetData;
			this.placeholders = placeholders;
			this.visualDoneCallback = visualDone;
			this.allDoneCallback = allDone;
		},

		/**
		 * Start loading widgets
		 */
		load: function() {
			console.log("WidgetLoader.Start loading visual widgets");

			this.emptyPlaceholders();

			// Reorder in priority order
			this.data.sort(function(a, b) {
				return b.priority - a.priority;
			});


			// TODO: this should also come from server
			//this.data.push({metadataFile: "data/data.oam.xml?v="+ Math.random(), placeholder: null, orderNumber: 1000});

			// Distribute widgets
			var i;
			for (i in this.data) {
				// TODO: server should return whether widget is data widget
				if (this.data[i].isDataWidget) {
					this.visualWidgets.push(this.data[i].orderNumber);
				} else {
					this.dataWidgets.push(this.data[i].orderNumber);
				}
			}
			log("visualWidgets", this.visualWidgets);
			log("dataWidgets", this.dataWidgets);

			// Load visual widgets
			if (this.visualWidgets.length == 0) {
				this.visualDone();
				return;
			}
			for (i in this.data) {
				if (this.visualWidgets.indexOf(this.data[i].orderNumber) != -1) {
					this.loadVisualWidget(this.data[i]);
				}
			}
		},

		loadVisualWidget: function(widget) {
			var callback = (function(widgetId, widgetObject) {
				this.visualWidgetsLoaded.push(widgetId);
				this.visualWidgetsLoadedObjects.push(widgetObject);
				if (this.visualWidgetsLoaded.length == this.visualWidgets.length) {
					this.visualDone();
				}
			}).bind(this, widget.orderNumber);
			this.loadWidget(widget, callback);
		},

		loadDataWidget: function(widget) {
			var callback = (function(widgetId, widgetObject) {
				this.dataWidgetsLoaded.push(widgetId);
				this.dataWidgetsLoadedObjects.push(widgetObject);
				if (this.dataWidgetsLoaded.length == this.dataWidgets.length) {
					this.done();
				}
			}).bind(this, widget.orderNumber);
			this.loadWidget(widget, callback);
		},

		loadWidget: function(widget, callback) {
			var widgetId = widget.orderNumber;

			// Create element for widget
			var divWidget = domConstruct.create("div", {
				id: this.WIDGET_ELEMENT_ID_PREFIX +"_"+ widgetId
			}, this.getPlaceholder(widget.placeholder));

			// Load widget
			this.loader.create({
				spec: widget.metadataFile +"?v="+ Math.random(), // TODO: remove random, needed for dev
				target: divWidget,
				properties: widget.properties ? widget.properties : {},
				onComplete: function(widgetObject) {
					log("Widget loaded", widgetId);
					widgetObject.widgetId2 = widgetId;
					callback(widgetObject);
				},
				onError: function(error) {
					console.log(error);
					alert(error);
				}
			});
		},

		/**
		 * All widgets finished loading
		 */
		visualDone: function() {
			log("WidgetLoader", "Finished loading visual widgets");
			if (typeof this.visualDone == "function") {
				this.visualDoneCallback(this.visualWidgetsLoadedObjects
					, this.dataWidgetsLoadedObjects);
			}
			this.loadDataWidgets();
		},

		/**
		 * Load data widgets once visual widgets are done
		 */
		loadDataWidgets: function() {
			log("WidgetLoader", "Start loading data widgets");
			if (this.dataWidgets.length == 0) {
				this.done();
				return;
			}
			for (i in this.data) {
				if (this.dataWidgets.indexOf(this.data[i].orderNumber) != -1) {
					this.loadDataWidget(this.data[i]);
				}
			}
		},

		done: function() {
			if (typeof this.allDoneCallback == "function") {
				this.allDoneCallback(this.visualWidgetsLoadedObjects
					, this.dataWidgetsLoadedObjects);
			}
			log("WidgetLoader", "Done");
		},

		getPlaceholder: function(placeholder) {
			if (!placeholder) {
				return document.body; // append to end of document if no placeholder, e.g. data widgets
			}

			for (var i in this.placeholders) {
				if (this.placeholders[i].getAttribute("itemid") == placeholder) {
					return this.placeholders[i];
				}
			}

			return document.body;
		},

		emptyPlaceholders: function() {
			for (var i in this.placeholders) {
				this.placeholders[i].innerHTML = "";
			}
		}

	});
});