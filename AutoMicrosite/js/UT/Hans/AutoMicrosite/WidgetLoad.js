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

		/**
		 * Loaded widgets management objects
		 */
		visualWidgetsLoadedObjects: [],
		dataWidgetsLoadedObjects: [],

		constructor: function(openAjaxLoader, widgetData, placeholders) {
			this.loader = openAjaxLoader;
			this.data = widgetData;
			this.placeholders = placeholders;

			// Reorder widgets in priority order
			this.data.sort(function(a, b) {
				return b.priority - a.priority;
			});
		},

		/**
		 * Start loading widgets
		 */
		load: function(visualDone, allDone) {
			this.visualDoneCallback = visualDone;
			this.allDoneCallback = allDone;

			// TODO: parse placeholder info instead
			this.emptyPlaceholders();

			// Distribute widgets to data and visual
			for (var i in this.data) {
				if (this.data[i].isDataWidget) {
					this.dataWidgets.push(this.data[i]);
				} else {
					this.visualWidgets.push(this.data[i]);
				}
			}

            // TODO: transformer should probably be loaded first, but should still
            //       be in an OpenAjax metadata file
            //this.attachTransformer(); // TODO: remove

            this.loadVisualWidgets();
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

		// TODO: move to a metadata file
		attachTransformer: function() {
			var transformerWidgetUrl = "http://automicrosite.maesalu.com:8833/TransformerWidget.html";
			var tunnelUrl = window.location.href.replace(/\/[^\/]*$/, '') + "/js/tunnel.html";

			var div = domConstruct.create("div", {
				id: "transformerWidget"
			}, document.body);

			new OpenAjax.hub.IframeContainer(this.loader.hub , "transformerWidget",
			  {
				Container: {
				  onSecurityAlert: function() {},
				  onConnect:       function() {},
				  onDisconnect:    function() {}
				},
				IframeContainer: {
				  // DOM element that is parent of this container:
				  parent:      div,
				  // Container's iframe will have these CSS styles:
				  iframeAttrs: { id: "smallHidden" },
				  // Container's iframe loads the following URL:
				  uri: transformerWidgetUrl,
				  // Tunnel URL required by IframeHubClient. This particular tunnel URL
				  // is the one that corresponds to release/all/OpenAjaxManagedHub-all.js:
				  tunnelURI:  tunnelUrl
				}
			  }
			);

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
			}, this.getPlaceholderElement(widget.placeholder));

			// Hide data widgets
			if (widget.isDataWidget) {
				domStyle.set(widget.div, "display", "none");
			} else {
				domStyle.set(widget.div, "overflow", "auto");
			}

			var thisLoader = this.loader;

			thisLoader.hub.subscribe("ee.stacc.transformer.mapping.add.raw", function() {}); // TODO: remove, only needed so 'onPublish' would be called when no subscribers

			// Load widget
			this.loader.create({
				spec:		widget.metadataFile,
				target:		widget.div,
				properties: (widget.properties ? widget.properties : {}),
				onComplete: function(widgetObject) {
					//console.log("Widget loaded: "+ widget.id);

					widgetObject.widgetId2 = widget.id;
					widgetObject.autoMicrositeData = widget;
					callback(widgetObject);

					thisLoader.hub.publish("ee.stacc.transformer.mapping.add.raw",
						widget.mappings);
				},
				onError:	function(error) {
					alert(error);
				}
			});
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
			console.log("WidgetLoad.visualDone");
			//this.buildNavigation();

			if (typeof this.visualDone == "function") {
				this.visualDoneCallback(this.visualWidgets
					, this.dataWidgets);
			}

			this.loadDataWidgets();
		},

		buildNavigation: function() {
			var menuWidget = this.getMenuWidget();
			if (!menuWidget) return;

			// Hide 'separatePage' widgets
			var widget, widgetId, widgetDiv, menuItems;

			for (var i = 0; i < this.visualWidgets.length; i++) {
				if (!this.visualWidgets[i].separatePage) continue;

				widget = this.visualWidgets[i];
				widget.enabled = false;
				widget.div.style.display = "none";

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
		},

		getMenuWidget: function() {
			// TODO: this info should probably come from server side, in case menu is used
			for (var i = 0; i < this.visualWidgetsLoadedObjects.length; i++) {
				if (this.visualWidgetsLoadedObjects[i].divMenu) { // TODO: really-really bad way to find menu
					return this.visualWidgetsLoadedObjects[i];
				}
			}
		},

		menuClick: function(widgetInfo, size) {
			console.log("WidgetLoad.menuClick "+ widgetInfo);
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
		 * All widgets finished loading
		 */
		done: function() {
			console.log("WidgetLoad.done");
			if (typeof this.allDoneCallback == "function") {
				this.allDoneCallback(this.visualWidgetsLoadedObjects
					, this.dataWidgetsLoadedObjects);
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

			return document.body;
		},

		emptyPlaceholders: function() {
			for (var i in this.placeholders) {
				this.placeholders[i].innerHTML = "";
			}
		}

	});
});