/**
 * Mashup handling class
 * 
 * @author Hans
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
	, "dojo/window", "dojo/on", "UT/Hans/AutoMicrosite/Proxy"]
	, function(declare, dom, domConstruct, domStyle, win, on, proxy) {
	return declare(null, {

		widgetData: [],

		widgetIdPrefix: "",

		divMashup: null,

		/**
		 * Widget loader object
		 */
		loader: null,

		/**
		 * OpenAjax hub object
		 */
		hub: null,

		/**
		 * Widget objects
		 */
		widgets: [],
		
		/**
		 * DOM elements of grid
		 */
		grid: {},

		/**
		 * Constructor method
		 */
		constructor: function(widgets, divMashupId) {
			// create loader and a hub
			this.loader = new OpenAjax.widget.Loader({ManagedHub: {
				onPublish:			proxy(this.onPublish, this),
				onSubscribe:		proxy(this.onSubscribe, this),
				onUnsubscribe:		proxy(this.onUnsubscribe, this),
				onSecurityAlert:	proxy(this.onSecurityAlert, this),
				scope: window
			}});
			this.hub = this.loader.hub;

			// widgets data
			this.widgetData = widgets;
			this.widgetData.sort(function(a, b) {
				return a.priority - b.priority;
			})

			this.divMashup = dom.byId(divMashupId);
			this.widgetIdPrefix = divMashupId + "_widget_";
			this.widgets = [];
			this.grid = {};

			this.buildGrid();
		},

		/**
		 * Build grid for attaching widgets
		 */
		buildGrid: function() {
			domConstruct.empty(this.divMashup);

			var divTopLine = domConstruct.create("div", {
				"class": "line top"
			}, this.divMashup);

			this.grid["left-top"] = domConstruct.create("div", {
				"class": "left"
			}, divTopLine);
			this.grid["center-top"] = domConstruct.create("div", {
				"class": "center"
			}, divTopLine);
			this.grid["right-top"] = domConstruct.create("div", {
				"class": "right"
			}, divTopLine);

			var divMiddleLine = domConstruct.create("div", {
				"class": "line middle"
			}, this.divMashup);

			this.grid["left-center"] = domConstruct.create("div", {
				"class": "left"
			}, divMiddleLine);
			this.grid["center-center"] = domConstruct.create("div", {
				"class": "center"
			}, divMiddleLine);
			this.grid["right-center"] = domConstruct.create("div", {
				"class": "right"
			}, divMiddleLine);

			var divBottomLine = domConstruct.create("div", {
				"class": "line bottom"
			}, this.divMashup);

			this.grid["left-bottom"] = domConstruct.create("div", {
				"class": "left"
			}, divBottomLine);
			this.grid["center-bottom"] = domConstruct.create("div", {
				"class": "center"
			}, divBottomLine);
			this.grid["right-bottom"] = domConstruct.create("div", {
				"class": "right"
			}, divBottomLine);
			
			this.windowResize();
			
			var here = this;
			on(window, "resize", function() {
				here.windowResize();
			});
		},
		
		windowResize: function() {
			console.log("resize event");
			// TODO: this is temporary to make nice colorful grid, remove
			var vp = win.getBox();
			var boxWidth = (vp.w / 3) - 2;
			var boxHeight = (vp.h / 3) - 2;
			for (var i in this.grid) {
				domStyle.set(this.grid[i], {
					border: "1px solid red",
					width: boxWidth +"px",
					height: boxHeight +"px"
				});
			}
		},

		onPublish: function(topic, data, publishContainer, subscribeContainer) {
			return true;
		},

		onSubscribe: function(topic, container) {
			return true;
		},

		onUnsubscribe: function(topic, container) {
			return true;
		},

		/**
		* Security alert from OpenAjax hub
		*/
		onSecurityAlert: function(source, alertType) {
			// TODO: do something about it
		},

		/**
		 * Load all widgets into mashup
		 */
		loadWidgets: function() {
			// TODO: remove
			this.loadWidget(100,
				{metadataFile: "data/data.oam.xml", horizontalPosition: "center", verticalPosition: "top"});


			for (var i in this.widgetData) {
				this.loadWidget(i, this.widgetData[i]);
			}
		},

		/**
		 * Load widget into mashup
		 */
		loadWidget: function(index, widget) {
			// create element for widget
			var divWidget = domConstruct.create("div", {
				id: this.widgetIdPrefix + index
			}, this.grid[widget.horizontalPosition +"-"+ widget.verticalPosition]);

			// load widget
			this.widgets[index] = this.loader.create({
				spec: widget.metadataFile,
				target: divWidget,
				properties: widget.properties ? widget.properties : {},
				onComplete: function(metadata) {
					console.log("Loaded:");
					console.log(metadata);
				},
				onError: function(error) {
					console.log(error);
					alert(error);
				}
			});
		}
	})
});