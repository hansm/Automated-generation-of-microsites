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
		
		constructor: function(openAjaxLoader, widgetData, placeholders, visualDone, allDone) {
			log("WidgetLoader", "constructor");
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
			log("WidgetLoader", "Start loading visual widgets");
			
			// Reorder in priority order
			/*
			this.data.sort(function(a, b) {
				return a.priority - b.priority;
			});
			*/
			
			// TODO: this should also come from server
			//this.data.push({metadataFile: "data/data.oam.xml?v="+ Math.random(), placeholder: null, orderNumber: 1000});
			
			// Distribute widgets
			var i;
			for (i in this.data) {
				// TODO: server should return whether widget is data widget
				if (this.data[i].placeholder !== null) {
					this.visualWidgets.push(this.data[i].orderNumber);
				} else {
					this.dataWidgets.push(this.data[i].orderNumber);
				}
			}
			log("visualWidgets", this.visualWidgets);
			log("dataWidgets", this.dataWidgets);
			
			// Load visual widgets
			for (i in this.data) {
				if (this.visualWidgets.indexOf(this.data[i].orderNumber) != -1) {
					this.loadVisualWidget(this.data[i]);
				}
			}
		},
		
		loadVisualWidget: function(widget) {
			var callback = (function(widgetId) {
				this.visualWidgetsLoaded.push(widgetId);
				if (this.visualWidgetsLoaded.length == this.visualWidgets.length) {
					this.visualDone();
				}
			}).bind(this, widget.orderNumber);
			this.loadWidget(widget, callback);
		},
		
		loadDataWidget: function(widget) {
			var callback = (function(widgetId) {
				this.dataWidgetsLoaded.push(widgetId);
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
				spec: widget.metadataFile,
				target: divWidget,
				properties: widget.properties ? widget.properties : {},
				onComplete: function(metadata) {
					log("Widget loaded", widgetId);
					callback();
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
				this.visualDoneCallback();
			}
			this.loadDataWidgets();
		},
		
		/**
		 * Load data widgets once visual widgets are done
		 */
		loadDataWidgets: function() {
			log("WidgetLoader", "Start loading data widgets");
			for (i in this.data) {
				if (this.dataWidgets.indexOf(this.data[i].orderNumber) != -1) {
					this.loadDataWidget(this.data[i]);
				}
			}
		},
		
		done: function() {
			if (typeof this.allDoneCallback == "function") {
				this.allDoneCallback();
			}
			log("WidgetLoader", "Done");
		},
		
		getPlaceholder: function(placeholder) {
			if (!placeholder) {
				return document.body; // append to end of document if no placeholder, e.g. data widgets
			}
			for (var i in this.placeholders) {
				if (this.placeholders[i].getAttribute("itemid") == placeholder) {
					this.placeholders[i].innerHTML = "";
					return this.placeholders[i];
				}
			}
			return document.body;
		}
		
	});
});