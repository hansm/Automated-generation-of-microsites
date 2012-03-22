/**
 * Mashup handling object
 * 
 * @author Hans
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "UT/Hans/AutoMicrosite/Proxy"], function(declare, dom, domConstruct, proxy) {
	return declare(null, {

		widgetData: [],

		widgetIdPrefix: "",

		divMashup: null,

		loader: null,

		hub: null,

		widgets: [],
		
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

		loadWidgets: function() {
			for (var i in this.widgetData) {
				this.loadWidget(i, this.widgetData[i]);
			}
		},

		loadWidget: function(index, widget) {
			// create element for widget
			var divWidget = domConstruct.create("div", {
				id: this.widgetIdPrefix + index
			}, this.grid[widget.position]);

			// load widget
			this.widgets[index] = this.loader.create({
				spec: widget.file,
				target: divWidget,
				onComplete: function(metadata) {
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