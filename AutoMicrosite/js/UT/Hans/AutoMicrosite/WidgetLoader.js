/**
 * Widget loading class
 *
 * @author Hans
 */
// TODO: load visual first and only then add data widgets. so far show "Loading..." message (probably overlay would be a good idea)
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
		, "dojo/window", "dojo/on", "dojo/query"
		, "UT/Hans/AutoMicrosite/Log"
		, "dojo/NodeList-traverse"]
	, function(declare, dom, domConstruct, domStyle, win, on, query, log, nodeListTraverse) {
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
			console.log("WidgetLoader.load loading visual widgets");

			// TODO: parse placeholder info instead
			this.emptyPlaceholders();

			// Reorder in priority order
			this.data.sort(function(a, b) {
				return b.priority - a.priority;
			});
			
			// Distribute widgets to data and visual
			for (var i in this.data) {
				if (this.data[i].isDataWidget) {
					this.dataWidgets.push(this.data[i]);
				} else {
					this.visualWidgets.push(this.data[i]);
				}
			}

			// Load visual widgets
			if (this.visualWidgets.length == 0) {
				this.visualDone();
				return;
			}
			for (i in this.visualWidgets) {
				this.loadVisualWidget(this.visualWidgets[i]);
			}
		},

		/**
		 * Load visual widget
		 */
		loadVisualWidget: function(widget) {
			var callback = (function(widgetId, widgetObject) {
				widget.openAjax = widgetObject;
				
				this.visualWidgetsLoaded.push(widgetId);
				
				this.visualWidgetsLoadedObjects.push(widgetObject); // TODO: get rid of this
				
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
				widget.openAjax = widgetObject;
				
				this.dataWidgetsLoaded.push(widgetId);
				this.dataWidgetsLoadedObjects.push(widgetObject); // TODO: get rid of this
				
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
			}, this.getPlaceholder(widget.placeholder));
			
			// Load widget
			this.loader.create({
				spec: widget.metadataFile + (AM_DEBUG ? "?v="+ Math.random() : ""),
				target: widget.div,
				properties: widget.properties ? widget.properties : {},
				onComplete: function(widgetObject) {
					console.log("Widget loaded: "+ widget.id);
					
					widgetObject.widgetId2 = widget.id;
					callback(widgetObject);
					
					// TODO: publish mappings
				},
				onError: function(error) {
					console.log(error);
					alert(error);
				}
			});
			/*
console.log(widget);
			if (widget.separatePage) {
				divWidget.style.display = "none";
				console.log("Menu 1111111111111111111111111");
				
				var menuWidget = this.getMenuWidget();
				console.log(menuWidget);
				if (menuWidget) {
					var menuItems = [];
					menuItems.push({
						label: widget.title ? widget.title : "123",
						href: this.WIDGET_ELEMENT_ID_PREFIX +"_"+ widgetId
					});

					menuWidget.OpenAjax.setPropertyValue("buttons", menuItems);
				}
			}*/
		},
		
		getMenuWidget: function() {
			// TODO: this info should probably come from server side, in case menu is used
			for (var i = 0; i < this.visualWidgetsLoadedObjects.length; i++) {
				if (this.visualWidgetsLoadedObjects[i].divMenu) { // TODO: really-really bad way to find menu
					return this.visualWidgetsLoadedObjects[i];
				}
			}
		},
		
		getWidgetsInPlaceholder: function(placeholder) {
			var widgets = [];
			for (var i = 0; i < this.visualWidgets.length; i++) {
				if (this.visualWidgets[i].placeholder == placeholder) {
					widgets.push(this.visualWidgets[i]);
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
		 * All widgets finished loading
		 */
		visualDone: function() {
			// Hide 'separatePage' widgets
			var widget, widgetId, widgetDiv, menuWidget, menuItems;
			for (var i = 0; i < this.visualWidgets.length; i++) {
				if (this.visualWidgets[i].separatePage) {
					widget = this.visualWidgets[i];
					widget.enabled = false;
					widget.div.style.display = "none";

					menuWidget = this.getMenuWidget();
					if (menuWidget) {
						menuItems = menuWidget.OpenAjax.getPropertyValue("buttons");
						if (!menuItems) {
							menuItems = [];
							menuItems.push({
								label: "Main", // TODO: maybe combine from rest of the widget titles
								href: {widget: null, placeholder: widget.placeholder}
							});
						}
						menuItems.push({
							label: widget.title ? widget.title : "123",
							href: {widget: widget.id, placeholder: widget.placeholder}
						});

						menuWidget.OpenAjax.setPropertyValue("buttons", menuItems);
					}
				}
			}
			
			console.log("WidgetLoader Finished loading visual widgets");
			if (typeof this.visualDone == "function") {
				this.visualDoneCallback(this.visualWidgetsLoadedObjects
					, this.dataWidgetsLoadedObjects);
			}
			this.loadDataWidgets();
		},
		
		menuClick: function(widgetInfo, size) {
			console.log("loader 123");
			console.log("opening widget "+ widgetInfo);
			var widgetId = widgetInfo.widget;
			var placeholderWidgets = this.getWidgetsInPlaceholder(widgetInfo.placeholder);
			this.forEach(placeholderWidgets, function(w) {
				// TODO: instead of w.separatePage use w.firstPage
				if (w.id == widgetId || widgetId == null && w.separatePage == false) {
					w.div.style.display = "block";
					w.enabled = true;
				} else {
					w.div.style.display = "none";
					w.enabled = false;
				}
			});

			size.run();
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
			for (i in this.dataWidgets) {
				this.loadDataWidget(this.dataWidgets[i]);
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