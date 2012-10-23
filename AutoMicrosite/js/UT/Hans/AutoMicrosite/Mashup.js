/**
 * Mashup handling class
 * 
 * @author Hans
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
	, "dojo/window", "dojo/on"]
	, function(declare, dom, domConstruct, domStyle, win, on) {
	return declare(null, {

		widgetData: [],
		
		widgetIdPrefix: "widget",

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
		 * Template placeholders for the widgets
		 */
		placeholders: [],
		
		/**
		 * Constructor method
		 *
		 * @param string divMashupId ID of element where hub should be attached
		 */
		constructor: function(divMashupId) {
			// create loader and a hub
			this.loader = new OpenAjax.widget.Loader({ManagedHub: {
				onPublish:			this.onPublish.bind(this),
				onSubscribe:		this.onSubscribe.bind(this),
				onUnsubscribe:		this.onUnsubscribe.bind(this),
				onSecurityAlert:	this.onSecurityAlert.bind(this),
				scope: window
			}});
			this.hub = this.loader.hub;
			this.divMashup = dom.byId(divMashupId);
			this.widgetData = [];
			
			// TODO: find a cross-browser way to do this
			this.placeholders = document.querySelectorAll("[itemtype='http://automicrosite.maesalu.com/TemplatePlaceholder']");
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
		loadWidgets: function(widgets) {
			this.widgetData = widgets;
			
			// reorder in priority order
			this.widgetData.sort(function(a, b) {
				return a.priority - b.priority;
			});
			
			// TODO: remove
			this.loadWidget(100,
				{metadataFile: "data/data.oam.xml", placeholder: null});

			for (var i in this.widgetData) {
				this.loadWidget(i, this.widgetData[i]);
			}
			
			// remove empty placeholders (optional)
			for (var k = 0; k < this.placeholders.length; k++) {
				var removeItem = true;
				for (var j in this.widgetData) {
					if (this.placeholders[k].getAttribute("itemid") == this.widgetData[j].placeholder) {
						removeItem = false;
						break;
					}
				}
				if (removeItem) {
					this.placeholders[k].parentNode.removeChild(this.placeholders[k]);
				}
			}
		},
		
		/**
		 * Load widget into mashup
		 */
		loadWidget: function(index, widget) {
			// create element for widget
			var divWidget = domConstruct.create("div", {
				id: this.widgetIdPrefix +"_"+ index
			}, this.getPlaceholder(widget.placeholder));

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
		},
		
		getPlaceholder: function(placeholder) {
			if (!placeholder) {
				return document.body; // append to end of document if no placeholder
			}
			for (var i in this.placeholders) {
				console.log(this.placeholders[i]);
				if (this.placeholders[i].getAttribute("itemid") == placeholder) {
					this.placeholders[i].innerHTML = '';
					return this.placeholders[i];
				}
			}
			return document.body;
		}
	})
});