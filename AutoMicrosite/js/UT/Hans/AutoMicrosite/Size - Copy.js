/**
 * Widget resizing
 *
 * @author Hans
 */

define(["dojo/_base/declare", "dojo/dom", "dojo/dom-construct", "dojo/dom-style"
		, "dojo/window", "dojo/on", "UT/Hans/AutoMicrosite/Log"]
	, function(declare, dom, domConstruct, domStyle, win, on, log) {
	return declare(null, {
		
		/**
		 * Widget data
		 */
		data: [],
		
		/**
		 * Widget objects
		 */
		widgets: [],
		
		/**
		 * Widget placeholder elements
		 */
		placeholders: [],
		
		/**
		 * Constructor method
		 *
		 * @param string divMashupId ID of element where hub should be attached
		 */
		constructor: function(widgetData, placeholders, widgets) {
			log("Size", "constructor");

			this.data = widgetData;
			this.placeholders = placeholders;
			this.widgets = widgets;
			
			log("Window dimensions", win.getBox());
		},
		
		/**
		 * Run widget resize
		 */
		run: function() {
			for (var i in this.widgets) {
				// TODO: this.data[i] might not be correct
				this.resizeWidget(this.widgets[i], this.data[i]);
			}
		},
				
		resizeWidget: function(widget, data) {
			// TODO: find dimensions that fit in placeholder and respect widget allowed dimensions
			
			widget.OpenAjax.requestSizeChange({
				width: 100,
				height: 100
			});
		}

	});
});