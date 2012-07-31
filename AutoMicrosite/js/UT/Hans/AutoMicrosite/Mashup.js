/**
 * Mashup handling class
 * 
 * @author Hans
 */
define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
	, "dojo/window", "dojo/on", "UT/Hans/AutoMicrosite/Grid"]
	, function(declare, dom, domConstruct, domStyle, win, on, Grid) {
	return declare(null, {

		widgetData: [],

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
		grid: null,

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
			
			this.grid = new Grid(this.divMashup);
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
			this.setGridDimensions();
			
			// reorder in priority order
			this.widgetData.sort(function(a, b) {
				return a.priority - b.priority;
			});
			
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
				id: this.widgetIdPrefix +"_"+ index
			}, this.grid.getBlock(widget.verticalPosition, widget.horizontalPosition));

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
		
		/**
		 * Update grid dimensions to fit widgets
		 */
		setGridDimensions: function() {
			var dimensions = {};
			var widget;
			for (var i in this.widgetData) {
				widget = this.widgetData[i];
				
				if (!dimensions[widget.verticalPosition]) {
					dimensions[widget.verticalPosition] = {};
				}
				if (!dimensions[widget.verticalPosition][widget.horizontalPosition]) {
					dimensions[widget.verticalPosition][widget.horizontalPosition] = {
						width: 0, height: 0
					}
				}
				
				dimensions[widget.verticalPosition][widget.horizontalPosition].height += widget.height;
				
				if (widget.width > dimensions[widget.verticalPosition][widget.horizontalPosition].width) {
					dimensions[widget.verticalPosition][widget.horizontalPosition].width = widget.width;
				}
			}
			
			for (var v in dimensions) {
				for (var h in dimensions[v]) {
					// TODO: temporary because of publish data button, remove
					if (v == "top") {
						dimensions[v][h].height += 40;
					}
					
					this.grid.setDimensions({vertical: v, horizontal: h}
						, dimensions[v][h].width +"px", dimensions[v][h].height +"px");
				}
			}
		}
	})
});