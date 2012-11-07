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
		 * Widget placeholder elements
		 */
		placeholders: [],
		
		/**
		 * Constructor method
		 *
		 * @param string divMashupId ID of element where hub should be attached
		 */
		constructor: function(widgetData, placeholders) {
			log("Size", "constructor");

			this.data = widgetData;
			this.placeholders = placeholders;
			
			log("Window dimensions", win.getBox());
		},
		
		/**
		 * Run widget resize
		 */
		run: function() {
			//
		}

	});
});