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
			domConstruct.empty(this.divMashup);
			for (var i in this.widgetData) {
				this.loadWidget(i, this.widgetData[i]);
			}
		},

		loadWidget: function(index, widget) {
			// create element for widget
			domConstruct.create("div", {
				id: this.widgetIdPrefix + index
			}, this.divMashup);
console.log(widget.file);
			// load widget
			this.widgets[index] = this.loader.create({
				spec: widget.file,
				target: dom.byId(this.widgetIdPrefix + index),
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