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
	, function(declare, dom, domConstruct, domStyle, win, on, query, log, NodeListT) {
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
				if (this.data[i].isDataWidget) {
					this.dataWidgets.push(this.data[i]);
				} else {
					this.visualWidgets.push(this.data[i]);
				}
			}
			log("visualWidgets", this.visualWidgets);
			log("dataWidgets", this.dataWidgets);

			// Load visual widgets
			if (this.visualWidgets.length == 0) {
				this.visualDone();
				return;
			}
			for (i in this.visualWidgets) {
				this.loadVisualWidget(this.visualWidgets[i]);
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

		/**
		 * All widgets finished loading
		 */
		visualDone: function() {
			// Hide 'separatePage' widgets
			var widget, widgetId, widgetDiv, menuWidget, menuItems;
			for (var i = 0; i < this.visualWidgets.length; i++) {
				if (this.visualWidgets[i].separatePage) {
					widget = this.visualWidgets[i];
					widgetId = widget.orderNumber; // TODO: use widget.id instead
					widgetDiv = document.getElementById(this.WIDGET_ELEMENT_ID_PREFIX +"_"+ widgetId);
					widgetDiv.style.display = "none";

					menuWidget = this.getMenuWidget();
					if (menuWidget) {
						menuItems = menuWidget.OpenAjax.getPropertyValue("buttons");
						if (!menuItems) {
							menuItems = [];
							menuItems.push({
								label: "Main page",
								href: "back_index"
							});
						}
						menuItems.push({
							label: widget.title ? widget.title : "123",
							href: this.WIDGET_ELEMENT_ID_PREFIX +"_"+ widgetId
						});

						menuWidget.OpenAjax.setPropertyValue("buttons", menuItems);
					}
				}
			}
			
			
			log("WidgetLoader", "Finished loading visual widgets");
			if (typeof this.visualDone == "function") {
				this.visualDoneCallback(this.visualWidgetsLoadedObjects
					, this.dataWidgetsLoadedObjects);
			}
			this.loadDataWidgets();
		},
		
		menuClick: function(widgetId) {
			console.log("loader 123");
			console.log("opening widget "+ widgetId);
			var widgetToShow = document.getElementById(widgetId);
			console.log(widgetToShow);
			var hide = widgetToShow.parentNode.childNodes;
			for (var i = 0; i < hide.length; i++) {
				hide[i].style.display = "none";
			}
			widgetToShow.style.display = "block";
			this.visualDoneCallback(); // TODO: this be bad-bad
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